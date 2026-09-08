<?php
require_once 'customer_header.php';
require_once 'db.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch all orders for the logged-in customer
$query = "
    SELECT 
        o.id AS order_id,
        o.order_total,
        o.status,
        o.created_at,
        GROUP_CONCAT(CONCAT(m.name, ' (x', oi.quantity, ')') SEPARATOR '<br>') AS items
    FROM orders o
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN menu_items m ON oi.menu_item_id = m.id
    WHERE o.user_id = ?
    GROUP BY o.id
    ORDER BY o.created_at DESC
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<title>My Orders</title>

<h1 style="font-size: 28px; color: #2c2f48; margin-bottom: 25px;">My Orders</h1>

<div class="card" style="background: #fff; border-radius: 14px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); padding: 25px; overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background: #f8f9fa; text-align: left;">
                <th style="padding: 12px;">Order ID</th>
                <th style="padding: 12px;">Items</th>
                <th style="padding: 12px;">Total (Rs.)</th>
                <th style="padding: 12px;">Status</th>
                <th style="padding: 12px;">Date</th>
                <th style="padding: 12px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <?php
                        $status = $row['status'];
                        $badgeColor = '#6c757d';
                        if ($status === 'PENDING') $badgeColor = '#ffc107';
                        elseif ($status === 'CONFIRMED') $badgeColor = '#17a2b8';
                        elseif ($status === 'OUT_FOR_DELIVERY') $badgeColor = '#007bff';
                        elseif ($status === 'COMPLETED') $badgeColor = '#28a745';
                        elseif ($status === 'CANCELLED') $badgeColor = '#dc3545';
                    ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 12px;">#<?= htmlspecialchars($row['order_id']) ?></td>
                        <td style="padding: 12px;"><?= $row['items'] ?: '—' ?></td>
                        <td style="padding: 12px;"><strong><?= number_format($row['order_total'], 2) ?></strong></td>
                        <td style="padding: 12px;">
                            <span style="background-color: <?= $badgeColor ?>; color: white; padding: 4px 10px; border-radius: 10px; font-size: 13px;"><?= htmlspecialchars($status) ?></span>
                        </td>
                        <td style="padding: 12px;"><?= htmlspecialchars($row['created_at']) ?></td>
                        <td style="padding: 12px;">
                            <?php if ($status === 'PENDING'): ?>
                                <form action="cancel_order.php" method="POST" style="display:inline;">
                                    <input type="hidden" name="order_id" value="<?= $row['order_id'] ?>">
                                    <button type="submit" class="btn" style="background:#dc3545; border:none; color:white; padding:6px 12px; border-radius:6px; cursor:pointer;">Cancel</button>
                                </form>
                            <?php else: ?>
                                <span style="color: #999;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" style="text-align:center; padding: 20px; color: #888;">No orders found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'customer_footer.php'; ?>
