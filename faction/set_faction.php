<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$faction_id = intval($_POST['faction_id'] ?? 0);

if ($faction_id <= 0) {
    header("Location: choose_faction.php");
    exit();
}

// Set faction only if not already set
$stmt = $conn->prepare("
    UPDATE users 
    SET faction_id = ? 
    WHERE id = ? AND faction_id IS NULL
");
$stmt->bind_param("ii", $faction_id, $user_id);
$stmt->execute();

header("Location: ../menu/game.php");
exit();