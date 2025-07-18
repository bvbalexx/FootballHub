<?php


declare(strict_types=1);

function insert_team(object $pdo, string $name, int $founded_year, string $tla,
string $club_colors, string $venue, string $coach)

{

   $query = "INSERT INTO teams (name, founded_year, tla, club_colors, venue, coach) VALUES
  (:name, :founded_year, :tla, :club_colors, :venue, :coach);";

  $stmt = $pdo->prepare($query);

  $stmt->bindParam(":name", $name);
  $stmt->bindParam(":founded_year", $founded_year);
  $stmt->bindParam(":tla", $tla);
  $stmt->bindParam(":club_colors", $club_colors);
  $stmt->bindParam(":venue", $venue);
  $stmt->bindParam(":coach", $coach);


  $stmt->execute();


}

function create_team(object $pdo, string $name, int $founded_year, string $tla,
string $club_colors, string $venue, string $coach)
{


  insert_team( $pdo, $name, $founded_year, $tla, $club_colors, $venue, $coach);
}

function get_team_id_by_name(object $pdo, string $team_name): ?int {
    $stmt = $pdo->prepare("SELECT id FROM teams WHERE name = :name");
    $stmt->execute(['name' => $team_name]);
    $team = $stmt->fetch(PDO::FETCH_ASSOC);

    return $team ? (int) $team['id'] : null;
}

function get_team_name_by_id(object $pdo, int $team_id): ?string {
    $query = "SELECT name FROM teams WHERE id = :team_id;";
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(":team_id", $team_id);
    $stmt->execute();

    $team = $stmt->fetch(PDO::FETCH_ASSOC);

    return $team ? $team['name'] : null;
}

function get_team_emblem_by_player_id(PDO $pdo, int $player_id): ?string {
    $stmt = $pdo->prepare("SELECT t.crest_url
        FROM teams t
        JOIN players p ON p.team_id = t.id
        WHERE p.id = :player_id
    ");
    $stmt->execute([':player_id' => $player_id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? $row['crest_url'] : null;
}



 ?>
