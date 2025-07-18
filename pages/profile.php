<!DOCTYPE html>
<?php session_start();

if (!isset($_SESSION["user_id"])) {
     header("Location: login.php?error=not_logged_in");
    exit();
}


require_once '../models/user_model.php';
require_once '../config/database_handler.php';
require_once '../views/profile_view.php';

$date_joined = get_date_joined($pdo, $_SESSION['username']);

?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FootballHub</title>
    <link rel="stylesheet" href="../css/profile.css?v=<?php echo time(); ?>">

</head>
<body>


    <nav class="navbar">
        <div class="container">
            <a href="home.php" class="brand">FootballHub</a>
            <ul class="nav-links">
                <li><a href="standings.php">Standings</a></li>
                <li><a href="players.php">Players</a></li>
                <li><a href="teams.php">Teams</a></li>
                <li><a href="quizzes.php">Quizzes</a></li>
                <li><a href="favourites.php">Favourites</a></li>
            </ul>
            <div class="user-area">
                <a href="profile.php"><?php echo htmlspecialchars($_SESSION["username"]); ?></a>
                <a href="logout.php" class="btn-logout">Sign Out</a>
            </div>
        </div>
    </nav>

    <div class="container-profile">

        <h2>Your Profile</h2>
        <p class="member-duration">You've been with us for <strong><?php echo member_from($date_joined) ?></strong> days!</p>

        <div class="profile-wrapper">

            <nav class="profile-menu">
                <ul>
                    <li><a href="#" class="active" onclick="showSection('main')">Main Profile</a></li>
                    <li><a href="#" onclick="showSection('security')">Security</a></li>
                </ul>
            </nav>


            <div class="profile-content">

                <section id="main-section">
                  <div class="profile-details">
                      <p><strong>Username:</strong> <span><?php echo htmlspecialchars($_SESSION["username"]); ?></span></p>

                      <p><strong>Email:</strong> <span>
                          <?php
                          $email = get_email($pdo, $_SESSION["username"]);
                          echo htmlspecialchars($email["email"]);
                          ?>
                      </span></p>

                      <p><strong>Member Since:</strong> <span>
                          <?php
                          $date_joined = get_date_joined($pdo, $_SESSION["username"]);
                          echo htmlspecialchars(date("Y-m-d", strtotime($date_joined)));
                          ?>
                      </span></p>

                      <p><strong>Quiz Points:</strong> <span>
                          <?php
                          $points = get_quiz_points($pdo, $_SESSION["user_id"]);
                          echo htmlspecialchars($points);
                          ?>
                      </span></p>
                    </div>


                    </div>
                </section>


            <section id="security-section" style="display:none;">
                <div class="security-options">
                  <a href="change_email.php" class="security-option">Change Email</a>
                  <a href="change_password.php" class="security-option">Change Password</a>
                </div>
              </section>


            </div>
        </div>
    </div>

    <script>
        function showSection(section) {
            const mainSection = document.getElementById('main-section');
            const securitySection = document.getElementById('security-section');
            const links = document.querySelectorAll('.profile-menu a');

            links.forEach(link => link.classList.remove('active'));

            if(section === 'main') {
                mainSection.style.display = 'block';
                securitySection.style.display = 'none';
                links[0].classList.add('active');
            } else if (section === 'security') {
        mainSection.style.display = 'none';
        securitySection.style.display = 'block';
        links[1].classList.add('active');
    }
        }
    </script>

</body>
</html>
