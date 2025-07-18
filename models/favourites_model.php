<?php
require_once '../config/database_handler.php';
require_once 'player_model.php';
require_once 'team_model.php';


function search_favourite_teams(object $pdo, string $search_input): array {
    $query = "SELECT id, team_name FROM teams WHERE team_name LIKE :search_input LIMIT 10;";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':search_input', "%$search_input%");
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function search_favourite_players(object $pdo, string $search_input): array {
    $query = "SELECT id, player_name FROM players WHERE player_name LIKE :search_input LIMIT 10;";
    $stmt = $pdo->prepare($query);
    $stmt->bindValue(':search_input', "%$search_input%");
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


function insert_favourite_team(object $pdo, int $user_id, int $team_id): string {
    $query = "SELECT COUNT(*) FROM user_favourite_teams WHERE user_id = :user_id AND team_id = :team_id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":team_id", $team_id);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        return "This team is already in your favourites!";
    }

    $query = "INSERT INTO user_favourite_teams (user_id, team_id) VALUES (:user_id, :team_id);";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":team_id", $team_id);
    $stmt->execute();

    return "Team added to favourites!";
}


function insert_favourite_player(object $pdo, int $user_id, int $player_id): string {
    $query = "SELECT COUNT(*) FROM user_favourite_players WHERE user_id = :user_id AND player_id = :player_id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":player_id", $player_id);
    $stmt->execute();

    if ($stmt->fetchColumn() > 0) {
        return "This player is already in your favourites!";
    }

    $query = "INSERT INTO user_favourite_players (user_id, player_id) VALUES (:user_id, :player_id);";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":player_id", $player_id);
    $stmt->execute();

    return "Player added to favourites!";
}



function get_favourite_teams(object $pdo, int $user_id): array {
    $query = "SELECT team_id FROM user_favourite_teams WHERE user_id = :user_id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();

    $team_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $favouriteTeams = [];
    foreach ($team_ids as $team_id) {
        $team_name = get_team_name_by_id($pdo, $team_id);
        if ($team_name) {
            $favouriteTeams[] = $team_name;
        }
    }

    return $favouriteTeams;
}



function get_favourite_players(object $pdo, int $user_id): array {
    $query = "SELECT player_id FROM user_favourite_players WHERE user_id = :user_id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();

    $player_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

    $favouritePlayers = [];
    foreach ($player_ids as $player_id) {
        $player_name = get_player_name_by_id($pdo, $player_id);
        if ($player_name) {
            $favouritePlayers[] = $player_name;
        }
    }

    return $favouritePlayers;
}

function delete_favourite_team(object $pdo, int $user_id, string $team_name): string {
    $team_id = get_team_id_by_name($pdo, $team_name);

    if (!$team_id) {
        return "Team not found.";
    }

    $query = "DELETE FROM user_favourite_teams WHERE user_id = :user_id AND team_id = :team_id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":team_id", $team_id);
    $stmt->execute();

    return "Team removed from favourites.";
}

function delete_favourite_player(object $pdo, int $user_id, string $player_name): string {
    $player_id = get_player_id_by_name($pdo, $player_name);

    if (!$player_id) {
        return "Player not found.";
    }

    $query = "DELETE FROM user_favourite_players WHERE user_id = :user_id AND player_id = :player_id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->bindParam(":player_id", $player_id);
    $stmt->execute();

    return "Player removed from favourites.";
}

function user_has_favourites(PDO $pdo, int $user_id): bool {
    $teamStmt = $pdo->prepare("SELECT COUNT(*) FROM user_favourite_teams WHERE user_id = :user_id");
    $teamStmt->execute([':user_id' => $user_id]);
    $teamCount = $teamStmt->fetchColumn();

    $playerStmt = $pdo->prepare("SELECT COUNT(*) FROM user_favourite_players WHERE user_id = :user_id");
    $playerStmt->execute([':user_id' => $user_id]);
    $playerCount = $playerStmt->fetchColumn();

    return ($teamCount > 0 || $playerCount > 0);
}


?>
