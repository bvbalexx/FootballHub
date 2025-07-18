<?php


require_once '../models/team_model.php';
require_once '../models/league_standings_model.php';

function fetch_and_insert_league_standings(PDO $pdo): void {


    $apiKey = 'c009eea617d44469a59bc7a300c99ca4';
    $leagueCodes = ['PL', 'BL1', 'SA', 'FL1', 'PD'];
    $baseUrl = 'https://api.football-data.org/v4/competitions/';

    foreach ($leagueCodes as $leagueCode) {
        $url = $baseUrl . $leagueCode . '/standings';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => ["X-Auth-Token: $apiKey"]
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        if ($response === false) continue;

        $data = json_decode($response, true);

        if (!isset($data['standings'][0]['table'])) continue;

        $seasonYear = substr($data['season']['startDate'], 0, 4);

        delete_standings_by_league($pdo, $leagueCode);

        foreach ($data['standings'][0]['table'] as $entry) {
            $teamName = $entry['team']['name'];
            $teamId = get_team_id_by_name($pdo, $teamName);
            if (!$teamId) continue;

            insert_league_standing(
                $pdo,
                $leagueCode,
                (int)$seasonYear,
                $teamId,
                $entry['position'],
                (int)$entry['playedGames'],
                (int)$entry['won'],
                (int)$entry['draw'],
                (int)$entry['lost'],
                (int)$entry['points'],
                (int)$entry['goalsFor'],
                (int)$entry['goalsAgainst'],
                (int)$entry['goalDifference'],

            );
        }

        update_league_last_updated($pdo, $leagueCode);
    }
}

?>
