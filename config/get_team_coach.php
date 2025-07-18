<?php
require_once '../config/database_handler.php';
require_once '../models/coach_model.php';

header('Content-Type: application/json');

if (isset($_GET['team'])) {
    $team = trim($_GET['team']);
    $coach = get_coach_by_team_name($pdo, $team);

    if ($coach) {
        echo json_encode($coach);
    } else {
        echo json_encode(['error' => 'Coach not found']);
    }
} else {
    echo json_encode(['error' => 'Team name not provided']);
}
