<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../pages/login.php?error=not_logged_in");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>FootballHub - Teams</title>
    <link rel="stylesheet" href="../css/teams.css">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="home.php" class="brand">FootballHub</a>
        <ul class="nav-links">
            <li><a href="standings.php">Standings</a></li>
            <li><a href="players.php">Players</a></li>
            <li><a href="teams.php" class="active">Teams</a></li>
            <li><a href="quizzes.php">Quizzes</a></li>
            <li><a href="favourites.php">Favourites</a></li>
        </ul>
        <div class="user-area">
            <a href="profile.php"><?= htmlspecialchars($_SESSION["username"]) ?></a>
            <a href="logout.php" class="btn-logout">Sign Out</a>
        </div>
    </div>
</nav>

<div class="team-search-box">
    <h2>Search a team</h2>
    <div class="search-wrapper">
        <input type="text" id="team-search" placeholder="Search for a team...">
        <ul id="team-suggestions" class="suggestions-list"></ul>
    </div>
</div>

<div id="team-details-container" class="hidden"></div>



<script src="../js/teams.js"></script>
</body>
</html>
