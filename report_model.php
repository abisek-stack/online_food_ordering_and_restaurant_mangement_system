<?php
require_once 'db.php';

/** Get sales summary (total revenue + total orders) */
function get_sales_summary(): array {
    global $conn;
    $sql = "SELECT COUNT(id) AS total_orders, SUM(order_total) AS total_revenue FROM orders WHERE status='COMPLETED'";
    $res = $conn->query($sql);
    return $res->fetch_assoc() ?: ['total_orders' => 0, 'total_revenue' => 0];
}

/** Get daily sales */
function get_daily_sales(): array {
    global $conn;
    $sql = "SELECT DATE(created_at) AS day, SUM(order_total) AS revenue, COUNT(id) AS orders
            FROM orders
            WHERE status='COMPLETED'
            GROUP BY DATE(created_at)
            ORDER BY day DESC
            LIMIT 30";
    $res = $conn->query($sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}

/** Get monthly sales */
function get_monthly_sales(): array {
    global $conn;
    $sql = "SELECT DATE_FORMAT(created_at, '%Y-%m') AS month, SUM(order_total) AS revenue, COUNT(id) AS orders
            FROM orders
            WHERE status='COMPLETED'
            GROUP BY month
            ORDER BY month DESC
            LIMIT 12";
    $res = $conn->query($sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}

/** Get low stock items */
function get_low_stock(): array {
    global $conn;
    $sql = "SELECT name, current_stock, min_level, unit 
            FROM inventory_items 
            WHERE current_stock <= min_level
            ORDER BY current_stock ASC";
    $res = $conn->query($sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}
