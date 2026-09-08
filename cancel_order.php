<?php
session_start();
require_once 'db.php';
require_once 'inventory_model.php'; // We need this for the new restock function

if ($_SESSION['role'] !== 'Customer') {
    die("Access Denied");
}

if (isset($_POST['order_id'])) {
    $orderId = (int)$_POST['order_id'];
    $userId = $_SESSION['user_id'];

    // Use a transaction to ensure both status update and restock succeed or fail together
    $conn->begin_transaction();
    try {
        // Step 1: Only cancel if the order is still 'PENDING'
        $stmt = $conn->prepare("UPDATE orders SET status='CANCELLED' WHERE id=? AND user_id=? AND status='PENDING'");
        $stmt->bind_param("ii", $orderId, $userId);
        $stmt->execute();
        
        // Check if a row was actually updated
        if ($stmt->affected_rows > 0) {
            // Step 2: If the order was successfully cancelled, restock the ingredients
            restock_ingredients_for_cancelled_order($orderId);
        }

        // If everything is successful, commit the changes
        $conn->commit();

    } catch (Exception $e) {
        // If any step fails, roll back all changes
        $conn->rollback();
        // Optional: log the error or show a user-friendly error message
        die("An error occurred while cancelling the order. Please try again. Error: " . $e->getMessage());
    }
}

header("Location: my_orders.php");
exit();
?>