<?php
require_once 'database_handler.php';
require_once '../models/player_model.php';
require_once '../models/team_model.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['name'])) {
    $name = trim($_GET['name']);
    $player = get_player_details_by_name($pdo, $name);

    if ($player) {
        $teamName = get_team_name_by_id($pdo, (int)$player['team_id']);
        $teamCrest = get_team_emblem_by_player_id($pdo, (int)$player['id']);

        $player['team_name'] = $teamName ?? 'Unknown';
        $player['team_crest'] = $teamCrest ?? null;
    }

    header('Content-Type: application/json');
    echo json_encode($player ?? ['error' => 'Player not found']);
}
