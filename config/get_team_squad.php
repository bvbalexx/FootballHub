<?php
require_once '../config/database_handler.php';
require_once '../models/player_model.php';

header('Content-Type: application/json');

if (isset($_GET['team'])) {
    $team = trim($_GET['team']);
    $players = get_players_by_team_name($pdo, $team);
    echo json_encode($players);
}
