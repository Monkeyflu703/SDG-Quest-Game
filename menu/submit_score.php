<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['name'];
$points = intval($_POST['points'] ?? 0);

if ($points <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid points']);
    exit();
}

// Check if user exists
$result = $conn->query("SELECT id FROM leaderboard WHERE user_id = $user_id");

if ($result->num_rows > 0) {

    // ✅ ADD points to existing score
    $stmt = $conn->prepare("
        UPDATE leaderboard 
        SET score = score + ?, last_update = NOW() 
        WHERE user_id = ?
    ");
    $stmt->bind_param("ii", $points, $user_id);
    $stmt->execute();

} else {

    // ✅ Insert new user with starting score
    $stmt = $conn->prepare("
        INSERT INTO leaderboard (user_id, username, score) 
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("isi", $user_id, $username, $points);
    $stmt->execute();
}

// Get user's faction
$result = $conn->query("SELECT faction_id FROM users WHERE id = $user_id");
$row = $result->fetch_assoc();
$faction_id = $row['faction_id'];

if ($faction_id) {
    $stmt = $conn->prepare("
        UPDATE factions 
        SET total_score = total_score + ? 
        WHERE id = ?
    ");
    $stmt->bind_param("ii", $points, $faction_id);
    $stmt->execute();
}

echo json_encode(['status' => 'success']);
?>