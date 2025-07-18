<?php
require_once '../config/database_handler.php';
require_once '../models/top_scorers_model.php';
require_once '../models/player_model.php';
require_once '../models/team_model.php';

function fetch_and_insert_top_scorers(PDO $pdo): void {
    $leagues = ["PL", "BL1", "PD", "SA", "FL1"];
    $token = "c009eea617d44469a59bc7a300c99ca4";

    foreach ($leagues as $league_code) {
        delete_top_scorers_by_league($pdo, $league_code);

        $url = "https://api.football-data.org/v4/competitions/$league_code/scorers";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "X-Auth-Token: $token"
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        if (!$response) continue;

        $data = json_decode($response, true);
        $scorers = $data["scorers"] ?? [];

        foreach ($scorers as $scorer) {
            $player_name = $scorer["player"]["name"];
            $player_id = get_player_id_by_name($pdo, $player_name);

            $team_name = $scorer["team"]["name"];
            $team_id = get_team_id_by_name($pdo, $team_name);

            if (!$player_id || !$team_id) continue;

            $playedMatches = (int)$scorer["playedMatches"];
            $goals = (int)$scorer["goals"];
            $assists = (int)$scorer["assists"];
            $penalties = (int)$scorer["penalties"];

            insert_top_scorer($pdo, $league_code, $player_id, $team_id, $playedMatches, $goals, $assists, $penalties);
        }

        update_top_scorers_last_updated($pdo, $league_code);
    }
}
