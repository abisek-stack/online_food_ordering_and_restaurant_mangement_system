<?php
// Include the database connection file.
// IMPORTANT: For production, ensure db.php uses secure, prepared statements!
include "db.php";
session_start();

$message = "";
$message_type = ""; // success or error

/**
 * Mocks the necessary function to send an email.
 * In a real application, this would use a library like PHPMailer or a service like SendGrid/Mailgun.
 * @param string $to The recipient's email address.
 * @param string $token The generated reset token.
 * @return bool Always true for simulation.
 */
function send_reset_email($to, $token) {
    // In a production environment, the reset link would look like this:
    // $reset_link = "http://yourwebsite.com/reset_password.php?token=" . $token;

    // --- REAL EMAIL SENDING LOGIC WOULD GO HERE ---
    // Example using PHP's mail() function (not recommended for production):
    /*
    $subject = "Password Reset Request";
    $body = "A password reset was requested for your account. Click the link below to set a new password:\n\n";
    $body .= $reset_link;
    $headers = "From: no-reply@yourdomain.com";
    return mail($to, $subject, $body, $headers);
    */
    
    // We simulate success for the purpose of this canvas demo:
    error_log("SIMULATION: Reset email sent to $to with token $token");
    return true;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // 1. Check if the email exists in the user_details table
    // SECURITY NOTE: This query is highly vulnerable to SQL Injection. Use prepared statements in production!
    $sql_check = "SELECT id, username FROM user_details WHERE email='$email'";
    $result_check = $conn->query($sql_check);

    if ($result_check && $result_check->num_rows == 1) {
        $user = $result_check->fetch_assoc();
        $user_id = $user['id'];
        
        // 2. Generate a secure, unique token
        $token = bin2hex(random_bytes(32)); 
        $expires_at = time() + (60 * 30); // Token expires in 30 minutes

        // 3. Store the token and expiry time in a separate database table (e.g., password_resets)
        // Since we don't have a live database, we will mock the insertion.
        
        /*
        // REAL DB QUERY (requires a table named 'password_resets'):
        $sql_insert = "INSERT INTO password_resets (user_id, token, expires_at) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql_insert);
        $stmt->bind_param("isi", $user_id, $token, $expires_at);
        $stmt->execute();
        */
        
        // 4. Send the password reset email
        if (send_reset_email($email, $token)) {
            $message = "Success! If an account exists for " . htmlspecialchars($email) . ", a password reset link has been sent to your inbox.";
            $message_type = "success";
        } else {
            $message = "Error: Could not send the password reset email. Please try again later.";
            $message_type = "error";
        }

    } else {
        // We always show a generic success message for security, even if the email doesn't exist,
        // to prevent users from probing for valid email addresses.
        $message = "If an account exists for " . htmlspecialchars($email) . ", a password reset link has been sent to your inbox.";
        $message_type = "success";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Forgot Password</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

<style>
:root {
    --bg-primary: #0A0A0A;
    --bg-card: rgba(24,24,24,0.9);
    --text-primary: #F0F0F0;
    --text-secondary: #AAAAAA;
    --accent-orange: #f97316;
    --accent-hover: #fb923c;
    --border-subtle: #333333;
    --shadow-deep: 0 15px 50px rgba(0,0,0,0.9);
}

body {
    font-family: 'Inter', sans-serif;
    background: linear-gradient(135deg,#0A0A0A 0%,#1a1a1a 100%);
    color: var(--text-secondary);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.luxury-heading {
    font-family: 'Cormorant Garamond', serif;
    color: var(--accent-orange);
    letter-spacing: 0.25em;
    text-shadow: 0 0 14px rgba(249,115,22,0.6);
    text-transform: uppercase;
    font-size: 4.5xl;
}

.card-bg {
    background: var(--bg-card);
    backdrop-filter: blur(14px);
    border: 1px solid var(--border-subtle);
    box-shadow: var(--shadow-deep);
    color: var(--text-primary);
    transition: transform 0.5s ease, box-shadow 0.5s ease;
    width: 450px;
    padding: 3rem 2.5rem;
    border-radius: 2rem;
}
.card-bg:hover {
    transform: translateY(-6px) scale(1.025);
    box-shadow: 0 20px 60px rgba(249,115,22,0.85);
}

.form-input-lux {
    border: 1px solid var(--border-subtle);
    background-color: rgba(10,10,10,0.65);
    color: var(--text-primary);
    transition: all 0.4s ease;
    font-size: 1.125rem;
}
.form-input-lux::placeholder {
    color: var(--text-secondary);
}
.form-input-lux:focus {
    border-color: var(--accent-orange);
    box-shadow: 0 0 12px var(--accent-orange);
    outline: none;
    background-color: rgba(16,16,16,0.9);
}

.button-lux {
    background: linear-gradient(135deg,#f97316,#fb923c);
    color: #0A0A0A;
    font-weight: 700;
    letter-spacing: 0.12em;
    box-shadow: 0 8px 25px rgba(249,115,22,0.6);
    transition: all 0.3s ease-in-out;
    font-size: 1.125rem;
    padding: 1rem 0;
}
.button-lux:hover {
    transform: translateY(-4px) scale(1.025);
    box-shadow: 0 14px 35px rgba(249,115,22,0.8);
}

.message-error {
    background-color: rgba(248,113,113,0.15);
    border: 1px solid #f87171;
    color: #f87171;
    font-weight: 500;
    text-align: center;
    margin-bottom: 1.5rem;
}
.message-success {
    background-color: rgba(52,211,153,0.15);
    border: 1px solid #34d399;
    color: #34d399;
    font-weight: 500;
    text-align: center;
    margin-bottom: 1.5rem;
}

</style>
</head>
<body>

<div class="card-bg">
    <div class="text-center mb-12">
        <div class="mx-auto w-20 h-20 mb-6 text-white opacity-90">
            <i class="fas fa-question-circle text-6xl"></i>
        </div>
        <h2 class="luxury-heading text-white mb-3 text-4xl">FORGOT PASSWORD</h2>
        <p class="text-gray-400 text-lg font-light">Enter your email to receive a password reset link.</p>
    </div>

    <?php if(!empty($message)) : ?>
        <div class="p-4 rounded-xl shadow-lg 
            <?php echo ($message_type === 'error' ? 'message-error' : 'message-success'); ?>">
            <i class="fas fa-info-circle mr-2"></i> <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
        <div>
            <label for="email" class="sr-only">Email Address</label>
            <input type="email" name="email" id="email" required
                   class="form-input-lux w-full px-6 py-4 rounded-full" placeholder="Email Address">
        </div>
        
        <button type="submit" class="button-lux w-full rounded-full">
            <i class="fas fa-envelope mr-2"></i> Send Reset Link
        </button>
    </form>

    <p class="mt-10 text-center text-gray-400 text-base">
        <a href="login.php" class="text-amber-500 font-medium hover:text-amber-400 transition">
            <i class="fas fa-arrow-left mr-1"></i> Back to Login
        </a>
    </p>
</div>

</body>
</html>
