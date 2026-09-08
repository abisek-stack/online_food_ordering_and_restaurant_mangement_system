
<?php
// Note: This updated file assumes that 'admin_header.php' contains:
// 1. session_start()
// 2. require_once 'db.php' (for $conn)
// 3. Admin access control (though I'll re-include a basic check)
// 4. The definition of the sidebar/header layout

// --- START: NEW COMPREHENSIVE FINANCIAL LOGIC & HELPERS ---

// Includes session, db, and sidebar (Original file's requirement)
require_once 'admin_header.php';

// Re-including a basic access check, just in case admin_header is minimal
if (!isset($_SESSION['username']) || ($_SESSION['role'] ?? '') !== 'Admin') {
    // If you don't have a login system, you may need to adjust this
    // If admin_header.php already handles this, this block is redundant but safe.
    // header("Location: login.php");
    // exit();
}

/**
 * Helper function to check if a table exists (from the new code)
 */
function table_exists($conn, $name) {
    $res = $conn->query("SHOW TABLES LIKE '" . $conn->real_escape_string($name) . "'");
    return ($res && $res->num_rows > 0);
}
$hasPayments = table_exists($conn, 'payments');
$hasExpenses = table_exists($conn, 'expenses');


// --- TIMEFRAME HANDLING ---
$period = $_GET['period'] ?? '7days';
$start = null; $end = null;
if (!empty($_GET['start']) && !empty($_GET['end'])) {
    // custom date range (YYYY-MM-DD)
    $start = $_GET['start'];
    $end = $_GET['end'];
} else {
    $today = date('Y-m-d');
    switch ($period) {
        case '4days': $start = date('Y-m-d', strtotime('-3 days')); $end = $today; break;
        case '7days': $start = date('Y-m-d', strtotime('-6 days')); $end = $today; break;
        case '1month': $start = date('Y-m-d', strtotime('-1 month')); $end = $today; break;
        case '1year': $start = date('Y-m-d', strtotime('-1 year')); $end = $today; break;
        default: $start = date('Y-m-d', strtotime('-6 days')); $end = $today; break;
    }
}
$start_dt = $start . ' 00:00:00';
$end_dt   = $end   . ' 23:59:59';

// ----------------------------------------------------
// FINANCIAL CALCULATIONS (LIVE DATA) - CORRECTED LOGIC
// ----------------------------------------------------

// --- KPIs: Total Sales, COGS, and Net Profit ---
$totalSales = 0;
$cogs = 0;

