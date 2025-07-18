<?php
require_once '../config/database_handler.php';


$playerStmt = $pdo->query("SELECT name FROM players");
$players = $playerStmt->fetchAll(PDO::FETCH_COLUMN);

echo json_encode($players);
