<?php
require_once '../config/database_handler.php';

function insert_league_standing(PDO $pdo, string $league_code, int $season_year, int $team_id, int $position, int $played_games, int $won, int $draw, int $lost, int $points, int $goals_for, int $goals_against, int $goal_difference): bool {
    $query = "INSERT INTO league_standings (
        league_code, season_year, team_id, position, played_games, won, draw, lost, points,
        goals_for, goals_against, goal_difference, last_updated
    ) VALUES (
        :league_code, :season_year, :team_id, :position, :played_games, :won, :draw, :lost, :points,
        :goals_for, :goals_against, :goal_difference, :last_updated
    )";

    $stmt = $pdo->prepare($query);

    return $stmt->execute([
        ':league_code'     => $league_code,
        ':season_year'     => $season_year,
        ':team_id'         => $team_id,
        ':position'        => $position,
        ':played_games'    => $played_games,
        ':won'             => $won,
        ':draw'            => $draw,
        ':lost'            => $lost,
        ':points'          => $points,
        ':goals_for'       => $goals_for,
        ':goals_against'   => $goals_against,
        ':goal_difference' => $goal_difference,
        ':last_updated'    => date('Y-m-d')
    ]);
}

function delete_standings_by_league(PDO $pdo, string $league_code): bool {
    $query = "DELETE FROM league_standings WHERE league_code = :league_code";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([':league_code' => $league_code]);
}

function update_league_last_updated(PDO $pdo, string $league_code): void {
    $stmt = $pdo->prepare("UPDATE league_standings SET last_updated = CURDATE() WHERE league_code = :league_code");
    $stmt->execute([':league_code' => $league_code]);
}

function should_update_league_today(PDO $pdo): bool {
    $stmt = $pdo->query("SELECT last_updated FROM league_standings LIMIT 1");
    $lastUpdate = $stmt->fetchColumn();

    return $lastUpdate !== date('Y-m-d');
}

function get_standings_by_league(PDO $pdo, string $league_code): array {
    $stmt = $pdo->prepare("SELECT * FROM league_standings WHERE league_code = :league_code ORDER BY points DESC, goal_difference DESC, goals_for DESC");
    $stmt->execute([':league_code' => $league_code]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
