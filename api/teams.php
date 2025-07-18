<?php

set_time_limit(0);

require_once 'config/database_handler.php';
require_once 'models/team_model.php';

$auth_token = "c009eea617d44469a59bc7a300c99ca4";

$url_pl = "https://api.football-data.org/v4/competitions/PL/teams";
$url_bl1 = "https://api.football-data.org/v4/competitions/BL1/teams";
$url_fl1 = "https://api.football-data.org/v4/competitions/FL1/teams";
$url_sa = "https://api.football-data.org/v4/competitions/SA/teams";
$url_pd = "https://api.football-data.org/v4/competitions/PD/teams";

function get_json_data_teams(string $url, string $auth_token): array {


    $ch = curl_init();


    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-Auth-Token: $auth_token"
    ]);


    $response = curl_exec($ch);


    if(curl_errno($ch)) {
        echo 'Error:' . curl_error($ch);
    }


    curl_close($ch);

    return json_decode($response, true);
}

function process_teams_and_insert(object $pdo, array $teams_data): void {
    foreach ($teams_data['teams'] as $team) {

        $name = $team['name'];
        $founded_year = $team['founded'];
        $tla = $team['tla'];
        $club_colors = $team['clubColors'];
        $venue = $team['venue'];
        $coach = isset($team['coach']['name']) ? $team['coach']['name'] : 'N/A';


        insert_team($pdo, $name, $founded_year, $tla, $club_colors, $venue, $coach);


        echo "Echipa $name a fost adăugată cu succes!<br>" . PHP_EOL;
    }
}
