<?php
// chatbot.php

// 1. Set headers for JSON response
header('Content-Type: application/json');

// 2. Read the JSON input from the frontend
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Get the user's query
$query = $data['query'] ?? '';

// Check if a query was provided
if (empty($query)) {
    echo json_encode(['reply' => 'Please ask a question.']);
    exit;
}

// 3. Define the command to execute the Python script
// IMPORTANT: You might need to change 'python3' to 'python' depending on your server.
$python_command = "python3 chat_with_db.py " . escapeshellarg($query);

// 4. Execute the Python script
$output = shell_exec($python_command);

// 5. Check if the Python script ran successfully and returned data
if ($output === null) {
    $reply = "⚠️ Connection Error: Failed to execute the Python script. Check the command in `chatbot.php` and if Python is installed on your server.";
} else {
    // In chat_with_db.py, the final answer is always printed last, 
    // preceded by "🤖: ". We need to extract that final answer.
    $lines = explode("\n", trim($output));
    $last_line = end($lines);

    // Look for the clean AI response format
    if (strpos($last_line, '🤖:') !== false) {
        $reply = trim(str_replace('🤖:', '', $last_line));
    } else {
        // Fallback or error message capture
        $reply = "Error/Raw Output: " . $output;
    }
}

// 6. Send the AI's reply back to the frontend
echo json_encode(['reply' => $reply]);
?>