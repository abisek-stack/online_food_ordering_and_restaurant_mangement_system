<?php
// Ensure db.php is included where this model is used.
/**
 * Fetches all inventory items with their stock status.
 * @return array
 */
function get_inventory_status(): array {
    global $conn;
    $stmt = $conn->prepare("SELECT id, name, unit, current_stock, min_level, max_level, avg_price_per_unit FROM inventory_items ORDER BY name");
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_all(MYSQLI_ASSOC);
}

/**
 * Adds stock to an inventory item and logs the transaction.
 * @throws Exception if quantity exceeds max_level
 */
function add_stock(int $itemId, float $quantity, float $purchasePrice): bool {
    global $conn;
    if ($quantity <= 0) {
        throw new Exception("Quantity must be positive.");
    }

    $conn->begin_transaction();
    try {
        // Get current item details and lock the row for update
        $stmt = $conn->prepare("SELECT current_stock, max_level, avg_price_per_unit FROM inventory_items WHERE id = ? FOR UPDATE");
        $stmt->bind_param("i", $itemId);
        $stmt->execute();
        $item = $stmt->get_result()->fetch_assoc();

        if (!$item) {
            throw new Exception("Inventory item not found.");
        }

        // Check against max stock level
        if (($item['current_stock'] + $quantity) > $item['max_level']) {
            throw new Exception("Cannot add stock. It would exceed the maximum level of {$item['max_level']}.");
        }
        
        // Calculate new average price
        $oldTotalValue = $item['current_stock'] * $item['avg_price_per_unit'];
        $newStockValue = $quantity * $purchasePrice;
        $newTotalStock = $item['current_stock'] + $quantity;
        $newAvgPrice = ($oldTotalValue + $newStockValue) / $newTotalStock;
        // Update inventory item
        $stmt = $conn->prepare("UPDATE inventory_items SET current_stock = current_stock + ?, avg_price_per_unit = ? WHERE id = ?");
        $stmt->bind_param("ddi", $quantity, $newAvgPrice, $itemId);
        $stmt->execute();

        // Log transaction
        $stmt = $conn->prepare("INSERT INTO inventory_transactions (inventory_item_id, quantity_changed, transaction_type, notes) VALUES (?, ?, 'purchase', ?)");
        $note = "Purchased at price: " . $purchasePrice;
        $stmt->bind_param("ids", $itemId, $quantity, $note);
        $stmt->execute();

        $conn->commit();
        return true;
    } catch (Throwable $e) {
        $conn->rollback();
        throw $e;
    }
}

/**
 * Records wasted items, deducts from stock, and logs it.
 */
function record_waste(int $itemId, float $quantity, string $notes): bool {
    global $conn;
    if ($quantity <= 0) {
        throw new Exception("Waste quantity must be positive.");
    }
    
    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE inventory_items SET current_stock = current_stock - ? WHERE id = ?");
        $stmt->bind_param("di", $quantity, $itemId);
        $stmt->execute();
        
        $logQty = -$quantity;
        $stmt = $conn->prepare("INSERT INTO inventory_transactions (inventory_item_id, quantity_changed, transaction_type, notes) VALUES (?, ?, 'waste', ?)");
        $stmt->bind_param("ids", $itemId, $logQty, $notes);
        $stmt->execute();

        $conn->commit();
        return true;
    } catch (Throwable $e) {
        $conn->rollback();
        throw $e;
    }
}

/**
 * The core auto-deduction function.
 * @throws Exception if not enough stock is available.
 */
function deduct_ingredients_for_order(int $orderId, array $cart): void {
    global $conn;
    // Prepare statements outside the loop for efficiency
    $getRecipe = $conn->prepare("SELECT ii.id, ii.name, ii.current_stock, mir.quantity_used FROM menu_item_recipes mir JOIN inventory_items ii ON mir.inventory_item_id = ii.id WHERE mir.menu_item_id = ?");
    $updateStock = $conn->prepare("UPDATE inventory_items SET current_stock = current_stock - ? WHERE id = ?");
    $logTransaction = $conn->prepare("INSERT INTO inventory_transactions (inventory_item_id, quantity_changed, transaction_type, related_order_id) VALUES (?, ?, 'sale', ?)");
    foreach ($cart as $menuItemId => $qty) {
        $getRecipe->bind_param("i", $menuItemId);
        $getRecipe->execute();
        $recipeItems = $getRecipe->get_result()->fetch_all(MYSQLI_ASSOC);
        // First, check if all ingredients are available for this menu item
        foreach ($recipeItems as $ingredient) {
            $totalNeeded = $ingredient['quantity_used'] * $qty;
            if ($ingredient['current_stock'] < $totalNeeded) {
                throw new Exception("Not enough stock for ingredient '{$ingredient['name']}' to fulfill order. Required: {$totalNeeded}, Available: {$ingredient['current_stock']}.");
            }
        }
        
        // If checks pass, deduct all ingredients
        foreach ($recipeItems as $ingredient) {
            $totalToDeduct = $ingredient['quantity_used'] * $qty;
            // Deduct from stock
            $updateStock->bind_param("di", $totalToDeduct, $ingredient['id']);
            $updateStock->execute();
            // Log the transaction
            $logQty = -$totalToDeduct;
            $logTransaction->bind_param("idi", $ingredient['id'], $logQty, $orderId);
            $logTransaction->execute();
        }
    }
}


/**
 * NEW FUNCTION: Re-adds ingredients to stock for a cancelled order.
 * @param int $orderId The ID of the order being cancelled.
 */
function restock_ingredients_for_cancelled_order(int $orderId): void {
    global $conn;

    // Get all items and quantities for the given order
    $getOrderItems = $conn->prepare("SELECT menu_item_id, quantity FROM order_items WHERE order_id = ?");
    $getOrderItems->bind_param("i", $orderId);
    $getOrderItems->execute();
    $orderItems = $getOrderItems->get_result()->fetch_all(MYSQLI_ASSOC);

    if (empty($orderItems)) {
        return; // No items in this order to restock
    }

    // Prepare statements for efficiency
    $getRecipe = $conn->prepare("SELECT inventory_item_id, quantity_used FROM menu_item_recipes WHERE menu_item_id = ?");
    $updateStock = $conn->prepare("UPDATE inventory_items SET current_stock = current_stock + ? WHERE id = ?");
    $logTransaction = $conn->prepare("INSERT INTO inventory_transactions (inventory_item_id, quantity_changed, transaction_type, related_order_id, notes) VALUES (?, ?, 'cancellation_restock', ?, ?)");

    foreach ($orderItems as $orderItem) {
        $menuItemId = $orderItem['menu_item_id'];
        $orderQty = $orderItem['quantity'];

        // Find the recipe for this menu item
        $getRecipe->bind_param("i", $menuItemId);
        $getRecipe->execute();
        $recipeItems = $getRecipe->get_result()->fetch_all(MYSQLI_ASSOC);

        // For each ingredient in the recipe, add it back to stock
        foreach ($recipeItems as $ingredient) {
            $totalToRestock = $ingredient['quantity_used'] * $orderQty;

            // Update inventory_items table
            $updateStock->bind_param("di", $totalToRestock, $ingredient['inventory_item_id']);
            $updateStock->execute();

            // Log the restock transaction
            $note = "Restocked from cancelled order #" . $orderId;
            $logTransaction->bind_param("idis", $ingredient['inventory_item_id'], $totalToRestock, $orderId, $note);
            $logTransaction->execute();
        }
    }
}