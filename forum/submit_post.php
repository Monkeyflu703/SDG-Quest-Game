<?php
session_start();
require_once 'config.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo 'You must be logged in';
    exit();
}

// CSRF check
if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    http_response_code(400);
    echo 'Invalid CSRF token';
    exit();
}

$content = trim($_POST['content'] ?? '');
$thread_id = intval($_POST['thread_id'] ?? 1);
$user_id = $_SESSION['user_id'];

if ($content === '') {
    exit();
}

/* -------- FUNCTIONS -------- */

function censorText($text) {
    $badWords = ['badword1', 'badword2'];

    foreach ($badWords as $word) {
        $pattern = '/\b' . preg_quote($word, '/') . '\b/i';
        $text = preg_replace($pattern, str_repeat('*', strlen($word)), $text);
    }

    return $text;
}

function containsBadWord($text) {
    $badWords = ['badword1', 'badword2'];

    foreach ($badWords as $word) {
        if (preg_match('/\b' . preg_quote($word, '/') . '\b/i', $text)) {
            return true;
        }
    }
    return false;
}

/* -------- CHECK TIMEOUT -------- */

$stmt = $conn->prepare("SELECT strikes, timeout_until FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

if ($user['timeout_until'] && strtotime($user['timeout_until']) > time()) {
    http_response_code(403);
    echo "You are timed out until " . $user['timeout_until'];
    exit();
}

/* -------- BAD WORD CHECK -------- */

if (containsBadWord($content)) {

    $stmt = $conn->prepare("UPDATE users SET strikes = strikes + 1 WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $stmt = $conn->prepare("SELECT strikes FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $userData = $stmt->get_result()->fetch_assoc();

    if ($userData['strikes'] >= 3) {
        $timeoutUntil = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        $stmt = $conn->prepare("UPDATE users SET timeout_until = ?, strikes = 0 WHERE id = ?");
        $stmt->bind_param("si", $timeoutUntil, $user_id);
        $stmt->execute();

        http_response_code(403);
        echo "You have been timed out for 10 minutes.";
        exit();
    }

    $content = censorText($content);
}

/* -------- INSERT MESSAGE -------- */

$stmt = $conn->prepare("INSERT INTO forum_posts (thread_id, user_id, content) VALUES (?, ?, ?)");
$stmt->bind_param("iis", $thread_id, $user_id, $content);
$stmt->execute();

?>