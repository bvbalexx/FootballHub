<?php


declare(strict_types=1);

function insert_player(object $pdo, string $name, int $team_id, string $nationality, string $position,
int $shirt_number, string $contract_start, string $contract_untill, string $birth_date)

{

   $query = "INSERT INTO players (name, team_id, nationality, position, shirt_number, contract_start, contract_untill, birth_date) VALUES
  (:name, :team_id, :nationality, :position, :shirt_number, :contract_start, :contract_untill, :birth_date);";

  $stmt = $pdo->prepare($query);

  $stmt->bindParam(":name", $name);
  $stmt->bindParam(":team_id", $team_id);
  $stmt->bindParam(":nationality", $nationality);
  $stmt->bindParam(":position", $position);
  $stmt->bindParam(":shirt_number", $shirt_number);
  $stmt->bindParam(":contract_start", $contract_start);
  $stmt->bindParam(":contract_untill", $contract_untill);
  $stmt->bindParam(":birth_date", $birth_date);


  $stmt->execute();


}

function create_player(object $pdo, string $name, int $team_id, string $nationality, string $position,
int $shirt_number, string $contract_start, string $contract_untill, string $birth_date)
{


  insert_player( $pdo, $name, $team_id, $nationality, $position, $shirt_number, $contract_start, $contract_untill, $birth_date);
}

function get_player_id_by_name(object $pdo, string $player_name): ?int {
    $stmt = $pdo->prepare("SELECT id FROM players WHERE name = :name LIMIT 1");
    $stmt->execute(['name' => $player_name]);
    $player = $stmt->fetch(PDO::FETCH_ASSOC);

    return $player ? (int) $player['id'] : null;
}


function get_player_name_by_id(object $pdo, int $player_id): ?string {
    $query = "SELECT name FROM players WHERE id = :player_id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":player_id", $player_id);
    $stmt->execute();

    $player = $stmt->fetch(PDO::FETCH_ASSOC);

    return $player ? $player['name'] : null;
}

function get_player_details_by_name(object $pdo, string $name): ?array {
    $stmt = $pdo->prepare("SELECT * FROM players WHERE name = :name LIMIT 1");
    $stmt->execute([':name' => $name]);
    return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
}

function get_players_by_team_name(object $pdo, string $team_name): array {
    $stmt = $pdo->prepare("SELECT p.name, p.position, p.shirt_number, p.nationality
        FROM players p
        JOIN teams t ON p.team_id = t.id
        WHERE t.name = :team_name
        ORDER BY
            CASE
                WHEN p.position = 'Goalkeeper' THEN 1
                WHEN p.position LIKE '%Defence%' OR p.position LIKE '%Back%' THEN 2
                WHEN p.position LIKE '%Midfield%' THEN 3
                WHEN p.position LIKE '%Winger%' OR p.position LIKE '%Forward%' OR p.position LIKE '%Offence%' THEN 4
                ELSE 5
            END,
            p.name
    ");
    $stmt->execute([':team_name' => $team_name]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}



 ?>
