<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Get user's faction
$result = $conn->query("SELECT faction_id FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();

if (!$user || !$user['faction_id']) {
    echo json_encode([]);
    exit();
}

$faction_id = $user['faction_id'];

// Get top players in same faction
$stmt = $conn->prepare("
    SELECT username, score 
    FROM leaderboard 
    WHERE faction_id = ? 
    ORDER BY score DESC 
    LIMIT 10
");
$stmt->bind_param("i", $faction_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>