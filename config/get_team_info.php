<?php
require_once '../config/database_handler.php';
require_once '../models/coach_model.php';

if (isset($_GET['name'])) {
    $name = trim($_GET['name']);

    $stmt = $pdo->prepare("
        SELECT t.*, l.name AS league_name
        FROM teams t
        JOIN leagues l ON t.league_id = l.id
        WHERE t.name = :name
        LIMIT 1
    ");
    $stmt->execute([':name' => $name]);
    $team = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($team) {
        echo json_encode($team);
    } else {
        echo json_encode(['error' => 'Team not found']);
    }
}

// $team_data['coach_info'] = get_coach_by_team_id($pdo, $team_data['id']);
