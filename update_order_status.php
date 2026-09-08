<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['status']; // CONFIRMED, OUT_FOR_DELIVERY, COMPLETED

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $newStatus, $orderId);
    $stmt->execute();
}

header("Location: customer_orders.php");
exit();
?>
