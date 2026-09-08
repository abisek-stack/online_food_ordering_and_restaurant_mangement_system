<?php 
require_once 'admin_header.php'; // Includes session, db, and sidebar
require_once 'discount_model.php';

$message = "";
$message_type = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_discount'])) {
            add_discount($_POST['code'], $_POST['description'], $_POST['discount_type'], (float)$_POST['value'], $_POST['start_date'], $_POST['end_date']);
            $message = "Discount added successfully!"; $message_type = "success";
        } elseif (isset($_POST['activate'])) {
            update_discount_status((int)$_POST['id'], "Active");
            $message = "Discount activated."; $message_type = "success";
        } elseif (isset($_POST['deactivate'])) {
            update_discount_status((int)$_POST['id'], "Inactive");
            $message = "Discount deactivated."; $message_type = "success";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage(); $message_type = "error";
    }
}

$discounts = get_all_discounts();
?>
<title>Discount Management</title>

<main class="main-content">
    <h1>Discount Management</h1>

    <?php if ($message): ?>
        <div class="card" style="color: white; background-color: <?= $message_type === 'success' ? '#28a745' : '#dc3545'; ?>;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Add New Discount</h2>
        <form method="POST">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <input type="text" name="code" placeholder="Discount Code" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <input type="number" step="0.01" name="value" placeholder="Value" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <select name="discount_type" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                    <option value="percent">Percentage (%)</option>
                    <option value="fixed">Fixed Amount (Rs.)</option>
                </select>
                <input type="date" name="start_date" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <input type="date" name="end_date" required style="padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
                <input type="text" name="description" placeholder="Description (optional)" style="grid-column: 1 / -1; padding: 10px; border: 1px solid #ccc; border-radius: 5px;">
            </div>
            <button type="submit" name="add_discount" class="btn" style="margin-top: 15px;">Add Discount</button>
        </form>
    </div>

    <div class="card">
        <h2>Current Discounts</h2>
        <table>
            <thead><tr><th>Code</th><th>Description</th><th>Type</th><th>Value</th><th>Dates</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
                <?php foreach ($discounts as $d): ?>
                <tr>
                    <td><?= htmlspecialchars($d['code']) ?></td>
                    <td><?= htmlspecialchars($d['description']) ?></td>
                    <td><?= htmlspecialchars(ucfirst($d['discount_type'])) ?></td>
                    <td><?= ($d['discount_type'] === 'percent' ? $d['value'] . '%' : 'Rs. ' . number_format($d['value'], 2)) ?></td>
                    <td><?= htmlspecialchars($d['start_date']) ?> to <?= htmlspecialchars($d['end_date']) ?></td>
                    <td><span style="background-color: <?= $d['status'] === 'Active' ? 'green' : 'gray'; ?>; color: white; padding: 4px 8px; border-radius: 10px; font-size: 12px;"><?= htmlspecialchars($d['status']) ?></span></td>
                    <td>
                        <form method="POST" style="display:inline">
                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                            <?php if ($d['status'] === "Active"): ?>
                                <button type="submit" name="deactivate" class="btn btn-sm btn-danger">Deactivate</button>
                            <?php else: ?>
                                <button type="submit" name="activate" class="btn btn-sm btn-success">Activate</button>
                            <?php endif; ?>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once 'admin_footer.php'; ?>