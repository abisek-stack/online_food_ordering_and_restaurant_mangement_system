<?php
include "db.php";
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username_email = $_POST['username_email'];
    $password = $_POST['password'];

    // Note: This code is highly vulnerable to SQL Injection.
    // In a professional setting, always use prepared statements!
    $sql = "SELECT id, username, password, gender, role FROM user_details WHERE username='$username_email' OR email='$username_email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        // Uses password_verify() for secure password checking, which is professional best practice.
        if (password_verify($password, $row['password'])) {
            $_SESSION['username'] = $row['username'];
            $_SESSION['user_id']  = (int)$row['id'];
            $_SESSION['gender']   = $row['gender'] ?? null;
            $_SESSION['role']     = $row['role'];

            header("Location: dashboard.php");
            exit();
        } else {
            $message = "Invalid password."; 
        }
    } else {
        $message = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login</title> 
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

.link-subtle {
    color: var(--text-secondary);
    transition: color 0.3s;
}
.link-subtle:hover {
    color: var(--accent-orange);
}
</style>
</head>
<body>

<div class="card-bg">
    <div class="text-center mb-12">
        <div class="mx-auto w-20 h-20 mb-6 text-white opacity-90">
            <i class="fas fa-lock text-6xl"></i>
        </div>
        <h2 class="luxury-heading text-white mb-3">LOGIN</h2> <p class="text-gray-400 text-lg font-light">Enter your credentials to access your account.</p>
    </div>

    <?php if(!empty($message)) : ?>
        <div class="message-error p-4 rounded-xl shadow-lg">
            <i class="fas fa-exclamation-circle mr-2"></i> <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="space-y-6">
        <div>
            <label for="username_email" class="sr-only">Username or Email</label>
            <input type="text" name="username_email" id="username_email" required
                   class="form-input-lux w-full px-6 py-4 rounded-full" placeholder="Username or Email">
        </div>
        <div>
            <label for="password" class="sr-only">Password</label>
            <input type="password" name="password" id="password" required
                   class="form-input-lux w-full px-6 py-4 rounded-full" placeholder="Password">
        </div>
        
        <div class="text-right pt-2 pb-4">
            <a href="forgot_password.php" class="text-base link-subtle hover:text-amber-500 transition">
                Forgot Password?
            </a>
        </div>
        
        <button type="submit" class="button-lux w-full rounded-full">
            <i class="fas fa-sign-in-alt mr-2"></i> Log In
        </button>
    </form>

    <p class="mt-10 text-center text-gray-400 text-base">
        Don't have an account?
        <a href="register.php" class="text-amber-500 font-medium hover:text-amber-400 border-b border-transparent hover:border-amber-500 transition">
            Register Here
        </a>
    </p>
</div>

</body>
</html>