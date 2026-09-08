<?php
session_start();

// Redirect to login if user is not authenticated
if (isset($_SESSION['role']) && $_SESSION['role'] === 'Customer') {
    header("Location: order.php"); // This sends customers to their order page
    exit();
}
// Remove the specific customer redirect so they can use the dashboard layout.
// Note: If customer tries to access Admin-only pages (e.g., inventory.php), 
// those pages should have their own internal role checks.

require_once 'db.php';

// Fetch user details from session
$username = $_SESSION['username'];
$role = $_SESSION['role'];
$gender = $_SESSION['gender'] ?? "Male";
// Select avatar based on gender
$avatar = ($gender === "Female") ? "assets/img/female.png" : "assets/img/male.png";
// Function to highlight the active page in the sidebar
function is_active($page_name) {
    return basename($_SERVER['PHP_SELF']) == $page_name ? "active" : "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-color: #ff7043; /* Orange accent */
            --secondary-color: #2c2f48;
            --background-color: #f9fafc;
            --text-color: #333;
            --sidebar-text: #eee;
            --card-shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            --border-radius: 14px;
        }
        html, body {
            height: 100%; /* Ensure body and html take full height */
            margin: 0;
            padding: 0;
            overflow: hidden; /* Prevent double scrollbars */
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            display: flex;
        }
        /* Sidebar */
        .sidebar {
            width: 260px;
            background: var(--secondary-color);
            color: var(--sidebar-text);
            display: flex;
            flex-direction: column;
            flex-shrink: 0; /* Prevent sidebar from shrinking */
        }
        .sidebar-header {
            padding: 25px 20px;
            text-align: center;
            border-bottom: 1px solid #444;
        }
        .sidebar-header h2 {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
            color: var(--primary-color);
        }
        .sidebar-nav {
            list-style: none;
            padding: 20px 0;
            margin: 0;
            flex-grow: 1;
            overflow-y: auto; /* Allow sidebar to scroll if it has too many items */
        }
        .sidebar-nav li a {
            display: flex;
            align-items: center;
            padding: 14px 25px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-weight: 500;
            transition: background 0.3s, color 0.3s;
            border-left: 3px solid transparent;
        }
        .sidebar-nav li a:hover, .sidebar-nav li a.active {
            background: rgba(255, 255, 255, 0.08);
            color: #fff;
            border-left: 3px solid var(--primary-color);
        }
        .sidebar-nav li a i {
            margin-right: 12px;
        }
        /* Layout Container (THE FIX IS HERE) */
        .container {
            flex-grow: 1; /* This makes the container take up all remaining space */
            width: 100%;   /* Explicitly set width */
            display: flex;
            flex-direction: column;
            height: 100vh; /* Full viewport height */
        }
        .header {
            flex-shrink: 0; /* Prevent header from shrinking */
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 30px;
            background: #fff;
            box-shadow: 0 3px 8px rgba(0,0,0,0.05);
        }
        /* Main Content Area */
        .main-content {
            flex-grow: 1; /* Make the main content area fill the space below the header */
            padding: 30px;
            overflow-y: auto; /* Allow only the content to scroll */
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .user-info img {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid var(--primary-color);
        }
        .user-info .role-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            background: var(--primary-color);
            color: white;
        }
        .logout-link {
            color: var(--primary-color);
            font-size: 18px;
            text-decoration: none;
        }
        /* Generic Card and Table styles */
        .main-content h1 {
            font-size: 28px;
            margin-bottom: 20px;
            color: var(--secondary-color);
        }
        .card {
            background: #fff;
            border-radius: var(--border-radius);
            padding: 25px;
            box-shadow: var(--card-shadow);
            margin-bottom: 25px;
        }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 12px 15px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: #f8f9fa; font-weight: 600; }
        .btn {
            display: inline-block; background: var(--primary-color); color: #fff; padding: 10px 20px;
            border-radius: 8px; text-decoration: none; font-weight: 600; border: none; cursor: pointer;
        }
        .btn:hover { background: #e45b2b; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .btn-info { background: #17a2b8; }
        .btn-primary { background: #007bff; }
        .btn-success { background: #28a745; }
        .btn-danger { background: #dc3545; }
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <h2><i class="fa fa-bowl-food"></i> Flavoro</h2>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php" class="<?= is_active('dashboard.php') ?>"><i class="fa fa-gauge"></i> Dashboard</a></li>
            <li><a href="customer_orders.php" class="<?= is_active('customer_orders.php') ?>"><i class="fa fa-users"></i> Customer Orders</a></li>
            <li><a href="inventory.php" class="<?= is_active('inventory.php') ?>"><i class="fa fa-box"></i> Inventory</a></li>
            <li><a href="financials.php" class="<?= is_active('financials.php') ?>"><i class="fa fa-wallet"></i> Financials</a></li>
            <li><a href="staff_and_payroll.php" class="<?= is_active('staff_and_payroll.php') ?>"><i class="fa fa-users"></i> Staff & Payroll</a></li>
            <li><a href="discounts.php" class="<?= is_active('discounts.php') ?>"><i class="fa fa-gift"></i> Discounts</a></li>
            <li><a href="reports.php" class="<?= is_active('reports.php') ?>"><i class="fa fa-chart-line"></i> Reports</a></li>
        </ul>
    </aside>

    <div class="container">
        <header class="header">
            <div></div> <div class="user-info">
                <img src="<?= $avatar ?>" alt="User Avatar">
                <span>Hi, <strong><?= htmlspecialchars($username); ?></strong></span>
                <span class="role-badge"><?= htmlspecialchars($role); ?></span>
                <a href="logout.php" class="logout-link" title="Logout"><i class="fa fa-sign-out-alt"></i></a>
            </div>
        </header>

        <main class="main-content">
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatbot</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* === ChatGPT Dark Mode Style Chatbot === */
        .chatbot-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 1000;
            font-family: 'Inter', 'Segoe UI', sans-serif;
        }

        /* Toggle button */
        .chatbot-toggle {
            background: #10a37f; /* ChatGPT green */
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: none;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        .chatbot-toggle:hover {
            transform: scale(1.1);
        }

        /* Chat window */
        .chat-window {
            display: none;
            position: absolute;
            bottom: 80px;
            right: 0;
            width: 380px;
            height: 500px;
            background: #343541; /* ChatGPT grey */
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4);
            flex-direction: column;
            overflow: hidden;
        }
        .chat-window.fullscreen {
            position: fixed;
            width: 100%;
            height: 100%;
            top: 0;
            right: 0;
            left: 0;
            bottom: 0;
            border-radius: 0;
            z-index: 2000;
        }

        /* Header */
        .chat-header {
            background: #202123;
            color: #ffffff;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .chat-header h3 {
            font-size: 18px;
            margin: 0;
            font-weight: 600;
        }
        .fullscreen-btn {
            background: transparent;
            border: none;
            color: white;
            font-size: 18px;
            cursor: pointer;
        }

        /* Chat area */
        .chat-body {
            flex-grow: 1;
            padding: 15px;
            overflow-y: auto;
            background-color: #343541;
            color: #ffffff;
        }

        /* Footer */
        .chat-footer {
            padding: 10px;
            display: flex;
            gap: 5px;
            border-top: 1px solid #565869;
            background: #40414f;
        }
        .chat-footer input {
            flex-grow: 1;
            border-radius: 20px;
            border: 1px solid #565869;
            background: #40414f;
            color: white;
            padding: 10px 15px;
            font-size: 15px;
        }
        .chat-footer input::placeholder {
            color: #aaa;
        }
        .chat-footer button {
            border-radius: 20px;
            background: #10a37f;
            color: white;
            border: none;
            padding: 8px 14px;
            cursor: pointer;
            font-weight: 500;
        }

        /* Message styling */
        .chat-message {
            margin-bottom: 10px;
            padding: 12px 16px;
            border-radius: 12px;
            max-width: 80%;
            line-height: 1.6;
            font-size: 15px;
            white-space: pre-wrap;
            word-wrap: break-word;
        }
        .user-message {
            background: #0d0d0d;
            color: #ffffff;
            align-self: flex-end;
            margin-left: auto;
        }
        .bot-message {
            background: #444654;
            color: #ffffff;
            align-self: flex-start;
        }

        /* Bold & Headings (white only) */
        .bot-message b,
        .bot-message strong,
        .bot-message h1,
        .bot-message h2,
        .bot-message h3,
        .bot-message h4 {
            color: #ffffff !important;
            font-weight: 700;
        }

        /* Typing indicator */
        .typing-indicator {
            display: flex;
            gap: 4px;
            align-items: center;
            padding: 8px 12px;
        }
        .typing-indicator .dot {
            width: 8px;
            height: 8px;
            background-color: #10a37f;
            border-radius: 50%;
            animation: blink 1.4s infinite both;
        }
        .typing-indicator .dot:nth-child(2) {
            animation-delay: 0.2s;
        }
        .typing-indicator .dot:nth-child(3) {
            animation-delay: 0.4s;
        }
        @keyframes blink {
            0%, 80%, 100% { opacity: 0.2; }
            40% { opacity: 1; }
        }
    </style>
</head>
<body>

    <div class="chatbot-container">
        <div class="chat-window" id="chat-window">
            <div class="chat-header">
                <h3>Flavoro AI</h3>
                <button class="fullscreen-btn" id="fullscreen-btn"><i class="fa fa-expand"></i></button>
            </div>
            <div class="chat-body" id="chat-body">
                <div class="chat-message bot-message"><b>Ask me anything about the database!</b></div>
            </div>
            <div class="chat-footer">
                <input type="text" id="chat-input" placeholder="Ask anything...">
                <button id="chat-send"><i class="fa fa-paper-plane"></i></button>
            </div>
        </div>
        <button class="chatbot-toggle" id="chatbot-toggle"><i class="fa fa-robot"></i></button>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const chatbotToggle = document.getElementById('chatbot-toggle');
        const chatWindow = document.getElementById('chat-window');
        const chatInput = document.getElementById('chat-input');
        const chatSend = document.getElementById('chat-send');
        const chatBody = document.getElementById('chat-body');
        const fullscreenBtn = document.getElementById('fullscreen-btn');

        chatbotToggle.addEventListener('click', () => {
            chatWindow.style.display = chatWindow.style.display === 'flex' ? 'none' : 'flex';
        });

        let isFullscreen = false;
        fullscreenBtn.addEventListener('click', () => {
            isFullscreen = !isFullscreen;
            if (isFullscreen) {
                chatWindow.classList.add('fullscreen');
                fullscreenBtn.innerHTML = '<i class="fa fa-compress"></i>';
            } else {
                chatWindow.classList.remove('fullscreen');
                fullscreenBtn.innerHTML = '<i class="fa fa-expand"></i>';
            }
        });

        const typeText = async (element, text, speed = 1) => {
            let i = 0;
            element.innerHTML = ""; // Clear existing content
            return new Promise(resolve => {
                const interval = setInterval(() => {
                    if (i < text.length) {
                        element.innerHTML += text.charAt(i);
                        i++;
                        chatBody.scrollTop = chatBody.scrollHeight;
                    } else {
                        clearInterval(interval);
                        resolve();
                    }
                }, speed);
            });
        };

        const sendMessage = async () => {
            const query = chatInput.value.trim();
            if (!query) return;

            const userMsgDiv = document.createElement('div');
            userMsgDiv.className = 'chat-message user-message';
            userMsgDiv.textContent = query;
            chatBody.appendChild(userMsgDiv);
            chatInput.value = '';
            chatBody.scrollTop = chatBody.scrollHeight;

            const typingDiv = document.createElement('div');
            typingDiv.className = 'chat-message bot-message typing-indicator';
            typingDiv.innerHTML = `<span class="dot"></span><span class="dot"></span><span class="dot"></span>`;
            chatBody.appendChild(typingDiv);
            chatBody.scrollTop = chatBody.scrollHeight;

            try {
                // *** THIS IS THE ONLY LINE THAT CHANGED ***
                // We now point to the local Flask server's '/chat' endpoint.
                const response = await fetch('http://127.0.0.1:5000/chat', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ query })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const data = await response.json();

                typingDiv.remove();
                const botMsgDiv = document.createElement('div');
                botMsgDiv.className = 'chat-message bot-message';
                chatBody.appendChild(botMsgDiv);

                await typeText(botMsgDiv, data.reply, 10);

            } catch (err) {
                console.error("Fetch Error:", err);
                typingDiv.remove();
                const errorMsg = document.createElement('div');
                errorMsg.className = 'chat-message bot-message';
                errorMsg.textContent = "⚠️ Sorry, I'm having trouble connecting to the server. Make sure the Python script is running.";
                chatBody.appendChild(errorMsg);
            } finally {
                chatBody.scrollTop = chatBody.scrollHeight;
            }
        };

        chatSend.addEventListener('click', sendMessage);
        chatInput.addEventListener('keypress', e => {
            if (e.key === 'Enter') sendMessage();
        });
    });
    </script>
</body>
</html>