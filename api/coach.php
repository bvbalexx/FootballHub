<?php
set_time_limit(0);

require_once 'config/database_handler.php';
require_once 'models/coach_model.php';

$auth_token = "c009eea617d44469a59bc7a300c99ca4";

$url_pl = "https://api.football-data.org/v4/competitions/PL/teams";
$url_bl1 = "https://api.football-data.org/v4/competitions/BL1/teams";
$url_fl1 = "https://api.football-data.org/v4/competitions/FL1/teams";
$url_sa = "https://api.football-data.org/v4/competitions/SA/teams";
$url_pd = "https://api.football-data.org/v4/competitions/PD/teams";

function get_json_data_coaches(string $url, string $auth_token): array {


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

function process_coaches_and_insert(object $pdo, array $coaches_data): void {
  foreach ($coaches_data['teams'] as $team) {

       if (isset($team['coach'])) {
           $coach = $team['coach'];


           $coach_name = $coach['name'];
           $date_of_birth = $coach['dateOfBirth'];
           $nationality = $coach['nationality'];
           $team_name = $team['name'];
           $contract_start = $coach['contract']['start'];
           $contract_until = $coach['contract']['until'];


           $team_id = get_team_id_by_name($pdo, $team_name);


           if ($team_id !== null) {
               insert_coach($pdo, $coach_name, $date_of_birth, $nationality, $team_id, $contract_start, $contract_until);
               echo "Antrenorul $coach_name a fost adăugat cu succes pentru echipa $team_name!<br>" . PHP_EOL;
           } else {
               echo "Echipa $team_name nu a fost găsită pentru antrenorul $coach_name!<br>" . PHP_EOL;
           }
       } else {
           echo "Nu există antrenor pentru echipa " . $team['name'] . "<br>" . PHP_EOL;
       }
   }
}


 ?>
