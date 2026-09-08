<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Customer') {
    header("Location: login.php");
    exit();
}

require_once 'db.php';

$username = $_SESSION['username'];
$role = $_SESSION['role'];
$gender = $_SESSION['gender'] ?? "Male";
$avatar = ($gender === "Female") ? "assets/img/female.png" : "assets/img/male.png";

function is_active($page_name) {
    return basename($_SERVER['PHP_SELF']) == $page_name ? "active" : "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
    body {
        margin: 0;
        font-family: 'Poppins', sans-serif;
        display: flex;
        height: 100vh;
        background-color: #f8f9fa;
    }
    .sidebar {
        width: 250px;
        background: #2c2f48;
        color: #eee;
        display: flex;
        flex-direction: column;
    }
    .sidebar-header {
        padding: 25px;
        text-align: center;
        border-bottom: 1px solid #444;
    }
    .sidebar-header h2 {
        color: #ff7043;
        margin: 0;
    }
    .sidebar-nav {
        list-style: none;
        padding: 0;
        margin: 0;
        flex-grow: 1;
    }
    .sidebar-nav li a {
        display: flex;
        align-items: center;
        padding: 14px 25px;
        color: #eee;
        text-decoration: none;
        transition: background 0.3s;
    }
    .sidebar-nav li a.active, .sidebar-nav li a:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }
    .sidebar-nav li a i {
        margin-right: 10px;
    }
    .container {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
    }
    header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 30px;
        background: #fff;
        box-shadow: 0 3px 8px rgba(0,0,0,0.05);
    }
    .main-content {
        flex-grow: 1;
        overflow-y: auto;
        padding: 30px;
    }
    .user-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .user-info img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 2px solid #ff7043;
    }
</style>
</head>
<body>
<aside class="sidebar">
    <div class="sidebar-header">
        <h2><i class="fa fa-utensils"></i> Flavoro</h2>
    </div>
    <ul class="sidebar-nav">
        <li><a href="customer_home.php" class="<?= is_active('customer_home.php') ?>"><i class="fa fa-home"></i> Home</a></li>
        <li><a href="order_now.php" class="<?= is_active('order_now.php') ?>"><i class="fa fa-bowl-food"></i> Order Now</a></li>
        <li><a href="my_orders.php" class="<?= is_active('my_orders.php') ?>"><i class="fa fa-receipt"></i> My Orders</a></li>
        <li><a href="profile.php" class="<?= is_active('profile.php') ?>"><i class="fa fa-user"></i> Profile</a></li>
        <li><a href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a></li>
    </ul>
</aside>

<div class="container">
<header>
    <h3>Welcome, <?= htmlspecialchars($username) ?></h3>
    <div class="user-info">
        <img src="<?= $avatar ?>" alt="Avatar">
        <span><?= htmlspecialchars($role) ?></span>
    </div>
</header>
<main class="main-content">
