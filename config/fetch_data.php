<?php
require_once '../config/database_handler.php';


$teamStmt = $pdo->query("SELECT name FROM teams");
$teams = $teamStmt->fetchAll(PDO::FETCH_COLUMN);


$playerStmt = $pdo->query("SELECT name FROM players");
$players = $playerStmt->fetchAll(PDO::FETCH_COLUMN);


header('Content-Type: application/json');
echo json_encode(['teams' => $teams, 'players' => $players]);
?>
