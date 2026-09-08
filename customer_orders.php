<?php 
require_once 'admin_header.php'; // Includes session, db, and sidebar
require_once 'inventory_model.php'; // For the restock function

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $orderId = (int)$_POST['order_id'];
    $status = $_POST['status'];

    $conn->begin_transaction();
    try {
        $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $orderId);
        $stmt->execute();
        $stmt->close();

        if ($status === 'CANCELLED') {
            restock_ingredients_for_cancelled_order($orderId);
        }
        $conn->commit();
    } catch (Exception $e) {
        $conn->rollback();
        // You can add an error message here if you want
    }
}

// Fetch orders with joined data
$query = "
    SELECT
        o.id AS order_id,
        COALESCE(u.username, o.customer_name) AS customer_name,
        COALESCE(u.phone, o.phone) AS phone,
        COALESCE(u.address, o.location) AS location,
        o.order_total,
        o.status,
        o.created_at,
        GROUP_CONCAT(CONCAT(m.name, ' (x', oi.quantity, ')') SEPARATOR '<br>') AS items
    FROM orders o
    LEFT JOIN user_details u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN menu_items m ON oi.menu_item_id = m.id
    GROUP BY o.id
    ORDER BY o.created_at DESC
";
$result = $conn->query($query);
?>
<title>Customer Orders - Admin</title>

<main class="main-content">
    <h1>Customer Orders</h1>
    <div class="card">
        <div style="overflow-x: auto;">
            <table>
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Contact</th>
                        <th>Items</th>
                        <th>Status</th>
                        <th>Total (Rs.)</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['customer_name'] ?: '—') ?></td>
                        <td>
                            <?= htmlspecialchars($row['phone'] ?: '—') ?><br>
                            <small><?= htmlspecialchars($row['location'] ?: '—') ?></small>
                        </td>
                        <td><?= $row['items'] ?: '—' ?></td>
                        <td>
                            <?php
                            $status = $row['status'];
                            $badgeColor = '#6c757d'; // grey
                            if ($status === 'PENDING') $badgeColor = '#ffc107'; // yellow
                            elseif ($status === 'CONFIRMED') $badgeColor = '#17a2b8'; // teal
                            elseif ($status === 'OUT_FOR_DELIVERY') $badgeColor = '#007bff'; // blue
                            elseif ($status === 'COMPLETED') $badgeColor = '#28a745'; // green
                            elseif ($status === 'CANCELLED') $badgeColor = '#dc3545'; // red
                            ?>
                            <span style="background-color: <?= $badgeColor ?>; color: white; padding: 4px 8px; border-radius: 10px; font-size: 12px;"><?= htmlspecialchars($status) ?></span>
                        </td>
                        <td><strong><?= number_format((float)$row['order_total'], 2) ?></strong></td>
                        <td><?= htmlspecialchars($row['created_at']) ?></td>
                        <td>
                            <?php if ($row['status'] !== 'CANCELLED' && $row['status'] !== 'COMPLETED'): ?>
                                <form method="POST" style="display: flex; flex-direction: column; gap: 5px;">
                                    <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                    <button name="status" value="CONFIRMED" type="submit" class="btn btn-sm btn-info">Received</button>
                                    <button name="status" value="OUT_FOR_DELIVERY" type="submit" class="btn btn-sm btn-primary">Out for Delivery</button>
                                    <button name="status" value="COMPLETED" type="submit" class="btn btn-sm btn-success">Delivered</button>
                                    <button name="status" value="CANCELLED" type="submit" class="btn btn-sm btn-danger">Cancel</button>
                                </form>
                            <?php else: ?>
                                <span>—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once 'admin_footer.php'; ?>