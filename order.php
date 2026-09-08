<?php
require_once 'customer_header.php';
?>

<h1 style="font-size: 28px; color: #2c2f48;">Welcome to Flavoro!</h1>
<p style="margin-bottom: 25px;">Enjoy delicious meals, place new orders, and track your past ones — all in one place.</p>

<div style="display: flex; flex-wrap: wrap; gap: 20px;">
    <div style="flex: 1; min-width: 250px; background: #fff; border-radius: 14px; padding: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); text-align: center;">
        <i class="fa fa-bowl-food" style="font-size: 32px; color: #ff7043; margin-bottom: 10px;"></i>
        <h3>Order Now</h3>
        <p>Hungry? Browse our menu and order your favorite dishes instantly.</p>
        <a href="order_now.php" class="btn" style="background: #ff7043; color: white; text-decoration: none; padding: 10px 18px; border-radius: 6px;">Start Ordering</a>
    </div>

    <div style="flex: 1; min-width: 250px; background: #fff; border-radius: 14px; padding: 20px; box-shadow: 0 8px 25px rgba(0,0,0,0.08); text-align: center;">
        <i class="fa fa-receipt" style="font-size: 32px; color: #ff7043; margin-bottom: 10px;"></i>
        <h3>My Orders</h3>
        <p>View your recent and past orders, including their current status.</p>
        <a href="my_orders.php" class="btn" style="background: #17a2b8; color: white; text-decoration: none; padding: 10px 18px; border-radius: 6px;">View Orders</a>
    </div>
</div>

<?php require_once 'customer_footer.php'; ?>
