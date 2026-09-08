<?php 
require_once 'admin_header.php'; // Includes session, db, and sidebar
require_once 'inventory_model.php';

$message = '';
$message_type = ''; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_stock'])) {
            add_stock((int)$_POST['item_id'], (float)$_POST['quantity'], (float)$_POST['price']);
            $message = "Stock added successfully!";
            $message_type = 'success';
        } elseif (isset($_POST['record_waste'])) {
            record_waste((int)$_POST['item_id_waste'], (float)$_POST['quantity_waste'], $_POST['notes_waste']);
            $message = "Waste recorded successfully!";
            $message_type = 'success';
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = 'error';
    }
}

$inventory_items = get_inventory_status();
?>
<title>Inventory Management</title>

<main class="main-content">
    <h1>Inventory Management</h1>

    <?php if ($message): ?>
        <div class="card" style="color: white; background-color: <?= $message_type === 'success' ? '#28a745' : '#dc3545'; ?>;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <h2>Manage Stock</h2>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <form method="POST">
                <h3>Add New Stock</h3>
                <select name="item_id" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; margin-bottom: 10px;">
                    <option value="">-- Select Item --</option>
                    <?php foreach ($inventory_items as $item): ?>
                        <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" step="0.001" name="quantity" placeholder="Quantity" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; margin-bottom: 10px;">
                <input type="number" step="0.01" name="price" placeholder="Price per Unit" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; margin-bottom: 10px;">
                <button type="submit" name="add_stock" class="action-button">Add Stock</button>
            </form>
            <form method="POST">
                <h3>Record Waste/Spoilage</h3>
                <select name="item_id_waste" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; margin-bottom: 10px;">
                    <option value="">-- Select Item --</option>
                    <?php foreach ($inventory_items as $item): ?>
                        <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <input type="number" step="0.001" name="quantity_waste" placeholder="Wasted Quantity" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; margin-bottom: 10px;">
                <input type="text" name="notes_waste" placeholder="Reason (e.g., spoiled)" required style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #ddd; box-sizing: border-box; margin-bottom: 10px;">
                <button type="submit" name="record_waste" class="action-button">Record Waste</button>
            </form>
        </div>
    </div>

    <div class="card">
        <h2>Current Inventory Status</h2>
        <table>
            <thead>
                <tr>
                    <th>Ingredient</th>
                    <th>Current Stock</th>
                    <th>Unit</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($inventory_items as $item): ?>
                    <tr>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= number_format($item['current_stock'], 3) ?></td>
                        <td><?= htmlspecialchars($item['unit']) ?></td>
                        <td>
                            <?php
                                $status_class = 'status-ok'; $status_text = 'OK';
                                if ($item['current_stock'] <= $item['min_level']) { $status_class = 'status-low'; $status_text = 'Low Stock!'; }
                                if ($item['current_stock'] <= ($item['min_level'] * 0.5)) { $status_class = 'status-critical'; $status_text = 'Critically Low!'; }
                            ?>
                            <span style="color: <?= $status_class === 'status-ok' ? 'green' : ($status_class === 'status-low' ? 'orange' : 'red') ?>; font-weight: bold;"><?= $status_text ?></span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require_once 'admin_footer.php'; ?>