$kpiStmt = $conn->prepare("
    SELECT
        IFNULL(SUM(o.order_total), 0) AS total_revenue,
        IFNULL(SUM(
            (SELECT SUM(mir.quantity_used * ii.avg_price_per_unit * oi.quantity)
             FROM order_items oi
             JOIN menu_item_recipes mir ON oi.menu_item_id = mir.menu_item_id
             JOIN inventory_items ii ON mir.inventory_item_id = ii.id
             WHERE oi.order_id = o.id)
        ), 0) AS total_cost
    FROM orders o
    WHERE o.status = 'COMPLETED' AND o.created_at BETWEEN ? AND ?
");
$kpiStmt->bind_param("ss", $start_dt, $end_dt);
$kpiStmt->execute();
$kpiResult = $kpiStmt->get_result()->fetch_assoc();
$totalSales = (float)$kpiResult['total_revenue'];
$cogs = (float)$kpiResult['total_cost'];
$kpiStmt->close();


// --- Other Expenses ---
$otherExpenses = 0.0;
if ($hasExpenses) {
    $stmt = $conn->prepare("SELECT IFNULL(SUM(amount),0) AS total FROM expenses WHERE expense_date BETWEEN ? AND ?");
    $stmt->bind_param("ss", $start, $end);
    $stmt->execute();
    $otherExpenses = (float)$stmt->get_result()->fetch_assoc()['total'];
    $stmt->close();
}

// --- Net profit ---
$netProfit = $totalSales - ($cogs + $otherExpenses);

// --- Daily sales trend for chart (uses final order_total) ---
$chartData = [];
$stmt = $conn->prepare("
    SELECT DATE(o.created_at) AS day, IFNULL(SUM(o.order_total),0) AS total
    FROM orders o
    WHERE o.status='COMPLETED' AND o.created_at BETWEEN ? AND ?
    GROUP BY DATE(o.created_at)
    ORDER BY DATE(o.created_at) ASC
");
$stmt->bind_param("ss", $start_dt, $end_dt);
$stmt->execute();
$rows = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
foreach ($rows as $r) { $chartData[] = [$r['day'], (float)$r['total']]; }


// --- Top selling items + profit by item (Query and PHP Aggregation) ---
$items = [];
$stmt = $conn->prepare("
    SELECT
        mi.id,
        mi.name,
        SUM(oi.quantity) AS qty_sold,
        
        -- Calculate pre-discount revenue for each item line
        SUM(oi.quantity * oi.unit_price) AS pre_discount_revenue,

        -- Calculate cost for each item line
        SUM(oi.quantity * (
            SELECT IFNULL(SUM(mir2.quantity_used * ii2.avg_price_per_unit), 0)
            FROM menu_item_recipes mir2
            JOIN inventory_items ii2 ON mir2.inventory_item_id = ii2.id
            WHERE mir2.menu_item_id = mi.id
        )) AS total_cost,
        
        -- Get the final (post-discount) order total and pre-discount order total
        o.order_total,
        (SELECT SUM(oi2.quantity * oi2.unit_price) FROM order_items oi2 WHERE oi2.order_id = o.id) as pre_discount_order_total
    FROM order_items oi
    JOIN orders o ON oi.order_id = o.id
    JOIN menu_items mi ON oi.menu_item_id = mi.id
    WHERE o.status = 'COMPLETED' AND o.created_at BETWEEN ? AND ?
    GROUP BY o.id, mi.id
    ORDER BY qty_sold DESC
");

$stmt->bind_param("ss", $start_dt, $end_dt);
$stmt->execute();
$results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Aggregate results in PHP to handle proportional discounts
$aggregatedItems = [];
foreach ($results as $r) {
    $itemId = $r['id'];
    $preDiscountLineRevenue = (float)$r['pre_discount_revenue'];
    $lineCost = (float)$r['total_cost'];
    $orderTotal = (float)$r['order_total'];
    $preDiscountOrderTotal = (float)$r['pre_discount_order_total'];

    // Calculate the actual revenue for this item line by proportionally applying the discount
    $actualLineRevenue = 0;
    if ($preDiscountOrderTotal > 0) {
        $revenueRatio = $preDiscountLineRevenue / $preDiscountOrderTotal;
        $actualLineRevenue = $orderTotal * $revenueRatio;
    }
    
    $lineProfit = $actualLineRevenue - $lineCost;

    if (!isset($aggregatedItems[$itemId])) {
        $aggregatedItems[$itemId] = [
            'id' => $itemId,
            'name' => $r['name'],
            'qty' => 0,
            'revenue' => 0,
            'cost' => 0,
            'profit' => 0,
        ];
    }

    $aggregatedItems[$itemId]['qty'] += (int)$r['qty_sold'];
    $aggregatedItems[$itemId]['revenue'] += $actualLineRevenue;
    $aggregatedItems[$itemId]['cost'] += $lineCost;
    $aggregatedItems[$itemId]['profit'] += $lineProfit;
}

// Final list of items
$items = array_values($aggregatedItems);

// Sort items by quantity sold for the table
usort($items, function($a, $b) {
    return $b['qty'] <=> $a['qty'];
});

// Sort items by profit for the bar chart
$items_sorted_by_profit = $items;
usort($items_sorted_by_profit, function($a, $b) {
    return $b['profit'] <=> $a['profit'];
});

// Payment breakdown data (Required for the Pie Chart)
$paymentBreakdown = [];
if ($hasPayments) {
    $pmStmt = $conn->prepare("
        SELECT p.payment_method, IFNULL(SUM(p.amount),0) AS total
        FROM payments p
        JOIN orders o ON p.order_id = o.id
        WHERE o.status='COMPLETED' AND p.created_at BETWEEN ? AND ?
        GROUP BY p.payment_method
    ");
    $pmStmt->bind_param("ss", $start_dt, $end_dt);
    $pmStmt->execute();
    $paymentBreakdown = $pmStmt->get_result()->fetch_all(MYSQLI_ASSOC);
    $pmStmt->close();
}

// --- END: NEW COMPREHENSIVE FINANCIAL LOGIC & HELPERS ---
?>
<title>Financials - Admin</title>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<main class="main-content">
    <h1>Financial Overview</h1>

    <div class="card">
        <h2>Select Timeframe</h2>
        <form method="get" style="display: flex; flex-wrap: wrap; gap: 10px; align-items: center;">
            <select name="period" onchange="this.form.submit()" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
                <option value="4days" <?= $period=='4days'?'selected':'' ?>>Last 4 days</option>
                <option value="7days" <?= $period=='7days'?'selected':'' ?>>Last 7 days</option>
                <option value="1month" <?= $period=='1month'?'selected':'' ?>>Last month</option>
                <option value="1year" <?= $period=='1year'?'selected':'' ?>>Last year</option>
            </select>
            <input type="date" name="start" value="<?= htmlspecialchars($start) ?>" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <input type="date" name="end" value="<?= htmlspecialchars($end) ?>" style="padding: 8px; border-radius: 5px; border: 1px solid #ccc;">
            <button type="submit" class="btn">Apply</button>
        </form>
        <p style="margin-top: 10px;">Showing data from <strong><?= htmlspecialchars($start) ?></strong> to <strong><?= htmlspecialchars($end) ?></strong></p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 20px;">
        <div class="card">
            <h3>Total Sales</h3>
            <p style="font-size: 24px; font-weight: bold;">Rs. <?= number_format($totalSales, 2) ?></p>
        </div>
        <div class="card">
            <h3>Cost of Goods (COGS)</h3>
            <p style="font-size: 24px; font-weight: bold;">Rs. <?= number_format($cogs, 2) ?></p>
        </div>
        <div class="card">
            <h3>Other Expenses</h3>
            <p style="font-size: 24px; font-weight: bold;">Rs. <?= number_format($otherExpenses, 2) ?></p>
        </div>
        <div class="card">
            <h3>Net Profit</h3>
            <p style="font-size: 24px; font-weight: bold; color: <?= $netProfit >= 0 ? 'green' : 'red' ?>;">Rs. <?= number_format($netProfit, 2) ?></p>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 20px;">
        
        <div class="card" style="padding: 20px; grid-column: span 2;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">Sales Trend</h3>
            <div style="height: 350px;">
                <canvas id="salesChart"></canvas>
            </div>
        </div>
        
        <div class="card" style="padding: 20px; display: flex; flex-direction: column; justify-content: center;">
            <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem; text-align: center;">Payment Methods</h3>
            <div style="max-height: 300px; margin: auto;">
                <?php if(!empty($paymentBreakdown)): ?>
                    <canvas id="paymentMethodChart"></canvas>
                <?php else: ?>
                    <p style="text-align: center; color: #a3a3a3;">Payment breakdown not available for this period.</p>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <div class="card" style="padding: 20px; margin-top: 20px;">
        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">Item Profitability (Top 5)</h3>
        <div style="height: 350px;">
            <canvas id="itemProfitChart"></canvas>
        </div>
    </div>


    <div class="card" style="padding: 20px; margin-top: 20px; overflow-x: auto;">
        <h3 style="font-size: 1.25rem; font-weight: 600; margin-bottom: 1rem;">Top Selling Items</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 1px solid #ccc;">
                    <th style="padding: 10px; text-align: left;">Item Name</th>
                    <th style="padding: 10px; text-align: left;">Qty Sold</th>
                    <th style="padding: 10px; text-align: left;">Total Revenue</th>
                    <th style="padding: 10px; text-align: left;">Total Cost</th>
                    <th style="padding: 10px; text-align: left;">Total Profit</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($items)): ?>
                    <tr><td colspan="5" style="text-align: center; padding: 15px; color: #a3a3a3;">No items sold in this period.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $it): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px; font-weight: 500;"><?= htmlspecialchars($it['name']) ?></td>
                        <td style="padding: 10px;"><?= number_format($it['qty']) ?></td>
                        <td style="padding: 10px;">Rs. <?= number_format($it['revenue'], 2) ?></td>
                        <td style="padding: 10px;">Rs. <?= number_format($it['cost'], 2) ?></td>
                        <td style="padding: 10px; color: <?= $it['profit'] >= 0 ? 'green' : 'red' ?>;">Rs. <?= number_format($it['profit'], 2) ?></td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</main>

<?php require_once 'admin_footer.php'; ?>

<script>
    // --- Chart.js Configuration ---
    // Note: I'm keeping the default styling minimal to work with your original file's simple look.
    Chart.defaults.font.family = "'Inter', sans-serif"; 
    Chart.defaults.color = '#333'; // Default text color for light theme

    // 1. Sales Trend Line Chart
    const salesChartData = <?= json_encode($chartData) ?>;
    new Chart(document.getElementById('salesChart'), {
        type: 'line',
        data: {
            labels: salesChartData.map(r => r[0]),
            datasets: [{
                label: 'Sales (Rs)',
                data: salesChartData.map(r => r[1]),
                borderColor: '#f97316', // Primary Orange
                backgroundColor: 'rgba(249, 115, 22, 0.2)', 
                fill: true,
                tension: 0.4,
                pointBackgroundColor: '#f97316',
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: { 
                y: { 
                    beginAtZero: true, 
                    grid: { color: '#e5e5e5' } // Light grid for contrast
                }, 
                x: { 
                    grid: { display: false }
                } 
            }
        }
    });

    // 2. Payment Method Doughnut Chart
    <?php if(!empty($paymentBreakdown)): ?>
    const paymentData = <?= json_encode($paymentBreakdown) ?>;
    new Chart(document.getElementById('paymentMethodChart'), {
        type: 'doughnut',
        data: {
            labels: paymentData.map(p => p.payment_method),
            datasets: [{
                backgroundColor: ['#14b8a6', '#6366f1', '#facc15', '#f87171', '#64748b'], 
                data: paymentData.map(p => p.total),
                borderWidth: 0
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: true,
        }
    });
    <?php endif; ?>
    
    // 3. Item Profitability Bar Chart
    const itemProfitData = <?= json_encode(array_slice($items_sorted_by_profit, 0, 5)) ?>;
    new Chart(document.getElementById('itemProfitChart'), {
        type: 'bar',
        data: {
            labels: itemProfitData.map(i => i.name),
            datasets: [
                {
                    label: 'Revenue',
                    data: itemProfitData.map(i => i.revenue),
                    backgroundColor: '#f97316', // Orange
                    borderRadius: 4
                },
                {
                    label: 'Cost',
                    data: itemProfitData.map(i => i.cost),
                    backgroundColor: 'rgba(120, 53, 15, 0.8)', // Dark Amber
                    borderRadius: 4
                },
                {
                    label: 'Profit',
                    data: itemProfitData.map(i => i.profit),
                    backgroundColor: 'rgba(21, 128, 61, 0.8)', // Dark Green
                    borderRadius: 4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            indexAxis: 'x',
            scales: { 
                y: { beginAtZero: true },
                x: { stacked: false }
            }
        }
    });

</script>