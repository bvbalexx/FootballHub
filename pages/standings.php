<?php
session_start();
require_once '../config/database_handler.php';
require_once '../api/league_standings.php';
require_once '../api/top_scorers.php';
require_once '../models/league_standings_model.php';
require_once '../models/top_scorers_model.php';
require_once '../models/team_model.php';
require_once '../models/league_model.php';



if (!isset($_SESSION["user_id"])) {
    header("Location: ../pages/login.php?error=not_logged_in");
    exit();
}

if (should_update_league_today($pdo)) {
    fetch_and_insert_league_standings($pdo);
}

if (should_update_top_scorers_today($pdo)) {
    fetch_and_insert_top_scorers($pdo);
}



$selectedLeague = $_GET['league'] ?? 'PL';

$standings = get_standings_by_league($pdo, $selectedLeague);
$topScorers = get_top_scorers_by_league($pdo, $selectedLeague);


$highlightRules = [
    "PL" => [
        "champions-league" => array_map('intval', range(1, 5)),
        "europa-league" => [6],
        "conference-league" => [7],
        "relegation" => array_map('intval', range(18, 20))
    ],
    "FL1" => [
        "champions-league" => array_map('intval', range(1, 4)),
        "europa-league" => [5],
        "conference-league" => [6],
        "relegation" => array_map('intval', range(16, 18))
    ],
    "BL1" => [
        "champions-league" => array_map('intval', range(1, 4)),
        "europa-league" => [5],
        "conference-league" => [6],
        "relegation" => array_map('intval', range(16, 18))
    ],
    "SA" => [
        "champions-league" => array_map('intval', range(1, 4)),
        "europa-league" => [5],
        "conference-league" => [6],
        "relegation" => array_map('intval', range(18, 20))
    ],
    "PD" => [
        "champions-league" => array_map('intval', range(1, 4)),
        "europa-league" => [5],
        "conference-league" => [6],
        "relegation" => array_map('intval', range(18, 20))
    ]
];


?>


<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FootballHub - Standings</title>
    <link rel="stylesheet" href="../css/standings.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>

<script src="../js/standings.js"></script>

  <body>

    <nav class="navbar">
        <div class="container">
            <a href="home.php" class="brand">FootballHub</a>
            <ul class="nav-links">
                <li><a href="standings.php" class="active">Standings</a></li>
                <li><a href="players.php">Players</a></li>
                <li><a href="teams.php">Teams</a></li>
                <li><a href="quizzes.php">Quizzes</a></li>
                <li><a href="favourites.php">Favourites</a></li>
            </ul>
            <div class="user-area">
                <a href="profile.php"><?php echo htmlspecialchars($_SESSION["username"]);?></a>
                <a href="logout.php" class="btn-logout">Sign Out</a>
            </div>
        </div>
    </nav>

    <div class="standings-container">
    <h1>Select a League</h1>
    <form method="GET" class="league-form">
        <select name="league" onchange="this.form.submit()">
          <option value="PL" <?= ($selectedLeague === 'PL') ? 'selected' : '' ?>>Premier League</option>
          <option value="BL1" <?= ($selectedLeague === 'BL1') ? 'selected' : '' ?>>Bundesliga</option>
          <option value="PD" <?= ($selectedLeague === 'PD') ? 'selected' : '' ?>>La Liga</option>
          <option value="SA" <?= ($selectedLeague === 'SA') ? 'selected' : '' ?>>Serie A</option>
          <option value="FL1" <?= ($selectedLeague === 'FL1') ? 'selected' : '' ?>>Ligue 1</option>

        </select>
    </form>

    <?php
          $svgLinks = [
              'PL' => 'https://crests.football-data.org/PL.png',
              'BL1' => 'https://crests.football-data.org/BL1.png',
              'PD' => 'https://crests.football-data.org/PD.png',
              'SA' => 'https://crests.football-data.org/SA.png',
              'FL1' => 'https://crests.football-data.org/FL1.png'
          ];

          if (isset($svgLinks[$selectedLeague])) {
              echo '<div class="league-emblem">';
              echo '<img src="' . htmlspecialchars($svgLinks[$selectedLeague]) . '" alt="League Emblem" class="league-logo">';
              echo '</div>';
          }
          ?>


</div>
<div class="section-toggle">
    <button id="standingsBtn" class="toggle-btn active">Standings</button>
    <button id="scorersBtn" class="toggle-btn">Top Scorers</button>
</div>

<div id="standingsSection">

<?php if (!empty($standings)): ?>
    <table class="standings-table">
        <thead>
            <tr>
                <th>#</th>
                <th>Team</th>
                <th>Played</th>
                <th>W</th>
                <th>D</th>
                <th>L</th>
                <th>GF</th>
                <th>GA</th>
                <th>GD</th>
                <th>Points</th>
            </tr>
        </thead>
        <tbody>
          <?php foreach ($standings as $row): ?>
  <?php
  $position = (int)$row['position'];

      $class = '';

      $rules = $highlightRules[$selectedLeague] ?? [];

       foreach ($rules as $label => $positions) {
           if (in_array($position, $positions, true)) {
               $class = $label;
               break;
           }
       }

      $teamName = get_team_name_by_id($pdo, $row['team_id']);

  ?>
  <tr class="<?= $class ?>">
      <td class="position"><?= $position ?></td>
      <td class="team-name">

          <?= htmlspecialchars($teamName) ?>
      </td>
      <td><?= $row['played_games'] ?></td>
      <td><?= $row['won'] ?></td>
      <td><?= $row['draw'] ?></td>
      <td><?= $row['lost'] ?></td>
      <td><?= $row['goals_for'] ?></td>
      <td><?= $row['goals_against'] ?></td>
      <td><?= $row['goal_difference'] ?></td>
      <td><strong><?= $row['points'] ?></strong></td>
  </tr>
<?php endforeach; ?>

        </tbody>
    </table>
<?php else: ?>
    <p class="no-data">No standings available for this league.</p>
<?php endif; ?>

</div>

<div id="scorersSection" style="display: none;">
    <?php if (!empty($topScorers)): ?>
        <table class="standings-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Player</th>
                    <th>Team</th>
                    <th><i class="fa-solid fa-futbol"></i> Played</th>
                    <th><i class="fa-solid fa-bullseye"></i> Goals</th>
                    <th><i class="fa-solid fa-handshake-angle"></i> Assists</th>
                    <th><i class="fa-solid fa-circle-dot"></i> Penalties</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topScorers as $index => $scorer): ?>
                    <?php
                        $playerName = get_player_name_by_id($pdo, $scorer['player_id']);
                        $teamName = get_team_name_by_id($pdo, $scorer['team_id']);
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($playerName) ?></td>
                        <td><?= htmlspecialchars($teamName) ?></td>
                        <td><?= $scorer['played_matches'] ?></td>
                        <td><?= $scorer['goals'] ?></td>
                        <td><?= $scorer['assists'] ?></td>
                        <td><?= $scorer['penalties'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-data">No top scorers available for this league.</p>
    <?php endif; ?>
</div>









  </body>
</html>
