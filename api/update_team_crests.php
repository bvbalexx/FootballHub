<?php
require_once '../config/database_handler.php';

$api_token = 'c009eea617d44469a59bc7a300c99ca4';

$urls = [
    "https://api.football-data.org/v4/competitions/PL/teams",
    "https://api.football-data.org/v4/competitions/BL1/teams",
    "https://api.football-data.org/v4/competitions/FL1/teams",
    "https://api.football-data.org/v4/competitions/SA/teams",
    "https://api.football-data.org/v4/competitions/PD/teams"
];

$options = [
    "http" => [
        "header" => "X-Auth-Token: $api_token"
    ]
];

$context = stream_context_create($options);

foreach ($urls as $url) {
    $response = file_get_contents($url, false, $context);
    $data = json_decode($response, true);

    if (!isset($data['teams'])) {
        echo "Eroare: lipsesc echipele pentru $url<br>";
        continue;
    }

    foreach ($data['teams'] as $team) {
        $name = $team['name'];
        $crest = $team['crest'];


        $stmt = $pdo->prepare("UPDATE teams SET crest_url = :crest WHERE name = :name");
        $stmt->execute([
            ':crest' => $crest,
            ':name' => $name
        ]);

        echo "✅ Updated crest for <strong>$name</strong><br>";
    }
}
