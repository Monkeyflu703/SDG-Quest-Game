<?php
session_start();
require_once __DIR__ . '/../config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../index.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check again (safety check)
$result = $conn->query("SELECT faction_id FROM users WHERE id = $user_id");
$user = $result->fetch_assoc();

if (!empty($user['faction_id'])) {
    header("Location: ../menu/game.php");
    exit();
}

$factions = $conn->query("SELECT * FROM factions ORDER BY name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Choose Your Faction</title>
    <link rel="stylesheet" href="choose_faction.css">
</head>
<body>

<h1>Choose Your Faction ⚔️</h1>
<p>This choice cannot be changed later.</p>

<?php while ($f = $factions->fetch_assoc()): ?>
    <form method="POST" action="set_faction.php">
        <input type="hidden" name="faction_id" value="<?= $f['id'] ?>">
        <button type="submit" class="faction-btn">
            <?= htmlspecialchars($f['name']) ?>
        </button>
    </form>
<?php endwhile; ?>

</body>
</html>