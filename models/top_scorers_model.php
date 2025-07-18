<?php
require_once '../config/database_handler.php';

function insert_top_scorer(PDO $pdo, string $league_code, int $player_id, int $team_id, int $played_matches, int $goals, int $assists, int $penalties): bool {
    $query = "INSERT INTO top_scorers (
        league_code, player_id, team_id, played_matches, goals, assists, penalties, last_updated
    ) VALUES (
        :league_code, :player_id, :team_id, :played_matches, :goals, :assists, :penalties, :last_updated
    )";

    $stmt = $pdo->prepare($query);
    return $stmt->execute([
        ':league_code'     => $league_code,
        ':player_id'       => $player_id,
        ':team_id'         => $team_id,
        ':played_matches'  => $played_matches,
        ':goals'           => $goals,
        ':assists'         => $assists,
        ':penalties'       => $penalties,
        ':last_updated'    => date('Y-m-d')
    ]);
}

function delete_top_scorers_by_league(PDO $pdo, string $league_code): bool {
    $stmt = $pdo->prepare("DELETE FROM top_scorers WHERE league_code = :league_code");
    return $stmt->execute([':league_code' => $league_code]);
}

function should_update_top_scorers_today(PDO $pdo): bool {
    $stmt = $pdo->query("SELECT last_updated FROM top_scorers LIMIT 1");
    $lastUpdate = $stmt->fetchColumn();
    return $lastUpdate !== date('Y-m-d');
}

function get_top_scorers_by_league(PDO $pdo, string $league_code): array {
    $stmt = $pdo->prepare("SELECT * FROM top_scorers WHERE league_code = :league_code ORDER BY goals DESC, assists DESC, penalties ASC");
    $stmt->execute([':league_code' => $league_code]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function update_top_scorers_last_updated(PDO $pdo, string $league_code): void {
    $stmt = $pdo->prepare("UPDATE top_scorers SET last_updated = CURDATE() WHERE league_code = :league_code");
    $stmt->execute([':league_code' => $league_code]);
}

 ?>
