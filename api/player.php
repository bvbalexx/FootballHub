<?php

set_time_limit(0);

require_once 'config/database_handler.php';
require_once 'models/player_model.php';
require_once 'models/team_model.php';

$auth_token = "c009eea617d44469a59bc7a300c99ca4";

$url_player = "https://api.football-data.org/v4/persons/";

$url_pl = "https://api.football-data.org/v4/competitions/PL/teams";
$url_bl1 = "https://api.football-data.org/v4/competitions/BL1/teams";
$url_fl1 = "https://api.football-data.org/v4/competitions/FL1/teams";
$url_sa = "https://api.football-data.org/v4/competitions/SA/teams";
$url_pd = "https://api.football-data.org/v4/competitions/PD/teams";



function get_json_data_players(string $url, string $auth_token): array {

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


function procces_players_and_insert(object $pdo, array $players_data ): void{

  if (isset($players_data['teams'])) {
        foreach ($players_data['teams'] as $team) {

            $teamName = $team['name'];

            $team_id = get_team_id_by_name($pdo, $teamName);


            $auth_token = "c009eea617d44469a59bc7a300c99ca4";
            $url_player = "https://api.football-data.org/v4/persons/";

            if (isset($team['squad'])) {
                foreach ($team['squad'] as $player) {


                  $playerId = isset($player['id']) ? $player['id'] : "Unknown";

                  $playerName = isset($player['name']) && !empty($player['name']) ? $player['name'] : "Unknown";

                  $position = isset($player['position']) && !empty($player['position']) ? $player['position'] : "Unknown";

                  $nationality = isset($player['nationality']) && !empty($player['nationality']) ? $player['nationality'] : "Unknown";

                  $birth_date = isset($player['dateOfBirth']) && !empty($player['dateOfBirth']) ? $player['dateOfBirth'] : "Unknown";



                        $url = $url_player . $playerId;

                        $playerDetails = get_player_details($url, $auth_token);

                        $shirtNumber = $playerDetails['shirtNumber'];
                        $contract_start = $playerDetails['contract_start'];
                        $contract_end = $playerDetails['contract_end'];

                        create_player($pdo, $playerName, $team_id, $nationality, $position, $shirtNumber, $contract_start, $contract_end, $birth_date );

                        usleep(6100000);

                }
            }
        }
    }
}


function get_player_shirt_number(string $url, string $auth_token): int {


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

  $data = json_decode($response, true);

  if (isset($data['shirtNumber']) && is_numeric($data['shirtNumber'])) {
       return (int) $data['shirtNumber'];
   } else {
       return -1;
   }

}

function get_player_contract_start(string $url, string $auth_token): string {

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

  $data = json_decode($response, true);

  if (isset($data['currentTeam']['contract']['start']) && !empty($data['currentTeam']['contract']['start'])) {
       return (string) $data['currentTeam']['contract']['start'];
   } else {
       return (string) "Unknown";
   }




}

function get_player_contract_until(string $url, string $auth_token): string {

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

  $data = json_decode($response, true);

  if (isset($data['currentTeam']['contract']['until']) && !empty($data['currentTeam']['contract']['until'])) {
       return (string) $data['currentTeam']['contract']['until'];
   } else {
       return (string) "Unknown";
   }




}

function get_player_details(string $url, string $auth_token): array {

    $ch = curl_init();

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "X-Auth-Token: $auth_token"
    ]);

    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        echo 'Error: ' . curl_error($ch);
        curl_close($ch);


        return [
            'shirtNumber' => -1,
            'contract_start' => "Unknown",
            'contract_end' => "Unknown"
        ];
    }

    curl_close($ch);

    $data = json_decode($response, true);


    $shirtNumber = isset($data['shirtNumber']) && is_numeric($data['shirtNumber'])
        ? (int) $data['shirtNumber']
        : -1;

    $contract_start = $data['currentTeam']['contract']['start'] ?? "Unknown";
    $contract_end = $data['currentTeam']['contract']['until'] ?? "Unknown";


    return [
        'shirtNumber' => $shirtNumber,
        'contract_start' => $contract_start,
        'contract_end' => $contract_end
    ];
}




?>
