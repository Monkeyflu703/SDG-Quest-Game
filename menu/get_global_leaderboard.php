<?php
require_once __DIR__ . '/../config.php';

$result = $conn->query("
    SELECT name, total_score 
    FROM factions 
    ORDER BY total_score DESC 
    LIMIT 10
");

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
?>