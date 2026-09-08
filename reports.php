<?php 
require_once 'admin_header.php'; // Includes session, db, and sidebar
require_once 'report_model.php';

$sales_summary = get_sales_summary();
$daily_sales   = get_daily_sales();
$monthly_sales = get_monthly_sales();
$low_stock     = get_low_stock();
?>
<title>Business Reports</title>

<main class="main-content">
    <h1>Business Intelligence Reports</h1>

    <div class="card">
        <h2>Sales Summary</h2>
        <div style="display: flex; gap: 40px;">
            <div>
                <p style="font-size: 1.1rem; color: #555;">Total Orders:</p>
                <strong style="font-size: 1.8rem; color: var(--secondary-color);"><?= number_format($sales_summary['total_orders']) ?></strong>
            </div>
            <div>
                <p style="font-size: 1.1rem; color: #555;">Total Revenue:</p>
                <strong style="font-size: 1.8rem; color: var(--secondary-color);">Rs. <?= number_format($sales_summary['total_revenue'], 2) ?></strong>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Daily Sales (Last 30 Days)</h2>
        <table>
            <thead><tr><th>Date</th><th>Orders</th><th>Revenue (Rs.)</th></tr></thead>
            <tbody>
                <?php foreach ($daily_sales as $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['day']) ?></td>
                        <td><?= $row['orders'] ?></td>
                        <td><?= number_format($row['revenue'], 2) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Monthly Sales (Last 12 Months)</h2>
        <table>
            <thead><tr><th>Month</th><th>Orders</th><th>Revenue (Rs.)</th></tr></thead>
            <tbody>
                <?php foreach ($monthly_sales as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['month']) ?></td>
                    <td><?= $row['orders'] ?></td>
                    <td><?= number_format($row['revenue'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Low Stock Items</h2>
        <table>
            <thead><tr><th>Item</th><th>Stock</th><th>Min Level</th><th>Unit</th></tr></thead>
            <tbody>
                <?php if (empty($low_stock)): ?>
                    <tr><td colspan="4" style="text-align: center; color: green;">No low stock items!</td></tr>
                <?php else: ?>
                    <?php foreach ($low_stock as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td style="color: red; font-weight: bold;"><?= $item['current_stock'] ?></td>
                        <td><?= $item['min_level'] ?></td>
                        <td><?= htmlspecialchars($item['unit']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once 'admin_footer.php'; ?>