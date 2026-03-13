<?php
session_start();

// Generate CSRF token if it doesn't exist
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Only allow logged-in users
if (!isset($_SESSION['name']) || !isset($_SESSION['user_id'])) {
    $_SESSION['active_form'] = 'login';
    $_SESSION['alerts'][] = [
        'type' => 'error',
        'message' => 'You must log in to access the forum'
    ];
    header('Location: ../index.php');
    exit();
}

// Logged-in user info
$name = $_SESSION['name'];
$user_id = $_SESSION['user_id'];
$thread_id = 1; // default thread
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Forum</title>
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="forum.css">
</head>
<body>

<h1>Forum: General Discussion</h1>

<body>

<div class="forum-wrapper">

    <!-- Categories -->
    <aside class="forum-categories">
        <h3>Categories</h3>
        <ul id="categories-list"></ul>
    </aside>

    <!-- Threads -->
    <aside class="forum-threads">
        <h3>Threads</h3>
        <ul id="threads-list"></ul>
    </aside>

    <!-- Chat -->
    <main class="forum-chat">

        <div id="posts"></div>

        <form id="new-post-form" method="POST">
            <textarea name="content" placeholder="Write a message..." required></textarea>
            <input type="hidden" name="thread_id" value="<?= $thread_id ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
            <button type="submit">Send</button>
        </form>

    </main>

</div>

<script src="forum.js"></script>
</body>
</html>