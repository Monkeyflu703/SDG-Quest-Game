<?php
session_start();
require_once 'config.php';

$thread_id = $_GET['thread_id'] ?? 1;

$stmt = $conn->prepare("
    SELECT p.content, p.created_at, u.name AS username 
    FROM forum_posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.thread_id = ?
    ORDER BY p.created_at ASC
");
$stmt->bind_param("i", $thread_id);
$stmt->execute();
$result = $stmt->get_result();

$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}

header('Content-Type: application/json');
echo json_encode($posts);

?>