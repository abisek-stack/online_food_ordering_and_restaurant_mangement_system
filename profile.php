<?php
require_once 'customer_header.php'; // Keeps sidebar + top bar visible
require_once 'db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Fetch customer details
$stmt = $conn->prepare("SELECT username, email, phone, address, gender FROM user_details WHERE id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

$message = "";
$message_type = "";

// Handle profile update form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $gender = $_POST["gender"];

    $stmt = $conn->prepare("UPDATE user_details SET username=?, email=?, phone=?, address=?, gender=? WHERE id=?");
    $stmt->bind_param("sssssi", $username, $email, $phone, $address, $gender, $userId);

    if ($stmt->execute()) {
        $_SESSION["username"] = $username;
        $_SESSION["gender"] = $gender;
        $message = "Profile updated successfully!";
        $message_type = "success";
    } else {
        $message = "Error updating profile. Please try again.";
        $message_type = "error";
    }

    $stmt->close();
}
?>

<title>My Profile</title>

<div class="card" style="max-width: 600px; margin: auto; background: #fff; border-radius: 12px; padding: 25px; box-shadow: 0 6px 16px rgba(0,0,0,0.1);">
    <h1 style="font-size: 24px; color: #2c2f48; margin-bottom: 20px;">My Profile</h1>

    <?php if (!empty($message)): ?>
        <div style="padding: 10px 15px; border-radius: 8px; color: white; background-color: <?= $message_type === 'success' ? '#28a745' : '#dc3545'; ?>; margin-bottom: 15px;">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <form method="POST" style="display: flex; flex-direction: column; gap: 15px;">
        <label>
            <strong>Full Name:</strong>
            <input type="text" name="username" value="<?= htmlspecialchars($user['username'] ?? '') ?>" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
        </label>

        <label>
            <strong>Email:</strong>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
        </label>

        <label>
            <strong>Phone:</strong>
            <input type="text" name="phone" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
        </label>

        <label>
            <strong>Address:</strong>
            <textarea name="address" rows="2" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
        </label>

        <label>
            <strong>Gender:</strong>
            <select name="gender" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #ccc;">
                <option value="Male" <?= ($user['gender'] ?? '') === 'Male' ? 'selected' : '' ?>>Male</option>
                <option value="Female" <?= ($user['gender'] ?? '') === 'Female' ? 'selected' : '' ?>>Female</option>
            </select>
        </label>

        <button type="submit" class="btn" style="background-color: #ff7043; border: none; color: white; font-weight: 600; padding: 10px 15px; border-radius: 6px; cursor: pointer;">Update Profile</button>
    </form>
</div>

<?php require_once 'customer_footer.php'; ?>
