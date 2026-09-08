<?php
session_start();
require_once 'db.php';

if ($_SESSION['role'] !== 'Customer') {
    die("Only customers can place orders");
}

$userId = $_SESSION['user_id'];
$item_name = $_POST['item_name'];
$price = (float)$_POST['price'];
$quantity = (int)$_POST['quantity'];
$location = $_POST['location'];
$phone = $_POST['phone'];
$total = $price * $quantity;

// Insert order
$stmt = $conn->prepare("INSERT INTO orders (user_id, order_total, status) VALUES (?, ?, 'PENDING')");
$stmt->bind_param("id", $userId, $total);
$stmt->execute();
$orderId = $stmt->insert_id;

// Save order details
$stmt = $conn->prepare("INSERT INTO order_items (order_id, menu_item_id, quantity, unit_price) VALUES (?, 0, ?, ?)");
$stmt->bind_param("iid", $orderId, $quantity, $price);
$stmt->execute();

header("Location: my_orders.php?success=1");
exit();
?>
