<?php

declare(strict_types=1);

function insert_coach(object $pdo, string $name, string $date_of_birth, string $nationality, int $team_id, string $contract_start, string $contract_until): void {


    if (!$team_id) {
        throw new Exception("Echipa $team_name nu a fost găsită în baza de date.");
    }


    $query = "INSERT INTO coaches (name, date_of_birth, nationality, team_id, contract_start, contract_until)
              VALUES (:name, :date_of_birth, :nationality, :team_id, :contract_start, :contract_until)";

    $stmt = $pdo->prepare($query);

    $stmt->bindParam(":name", $name);
    $stmt->bindParam(":date_of_birth", $date_of_birth);
    $stmt->bindParam(":nationality", $nationality);
    $stmt->bindParam(":team_id", $team_id);
    $stmt->bindParam(":contract_start", $contract_start);
    $stmt->bindParam(":contract_until", $contract_until);

    $stmt->execute();
}

function create_coach(object $pdo, string $name, string $date_of_birth, string $nationality, int $team_id, string $contract_start, string $contract_until)
{


  insert_coach( $pdo, $name, $date_of_birth, $nationality, $team_id, $contract_start, $contract_until);
}

function get_coach_by_team_name(PDO $pdo, string $team_name): ?array {
    $stmt = $pdo->prepare("SELECT c.name, c.nationality, c.date_of_birth, c.contract_start, c.contract_until
        FROM coaches c
        JOIN teams t ON c.team_id = t.id
        WHERE t.name = :team_name
    ");
    $stmt->execute(['team_name' => $team_name]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result ?: null;
}





 ?>
