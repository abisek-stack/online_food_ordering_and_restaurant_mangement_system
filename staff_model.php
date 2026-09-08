<?php
require_once 'db.php';

/** Get all staff */
function get_all_staff(): array {
    global $conn;
    $res = $conn->query("SELECT * FROM staff ORDER BY name");
    return $res->fetch_all(MYSQLI_ASSOC);
}

/** Add new staff */
function add_staff(string $name, string $role, float $salary, string $join_date): bool {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO staff (name, role, salary, join_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $name, $role, $salary, $join_date);
    return $stmt->execute();
}

/** Record payroll */
function add_payroll(int $staff_id, string $month_year, float $amount, string $payment_date): bool {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO payroll (staff_id, month_year, amount, payment_date) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $staff_id, $month_year, $amount, $payment_date);
    return $stmt->execute();
}

/** Get payroll history */
function get_payroll_history(): array {
    global $conn;
    $sql = "SELECT p.id, s.name, s.role, p.month_year, p.amount, p.payment_date 
            FROM payroll p 
            JOIN staff s ON p.staff_id = s.id 
            ORDER BY p.payment_date DESC";
    $res = $conn->query($sql);
    return $res->fetch_all(MYSQLI_ASSOC);
}
/** Update staff */
function update_staff(int $id, string $name, string $role, float $salary, string $status): bool {
    global $conn;
    $stmt = $conn->prepare("UPDATE staff SET name=?, role=?, salary=?, status=? WHERE id=?");
    $stmt->bind_param("ssdsi", $name, $role, $salary, $status, $id);
    return $stmt->execute();
}

/** Delete staff */
function delete_staff(int $id): bool {
    global $conn;
    $stmt = $conn->prepare("DELETE FROM staff WHERE id=?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}
