<?php
session_start();
require 'config.php'; // DB connection

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

// Get JSON payload
$data = json_decode(file_get_contents('php://input'), true);

$user_id = $_SESSION['user_id'];
$sdg_number = $data['sdg_number'] ?? 1;

// Fetch current values
$stmt = $conn->prepare("SELECT * FROM sdg_progress WHERE user_id=? AND sdg_number=?");
$stmt->bind_param("ii", $user_id, $sdg_number);
$stmt->execute();
$result = $stmt->get_result();

$current = $result->fetch_assoc();

$coins = $data['coins'] ?? ($current['coins'] ?? 0);
$quest1_completed = $data['quest1_completed'] ?? ($current['quest1_completed'] ?? 0);
$quest2_completed = $data['quest2_completed'] ?? ($current['quest2_completed'] ?? 0);
$quest1_cooldown_end = $data['quest1_cooldown_end'] ?? ($current['quest1_cooldown_end'] ?? 0);
$quest2_cooldown_end = $data['quest2_cooldown_end'] ?? ($current['quest2_cooldown_end'] ?? 0);

if ($result->num_rows > 0) {
    // Update existing row
    $stmt = $conn->prepare("UPDATE sdg_progress SET coins=?, quest1_completed=?, quest2_completed=?, quest1_cooldown_end=?, quest2_cooldown_end=? WHERE user_id=? AND sdg_number=?");
    $stmt->bind_param("iiiiiii", $coins, $quest1_completed, $quest2_completed, $quest1_cooldown_end, $quest2_cooldown_end, $user_id, $sdg_number);
    $stmt->execute();
} else {
    // Insert new row
    $stmt = $conn->prepare("INSERT INTO sdg_progress (user_id, sdg_number, coins, quest1_completed, quest2_completed, quest1_cooldown_end, quest2_cooldown_end) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiiiiii", $user_id, $sdg_number, $coins, $quest1_completed, $quest2_completed, $quest1_cooldown_end, $quest2_cooldown_end);
    $stmt->execute();
}

echo json_encode(['success' => true]);
?>