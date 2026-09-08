<?php
require_once 'db.php';

/** Get all discounts */
function get_all_discounts(): array {
    global $conn;
    $res = $conn->query("SELECT * FROM discounts ORDER BY start_date DESC");
    return $res->fetch_all(MYSQLI_ASSOC);
}

/** Add new discount */
function add_discount(string $code, string $description, string $type, float $value, string $start_date, string $end_date): bool {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO discounts (code, description, discount_type, value, start_date, end_date) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiss", $code, $description, $type, $value, $start_date, $end_date);
    return $stmt->execute();
}

/** Change status (activate/deactivate) */
function update_discount_status(int $id, string $status): bool {
    global $conn;
    $stmt = $conn->prepare("UPDATE discounts SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    return $stmt->execute();
}
