<?php 
require_once 'admin_header.php'; // Includes session, db, and sidebar
require_once 'staff_model.php';

$message = "";
$message_type = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_staff'])) {
            add_staff($_POST['name'], $_POST['role'], (float)$_POST['salary'], $_POST['join_date']);
            $message = "New staff added successfully!";
            $message_type = "success";
        } elseif (isset($_POST['add_payroll'])) {
            add_payroll((int)$_POST['staff_id'], $_POST['month_year'], (float)$_POST['amount'], $_POST['payment_date']);
            $message = "Payroll recorded successfully!";
            $message_type = "success";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "error";
    }
}

$staff_list = get_all_staff();
$payroll_history = get_payroll_history();
?>
<title>Staff & Payroll</title>

<main class="main-content">
    <h1>Staff & Payroll Management</h1>

    <?php if ($message): ?>
        <div class="card" style="color: white; background-color: <?= $message_type === 'success' ? '#28a745' : '#dc3545'; ?>;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Add New Records</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <form method="POST">
                <h3>Add Staff Member</h3>
                <input type="text" name="name" placeholder="Full Name" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 10px;">
                <input type="text" name="role" placeholder="Role (e.g., Chef)" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 10px;">
                <input type="number" step="0.01" name="salary" placeholder="Monthly Salary (Rs.)" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 10px;">
                <input type="date" name="join_date" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 10px;">
                <button type="submit" name="add_staff" class="btn">Add Staff</button>
            </form>

            <form method="POST">
                <h3>Record Payroll</h3>
                <select name="staff_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 10px;">
                    <option value="" disabled selected>-- Select Staff Member --</option>
                    <?php foreach ($staff_list as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="text" name="month_year" placeholder="Period (e.g., Oct-2025)" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 10px;">
                <input type="number" step="0.01" name="amount" placeholder="Amount Paid (Rs.)" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 10px;">
                <input type="date" name="payment_date" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; margin-bottom: 10px;">
                <button type="submit" name="add_payroll" class="btn">Record Payment</button>
            </form>
        </div>
    </div>
    
    <div class="card">
        <h2>Current Staff Roster</h2>
        <table>
            <thead><tr><th>ID</th><th>Name</th><th>Role</th><th>Salary</th><th>Joined</th></tr></thead>
            <tbody>
                <?php foreach ($staff_list as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['id']) ?></td>
                    <td><?= htmlspecialchars($s['name']) ?></td>
                    <td><?= htmlspecialchars($s['role']) ?></td>
                    <td>Rs. <?= number_format($s['salary'], 2) ?></td>
                    <td><?= htmlspecialchars($s['join_date']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <div class="card">
        <h2>Payroll History</h2>
        <table>
            <thead><tr><th>Staff</th><th>Role</th><th>Month</th><th>Amount</th><th>Date Paid</th></tr></thead>
            <tbody>
                <?php foreach ($payroll_history as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['role']) ?></td>
                    <td><?= htmlspecialchars($p['month_year']) ?></td>
                    <td>Rs. <?= number_format($p['amount'], 2) ?></td>
                    <td><?= htmlspecialchars($p['payment_date']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once 'admin_footer.php'; ?>