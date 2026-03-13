<?php
session_start();
if (!isset($_SESSION['name'])) {
    $_SESSION['redirect_after_login'] = 'sdg1.php';
    header('Location: index.php');
    exit();
}

$name = $_SESSION['name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>SDG Quest World</title>
<link rel="stylesheet" href="sdg1.css">
</head>
<body>
    <!-- START SCREEN -->
    <div id="startScreen">
        <h1>Welcome, <?= htmlspecialchars($name) ?>!</h1>
        <button id="startBtn">Enter World</button>
        <!-- Exit button for Start Screen -->
        <button id="exitStartBtn" class="icon-btn">Exit</button>
    </div>

    <!-- DEVICE SELECTION SCREEN (hidden by default) -->
    <div id="deviceSelect" style="display:none;">
        <h2>Select Your Device</h2>
        <button class="device-btn" data-device="desktop">🖥 Desktop</button>
        <button class="device-btn" data-device="mobile">📱 Mobile</button>
    </div>

    <!-- LOADING OVERLAY -->
    <div id="loadingOverlay">
        <div class="spinner"></div>
        <p>Loading world...</p>
    </div>

    <!-- GAME UI (hidden initially) -->
    <div id="gameUI" style="display:none;">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="currency">Coins: <span id="coinCount">0</span></div>
            <div class="top-buttons">
                <button id="questBtn" class="icon-btn">📜 Quests</button>
                <button id="optionsBtn" class="icon-btn">⚙ Options</button>
            </div>
        </div>

        <!-- Options Popup -->
        <div id="optionsPopup" class="popup">
            <div class="popup-content">
                <span id="closeOptions" class="close-btn">×</span>
                <h2>Options</h2>
                <button id="exitGameBtn" class="icon-btn">Exit Game</button>
            </div>
        </div>

        <!-- Main Area -->
        <div id="main-area">
            <h2>Click on the Quest Button to play and earn points!</h2>
            <div id="gameContainer"></div>
        </div>
    </div>

    <!-- QUEST POPUP -->
    <div id="questPopup" class="popup">
    <div class="popup-content">
        <span class="close-btn" id="closePopup">&times;</span>
        <h2>Active Quests</h2>
        <ul></ul> <!-- EMPTY now, JS will populate -->
    </div>
</div>

    <script src="sdg1.js"></script>
</body>
</html>