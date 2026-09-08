<?php 
require_once 'admin_header.php'; // This includes the session start, db connection, and sidebar

// --- LIVE DASHBOARD STATS (REAL DATA) ---
$stats = [
    'total_sales' => 'Rs. 0',
    'net_profit' => 'Rs. 0',
    'new_orders' => 0,
    'low_stock_items' => 0
];

// --- FETCH SALES + PROFIT ---
$totalSales = 0;
$totalCost  = 0;
$netProfit  = 0;

$q = $conn->query("
    SELECT 
        SUM(o.order_total) AS total_revenue,
        SUM(
           (SELECT IFNULL(SUM(mir.quantity_used * ii.avg_price_per_unit * oi.quantity), 0)
            FROM order_items oi
            JOIN menu_item_recipes mir ON oi.menu_item_id = mir.menu_item_id
            JOIN inventory_items ii ON mir.inventory_item_id = ii.id
            WHERE oi.order_id = o.id)
        ) AS total_cost
    FROM orders o
    WHERE o.status = 'COMPLETED'
");

if ($q && $row = $q->fetch_assoc()) {
    $totalSales = (float)$row['total_revenue'];
    $totalCost  = (float)$row['total_cost'];
    $netProfit  = $totalSales - $totalCost;
}

$stats['total_sales'] = 'Rs. ' . number_format($totalSales, 2);
$stats['net_profit']  = 'Rs. ' . number_format($netProfit, 2);

// You can add queries here to fetch new_orders and low_stock_items if needed
?>
<title>Flavoro | Dashboard</title>

<main class="main-content">
    <div style="background: linear-gradient(135deg, var(--primary-color), #ffab91); color: white; padding: 28px; border-radius: var(--border-radius); margin-bottom: 30px;">
        <h1>Welcome to Flavoro</h1>
        <p>Your personalized Admin dashboard.</p>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 25px;">
        <div class='card'>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;"><div style="font-size: 22px; color: var(--primary-color); background: #fff0ec; width: 45px; height: 45px; border-radius: 50%; display: flex; justify-content: center; align-items: center;"><i class='fa fa-sack-dollar'></i></div><h3>Total Sales</h3></div>
            <div style="font-size: 26px; font-weight: 600; margin: 8px 0;"><?= $stats['total_sales'] ?></div>
            <p>This month</p>
        </div>
        <div class='card'>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;"><div style="font-size: 22px; color: var(--primary-color); background: #fff0ec; width: 45px; height: 45px; border-radius: 50%; display: flex; justify-content: center; align-items: center;"><i class='fa fa-chart-pie'></i></div><h3>Net Profit</h3></div>
            <div style="font-size: 26px; font-weight: 600; margin: 8px 0;"><?= $stats['net_profit'] ?></div>
            <p>After expenses</p>
        </div>
        <div class='card'>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;"><div style="font-size: 22px; color: var(--primary-color); background: #fff0ec; width: 45px; height: 45px; border-radius: 50%; display: flex; justify-content: center; align-items: center;"><i class='fa fa-receipt'></i></div><h3>New Orders</h3></div>
            <div style="font-size: 26px; font-weight: 600; margin: 8px 0;"><?= $stats['new_orders'] ?></div>
            <p>Waiting for processing</p>
        </div>
        <div class='card'>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 12px;"><div style="font-size: 22px; color: var(--primary-color); background: #fff0ec; width: 45px; height: 45px; border-radius: 50%; display: flex; justify-content: center; align-items: center;"><i class='fa fa-triangle-exclamation'></i></div><h3>Low Stock</h3></div>
            <div style="font-size: 26px; font-weight: 600; margin: 8px 0;"><?= $stats['low_stock_items'] ?></div>
            <p>Need reordering soon</p>
        </div>
        <div class='card' style="grid-column: 1 / -1;">
            <h3>Weekly Sales Trend</h3>
            <canvas id='salesChart'></canvas>
        </div>
    </div>
</main>

<?php require_once 'admin_footer.php'; ?>