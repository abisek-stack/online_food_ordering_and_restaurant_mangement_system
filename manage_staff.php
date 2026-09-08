<?php
session_start();
if ($_SESSION['role'] === 'Customer') {
    header("Location: order.php");
    exit();
}


if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (($_SESSION['role'] ?? 'Customer') !== 'Admin') {
    die("Access Denied. Only Admins can manage staff.");
}

require_once 'db.php';
require_once 'staff_model.php';

$message = "";
$message_type = "";

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['add_staff'])) {
            add_staff(
                $_POST['name'],
                $_POST['role'],
                (float)$_POST['salary'],
                $_POST['join_date']
            );
            $message = "New staff added successfully!";
            $message_type = "success";
        } elseif (isset($_POST['update_staff'])) {
            update_staff(
                (int)$_POST['id'],
                $_POST['name'],
                $_POST['role'],
                (float)$_POST['salary'],
                $_POST['status']
            );
            $message = "Staff updated successfully!";
            $message_type = "success";
        } elseif (isset($_POST['delete_staff'])) {
            delete_staff((int)$_POST['id']);
            $message = "Staff deleted successfully!";
            $message_type = "success";
        }
    } catch (Exception $e) {
        $message = "Error: " . $e->getMessage();
        $message_type = "error";
    }
}

// Filters
$filter_role = $_GET['role'] ?? '';
$filter_status = $_GET['status'] ?? '';

$staff_list = get_all_staff();

// Apply filters
if ($filter_role) {
    $staff_list = array_filter($staff_list, fn($s) => $s['role'] === $filter_role);
}
if ($filter_status) {
    $staff_list = array_filter($staff_list, fn($s) => $s['status'] === $filter_status);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Staff</title>
    <style>
        body { font-family: Arial; margin: 20px; background: #f4f4f9; }
        h1 { margin-bottom: 20px; }
        .message.success { color: green; }
        .message.error { color: red; }
        .card { background: #fff; padding: 15px; border-radius: 6px; margin-bottom: 20px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
        form { margin-bottom: 0; }
        input, select { padding: 6px; margin: 5px 0; width: 100%; }
        button { padding: 6px 12px; margin: 3px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #eee; }
        .actions { display: flex; gap: 5px; }
    </style>
</head>
<body>
    <h1>Manage Staff</h1>

    <?php if ($message): ?>
        <p class="message <?= $message_type ?>"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <!-- Add Staff -->
    <div class="card">
        <h2>Add Staff</h2>
        <form method="POST">
            <input type="text" name="name" placeholder="Full Name" required>
            <select name="role" required>
                <option value="">-- Select Role --</option>
                <option value="Chef">Chef</option>
                <option value="Receptionist">Receptionist</option>
                <option value="Inventory Manager">Inventory Manager</option>
                <option value="Waiter">Waiter</option>
                <option value="Manager">Manager</option>
                <option value="Admin">Admin</option>
            </select>
            <input type="number" step="0.01" name="salary" placeholder="Salary" required>
            <input type="date" name="join_date" required>
            <button type="submit" name="add_staff">Add Staff</button>
        </form>
    </div>

    <!-- Filters -->
    <div class="card">
        <h2>Filter Staff</h2>
        <form method="GET">
            <label>Role:</label>
            <select name="role">
                <option value="">All</option>
                <option value="Chef" <?= $filter_role=="Chef"?"selected":"" ?>>Chef</option>
                <option value="Receptionist" <?= $filter_role=="Receptionist"?"selected":"" ?>>Receptionist</option>
                <option value="Inventory Manager" <?= $filter_role=="Inventory Manager"?"selected":"" ?>>Inventory Manager</option>
                <option value="Waiter" <?= $filter_role=="Waiter"?"selected":"" ?>>Waiter</option>
                <option value="Manager" <?= $filter_role=="Manager"?"selected":"" ?>>Manager</option>
                <option value="Admin" <?= $filter_role=="Admin"?"selected":"" ?>>Admin</option>
            </select>
            <label>Status:</label>
            <select name="status">
                <option value="">All</option>
                <option value="Active" <?= $filter_status=="Active"?"selected":"" ?>>Active</option>
                <option value="Inactive" <?= $filter_status=="Inactive"?"selected":"" ?>>Inactive</option>
            </select>
            <button type="submit">Apply Filter</button>
        </form>
    </div>

    <!-- Staff List -->
    <div class="card">
        <h2>Staff List</h2>
        <table>
            <thead>
                <tr>
                    <th>Name</th><th>Role</th><th>Salary</th><th>Status</th><th>Join Date</th><th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staff_list as $s): ?>
                    <tr>
                        <form method="POST">
                            <td><input type="text" name="name" value="<?= htmlspecialchars($s['name']) ?>"></td>
                            <td>
                                <select name="role">
                                    <option value="Chef" <?= $s['role']=="Chef"?"selected":"" ?>>Chef</option>
                                    <option value="Receptionist" <?= $s['role']=="Receptionist"?"selected":"" ?>>Receptionist</option>
                                    <option value="Inventory Manager" <?= $s['role']=="Inventory Manager"?"selected":"" ?>>Inventory Manager</option>
                                    <option value="Waiter" <?= $s['role']=="Waiter"?"selected":"" ?>>Waiter</option>
                                    <option value="Manager" <?= $s['role']=="Manager"?"selected":"" ?>>Manager</option>
                                    <option value="Admin" <?= $s['role']=="Admin"?"selected":"" ?>>Admin</option>
                                </select>
                            </td>
                            <td><input type="number" step="0.01" name="salary" value="<?= htmlspecialchars($s['salary']) ?>"></td>
                            <td>
                                <select name="status">
                                    <option value="Active" <?= $s['status']=="Active"?"selected":"" ?>>Active</option>
                                    <option value="Inactive" <?= $s['status']=="Inactive"?"selected":"" ?>>Inactive</option>
                                </select>
                            </td>
                            <td><?= htmlspecialchars($s['join_date']) ?></td>
                            <td class="actions">
                                <input type="hidden" name="id" value="<?= $s['id'] ?>">
                                <button type="submit" name="update_staff">Update</button>
                                <button type="submit" name="delete_staff" onclick="return confirm('Are you sure?')">Delete</button>
                            </td>
                        </form>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
