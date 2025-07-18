<?php

session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../pages/login.php?error=not_logged_in");
    exit();
}



 ?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>FootballHub - Players </title>
    <link rel="stylesheet" href="../css/players.css">



  </head>
  <body>

    <nav class="navbar">
        <div class="container">
            <a href="home.php" class="brand">FootballHub</a>
            <ul class="nav-links">
                <li><a href="standings.php">Standings</a></li>
                <li><a href="players.php" class="active">Players</a></li>
                <li><a href="teams.php">Teams</a></li>
                <li><a href="quizzes.php">Quizzes</a></li>
                <li><a href="favourites.php">Favourites</a></li>
            </ul>
            <div class="user-area">
                <a href="profile.php"><?php echo htmlspecialchars($_SESSION["username"]);?></a>
                <a href="logout.php" class="btn-logout">Sign Out</a>
            </div>
        </div>
    </nav>

    <div class="player-search-box">
      <h2>Search a player</h2>
        <div class="search-wrapper">
          <input type="text" id="player-search" placeholder="Search for a player...">
          <ul id="suggestions" class="suggestions-list"></ul>
        </div>
      </div>

      <div id="player-details-container" class="hidden"></div>



    <script src="../js/players.js"></script>

  </body>
</html>
