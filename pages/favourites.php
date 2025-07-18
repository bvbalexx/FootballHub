<?php
session_start();
require_once '../config/database_handler.php';
require_once '../models/team_model.php';
require_once '../models/player_model.php';
require_once '../models/favourites_model.php';


if (!isset($_SESSION["user_id"])) {
    header("Location: ../pages/login.php?error=not_logged_in");
    exit();
}


$user_id = $_SESSION["user_id"];

$favourite_players = get_favourite_players($pdo, $user_id);
$favourite_teams = get_favourite_teams($pdo, $user_id);


if (isset($_GET["fetch_favourites"]) && isset($_GET["type"])) {
    header("Content-Type: application/json");
    $type = $_GET["type"];
    if ($type === "team") {
        echo json_encode(get_favourite_teams($pdo, $user_id));
    } elseif ($type === "player") {
        echo json_encode(get_favourite_players($pdo, $user_id));
    }
    exit();
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    header("Content-Type: application/json");

    $action = $_POST["action"];
    $name = trim($_POST["name"] ?? '');
    $category = $_POST["category"] ?? '';

    if ($action === "delete" && $name && $category) {
        if ($category === "team") {
            $message = delete_favourite_team($pdo, $user_id, $name);
            echo json_encode(["status" => "success", "message" => $message]);
        } elseif ($category === "player") {
            $message = delete_favourite_player($pdo, $user_id, $name);
            echo json_encode(["status" => "success", "message" => $message]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid category."]);
        }
        exit();
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["name"], $_POST["category"])) {
    header("Content-Type: application/json");

    $name = trim($_POST["name"]);
    $category = $_POST["category"];

    if ($category === "team") {

      $existingTeams = get_favourite_teams($pdo, $user_id);
       if (count($existingTeams) >= 20) {
           echo json_encode(["status" => "error", "message" => "You can only have up to 20 favourite teams."]);
           exit();
       }

        $team_id = get_team_id_by_name($pdo, $name);
        if ($team_id) {
            $message = insert_favourite_team($pdo, $user_id, $team_id);
            echo json_encode(["status" => "success", "message" => $message]);
        } else {
            echo json_encode(["status" => "error", "message" => "Team not found in database."]);
        }
        
    } elseif ($category === "player") {

      $existingPlayers = get_favourite_players($pdo, $user_id);
     if (count($existingPlayers) >= 20) {
         echo json_encode(["status" => "error", "message" => "You can only have up to 20 favourite players."]);
         exit();
     }
        $player_id = get_player_id_by_name($pdo, $name);
        if ($player_id) {
            $message = insert_favourite_player($pdo, $user_id, $player_id);
            echo json_encode(["status" => "success", "message" => $message]);
        } else {
            echo json_encode(["status" => "error", "message" => "Player not found in database."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid category."]);
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FootballHub - Favourites</title>
    <link rel="stylesheet" href="../css/favourites.css">
</head>
<body>


    <nav class="navbar">
        <div class="container">
            <a href="home.php" class="brand">FootballHub</a>
            <ul class="nav-links">
                <li><a href="standings.php">Standings</a></li>
                <li><a href="players.php">Players</a></li>
                <li><a href="teams.php">Teams</a></li>
                <li><a href="quizzes.php">Quizzes</a></li>
                <li><a href="favourites.php" class="active">Favourites</a></li>
            </ul>
            <div class="user-area">
                <a href="profile.php"><?php echo htmlspecialchars($_SESSION["username"]);?></a>
                <a href="logout.php" class="btn-logout">Sign Out</a>
            </div>
        </div>
    </nav>


    <div class="container-favourites">
        <h1>Your Favourites</h1>


        <div class="tabs">
            <button class="tab-button active">Teams</button>
            <button class="tab-button">Players</button>
        </div>


        <div class="search-container">
            <input type="text" id="searchInput" placeholder="Search for teams or players..." onkeyup="handleSearch()">
            <div id="suggestions"></div>
        </div>


        <div id="teams-section" class="favourites-list">
            <h2>Your Favourite Teams<span id="team-count">(0/20)</span></h2>

            <div id="team-message" class="favourite-message"></div>

            <ul id="favourite-teams">
                <?php if (!empty($favourite_teams)): ?>
                    <?php foreach ($favourite_teams as $team_name): ?>
                        <li><?php echo htmlspecialchars($team_name); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No favourite teams yet.</p>
                <?php endif; ?>
            </ul>
        </div>


        <div id="players-section" class="favourites-list" style="display: none;">
            <h2>Your Favourite Players<span id="player-count">(0/20)</span></h2>
            <div id="player-message" class="favourite-message"></div>

            <ul id="favourite-players">
                <?php if (!empty($favourite_players)): ?>
                    <?php foreach ($favourite_players as $player_name): ?>
                        <li><?php echo htmlspecialchars($player_name); ?></li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No favourite players yet.</p>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <script src="../js/favourites.js"></script>

</body>
</html>
