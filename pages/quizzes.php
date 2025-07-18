<?php
require_once '../config/database_handler.php';
require_once '../models/user_model.php';
require_once '../models/question_model.php';

session_start();
if (!isset($_SESSION["user_id"])) {
    header("Location: ../pages/login.php?error=not_logged_in");
    exit();
}

$user_id = $_SESSION['user_id'];
$today = date('Y-m-d');

$view_mode = $_POST['view_mode'] ?? 'quiz';
$leaderboard = ($view_mode === 'leaderboard') ? get_leaderboard_top($pdo) : [];

$quiz_done_today = !can_start_quiz_today($pdo, $user_id);
$today_score = $quiz_done_today ? get_today_quiz_points($pdo, $user_id) : null;

if (!$quiz_done_today && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['start_quiz']) && !isset($_SESSION['quiz_questions'])) {

    $_SESSION['quiz_questions'] = get_random_quiz_questions($pdo);
    $_SESSION['quiz_submitted'] = false;
    $_SESSION['quiz_started'] = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz']) && isset($_SESSION['quiz_questions'])) {
    $questions = $_SESSION['quiz_questions'];
    $score = 0;

    foreach ($questions as $q) {
        $user_answer = $_POST["q{$q['id']}"] ?? null;
        if ((int)$user_answer === (int)$q['correct_answer']) {
            $score += match($q['difficulty']) {
                'easy' => 1,
                'medium' => 2,
                'hard' => 3,
                default => 0
            };
        }
    }

    update_user_quiz_result($pdo, $user_id, $score, $today);
    $_SESSION['quiz_submitted'] = true;
    unset($_SESSION['quiz_questions'], $_SESSION['quiz_started']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FootballHub - Quizzes</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/quizzes.css?v=<?php echo time(); ?>">
</head>
<body>

<nav class="navbar">
    <div class="container">
        <a href="home.php" class="brand">FootballHub</a>
        <ul class="nav-links">
            <li><a href="standings.php">Standings</a></li>
            <li><a href="players.php">Players</a></li>
            <li><a href="teams.php">Teams</a></li>
            <li><a href="quizzes.php" class="active">Quizzes</a></li>
            <li><a href="favourites.php">Favourites</a></li>
        </ul>
        <div class="user-area">
            <a href="profile.php"><?= htmlspecialchars($_SESSION["username"]) ?></a>
            <a href="logout.php" class="btn-logout">Sign Out</a>
        </div>
    </div>
</nav>

<div class="quiz-container">
    <h2>Daily Quiz Section</h2>

    <div class="quiz-toggle">
        <form method="post">
            <input type="hidden" name="view_mode" value="quiz">
            <button class="start-btn <?= $view_mode === 'quiz' ? 'active' : '' ?>" type="submit">Quiz</button>
        </form>
        <form method="post">
            <input type="hidden" name="view_mode" value="leaderboard">
            <button class="start-btn <?= $view_mode === 'leaderboard' ? 'active' : '' ?>" type="submit">Leaderboard</button>
        </form>
    </div>

    <?php if ($view_mode === 'quiz'): ?>
        <?php if ($quiz_done_today || ($_SESSION['quiz_submitted'] ?? false)): ?>
            <p>You have already completed today’s quiz. Come back tomorrow!</p>
            <?php if ($today_score !== null): ?>
                <p>Your score for today: <strong><?= $today_score ?></strong> points.</p>
            <?php endif; ?>
            <?php unset($_SESSION['quiz_questions'], $_SESSION['quiz_started'], $_SESSION['quiz_submitted']); ?>
        <?php elseif (!isset($_SESSION['quiz_questions'])): ?>
            <form method="post">
                <input type="hidden" name="view_mode" value="quiz">
                <button class="start-btn" type="submit" name="start_quiz">Start Quiz</button>
            </form>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php if ($view_mode === 'leaderboard'): ?>
    <div class="quiz-container">
        <h2>Top 3 Quiz Players</h2>
        <table class="leaderboard-table">
            <tr><th>#</th><th>Username</th><th>Total Points</th></tr>
            <?php foreach ($leaderboard as $index => $row): ?>
                <tr class="rank-<?= $index + 1 ?>">
                    <td><?= $index + 1 ?></td>
                    <td><?= htmlspecialchars($row['username']) ?></td>
                    <td><?= htmlspecialchars($row['quiz_points']) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php endif; ?>

<?php if ($view_mode === 'quiz' && isset($_SESSION['quiz_questions'])): ?>
    <div class="question-block">
        <form method="post" action="quizzes.php">
            <input type="hidden" name="view_mode" value="quiz">
            <?php foreach ($_SESSION['quiz_questions'] as $i => $q): ?>
                <div class="question">
                    <p><strong><?= $i + 1 ?>. <?= htmlspecialchars($q['question_text']) ?></strong> (<?= $q['difficulty'] ?>)</p>
                    <?php for ($j = 1; $j <= 4; $j++): ?>
                        <label>
                            <input type="radio" name="q<?= $q['id'] ?>" value="<?= $j ?>" required>
                            <?= htmlspecialchars($q["answer_$j"]) ?>
                        </label><br>
                    <?php endfor; ?>
                </div>
                <hr>
            <?php endforeach; ?>
            <button class="start-btn" type="submit" name="submit_quiz">Submit Quiz</button>
        </form>
    </div>
<?php endif; ?>

</body>
</html>
