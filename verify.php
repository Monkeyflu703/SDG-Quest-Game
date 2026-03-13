<?php
session_start();
require_once 'config.php';

if (!isset($_GET['token'])) {
    die("Invalid verification link.");
}

$token = $_GET['token'];

// Check if token exists
$stmt = $conn->prepare("SELECT id FROM users WHERE verification_token = ?");
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    // Mark user as verified
    $stmt = $conn->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();

    // ✅ Set session so login modal shows automatically
    $_SESSION['alerts'][] = [
        'type' => 'success',
        'message' => 'Email verified! Please log in.'
    ];
    $_SESSION['active_form'] = 'login';

    // Show message and redirect
    echo "<!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <title>Email Verified</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                background-color: #f0f0f0;
                text-align: center;
            }
            .message-box {
                background: #fff;
                padding: 30px 50px;
                border-radius: 10px;
                box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            }
            .message-box h1 { color: #4CAF50; }
            .message-box p { margin-top: 15px; }
        </style>
        <script>
            // Redirect to index.php after 2.5 seconds
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 2500);
        </script>
    </head>
    <body>
        <div class='message-box'>
            <h1>Email Verified!</h1>
            <p>You will be redirected to login shortly...</p>
        </div>
    </body>
    </html>";

} else {
    echo "Invalid or expired token.";
}
?>