<?php
require_once 'db.php';
require_once 'inventory_model.php'; // gives $conn (mysqli)

function getMenuItems(): array {
    global $conn;
    $stmt = $conn->prepare("SELECT id, name, price FROM menu_items WHERE is_active=1 ORDER BY name");
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_all(MYSQLI_ASSOC);
}

/**
 * $cart is an assoc array: [menu_item_id => qty, ...]
 * Returns newly created $orderId
 */
function createOrder(int $userId, array $cart): int {
    global $conn;
    if (empty($cart)) throw new Exception("Cart is empty.");

    $conn->begin_transaction();
    try {
        $orderTotal = 0.0;

        // 1) create order shell with total 0
        $stmt = $conn->prepare("INSERT INTO orders (user_id, order_total, status) VALUES (?, 0, 'PENDING')");
        $stmt->bind_param("i", $userId);
        $stmt->execute();
        $orderId = $stmt->insert_id;

        // 2) insert each line after fetching current price
        $getPrice = $conn->prepare("SELECT price FROM menu_items WHERE id=? AND is_active=1");
        $insLine  = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price) VALUES (?,?,?,?)");

        foreach ($cart as $itemId => $qty) {
            $itemId = (int)$itemId;
            $qty    = (int)$qty;
            if ($qty <= 0) continue;

            $getPrice->bind_param("i", $itemId);
            $getPrice->execute();
            $res = $getPrice->get_result();
            if (!$row = $res->fetch_assoc()) {
                throw new Exception("Menu item $itemId not found or inactive.");
            }

            $price = (float)$row['price'];
            $line  = $price * $qty;
            $orderTotal += $line;

            $insLine->bind_param("iiid", $orderId, $itemId, $qty, $price);
            $insLine->execute();
        }

        if ($orderTotal <= 0) {
            throw new Exception("No valid items in cart.");
        }

        // 3) update header total
        $upd = $conn->prepare("UPDATE orders SET order_total=? WHERE id=?");
        $upd->bind_param("di", $orderTotal, $orderId);
        $upd->execute();
         // --- NEW LINE TO ADD ---
        // 4) Deduct ingredients from inventory
        deduct_ingredients_for_order($orderId, $cart);
        // --- END OF NEW LINE ---

        $conn->commit();
        return $orderId;
    } catch (Throwable $e) {
        $conn->rollback();
        throw $e;
    }
}

function getOrdersByUser(int $userId): array {
    global $conn;
    $stmt = $conn->prepare(
        "SELECT id, order_total, status, created_at
         FROM orders
         WHERE user_id=?
         ORDER BY id DESC"
    );
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_all(MYSQLI_ASSOC);
}

function getOrderWithItems(int $orderId): array {
    global $conn;
    $stmt = $conn->prepare(
        "SELECT o.id, o.user_id, o.order_total, o.status, o.created_at,
                oi.menu_item_id, oi.quantity, oi.unit_price, m.name
         FROM orders o
         JOIN order_items oi ON oi.order_id = o.id
         JOIN menu_items  m  ON m.id       = oi.menu_item_id
         WHERE o.id=?"
    );
    $stmt->bind_param("i", $orderId);
    $stmt->execute();
    $res = $stmt->get_result();

    $order = null; $items = [];
    while ($row = $res->fetch_assoc()) {
        if (!$order) {
            $order = [
                'id'         => $row['id'],
                'user_id'    => $row['user_id'],
                'order_total'=> $row['order_total'],
                'status'     => $row['status'],
                'created_at' => $row['created_at'],
                'items'      => []
            ];
        }
        $items[] = [
            'menu_item_id'=> $row['menu_item_id'],
            'name'        => $row['name'],
            'quantity'    => $row['quantity'],
            'unit_price'  => $row['unit_price']
        ];
    }
    if ($order) $order['items'] = $items;
    return $order ?? [];
}
