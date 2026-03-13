<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Faction War Leaderboard</title>
<link rel="stylesheet" href="leaderboard.css">
</head>
<body>

<h1>⚔️ Faction War Leaderboard</h1>

<div class="leaderboard-container">

    <div class="board">
        <h2>🌍 Global Factions</h2>
        <ul id="global-list"></ul>
    </div>

    <div class="board">
        <h2>🏳️ Your Faction</h2>
        <ul id="regional-list"></ul>
    </div>

</div>

<a class="back-btn" href="game.php">Back to Menu</a>

<script>
function updateGlobal() {
    fetch('get_global_leaderboard.php')
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('global-list');
            list.innerHTML = '';
            data.forEach((faction, index) => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <span class="rank">#${index + 1}</span>
                    <span class="name">${faction.name}</span>
                    <span class="score">${faction.total_score}</span>
                `;
                list.appendChild(li);
            });
        });
}

function updateRegional() {
    fetch('get_leaderboard.php')
        .then(res => res.json())
        .then(data => {
            const list = document.getElementById('regional-list');
            list.innerHTML = '';
            data.forEach((user, index) => {
                const li = document.createElement('li');
                li.innerHTML = `
                    <span class="rank">#${index + 1}</span>
                    <span class="name">${user.username}</span>
                    <span class="score">${user.score}</span>
                `;
                list.appendChild(li);
            });
        });
}

updateGlobal();
updateRegional();

setInterval(() => {
    updateGlobal();
    updateRegional();
}, 2000);
</script>

</body>
</html>