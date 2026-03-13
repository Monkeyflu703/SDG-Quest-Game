<?php
session_start();
require_once __DIR__ . '/../config.php';

// ----------------------
// CHECK LOGIN
// ----------------------
if (!isset($_SESSION['name'])) {
    $_SESSION['redirect_after_login'] = 'menu/game.php';
    $_SESSION['active_form'] = 'login';
    $_SESSION['alerts'][] = [
        'type' => 'error',
        'message' => 'You must log in to play the game.'
    ];
    header('Location: /login_registration/index.php');
    exit();
}

// ----------------------
// FETCH USER DATA
// ----------------------
$user_id = $_SESSION['user_id'] ?? 0;

$name = 'Player'; // default fallback
$faction_id = null;

if ($user_id) {
    $result = $conn->query("SELECT name, faction_id FROM users WHERE id = $user_id");
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $name = $user['name'] ?? 'Player';
        $faction_id = $user['faction_id'] ?? null;
    }
}

// ----------------------
// CHECK FACTION
// ----------------------
if (empty($faction_id)) {
    $_SESSION['alerts'][] = [
        'type' => 'info',
        'message' => 'Please choose a faction to continue.'
    ];
    header('Location: ../faction/choose_faction.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>SDG Game</title>
<link rel="stylesheet" href="game-menu.css">
<style>
    body { font-family: sans-serif; text-align: center; }
    button { margin: 10px; padding: 12px 25px; font-size: 16px; }
</style>
</head>
<body>
<h1>Welcome, <?= htmlspecialchars($name) ?>!</h1>

<div class="menu">
    <button id="startGameBtn">Start Game</button>
    <button onclick="window.location.href='leaderboard.php'">Leaderboard</button>
    <button onclick="window.location.href='credits.php'">Credits</button>
    <button onclick="window.location.href='/login_registration/index.php'">Exit</button>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay">
    <div class="loader-container">
        <div class="spinner"></div>
        <p>Loading game...</p>
    </div>
</div>

<script src="game-menu.js"></script>
</body>
</html>