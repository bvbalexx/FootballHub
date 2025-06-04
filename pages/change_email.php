<?php
session_start();

if (!isset($_SESSION["user_id"])) {
     header("Location: login.php?error=not_logged_in");
    exit();
}

require_once '../config/database_handler.php';
require_once '../models/user_model.php';

$step = "verify_password";
$error = "";
$success = "";


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["verify_password"])) {
    $entered_password = $_POST["current_password"];
    $username = $_SESSION["username"];

    if (verify_user_password($pdo, $username, $entered_password)) {
        $step = "change_email";
    } else {
        $error = "Incorrect password!";
    }
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['change_email'])) {
    $new_email = trim($_POST["new_email"]);
    $username = $_SESSION["username"];

    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
        $step = "change_email";
    } else {
        if (update_user_email($pdo, $username, $new_email)) {
            $success = "Email updated successfully!";
            $step = "done";
        } else {
            $error = "Something went wrong. Please try again.";
            $step = "change_email";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Email - FootballHub</title>
    <link rel="stylesheet" href="../css/change_email.css">
</head>
<body>

<div class="change-email-container">
    <h2>Change Email</h2>

    <?php if (!empty($error)): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
        <div class="success-message">
            <?php echo htmlspecialchars($success); ?>
        </div>
    <?php endif; ?>

    <?php if ($step === "verify_password"): ?>
        <form action="" method="POST">
            <label for="current_password">Current Password:</label>
            <input type="password" name="current_password" id="current_password" required>
            <button type="submit" name="verify_password">Verify Password</button>
        </form>

    <?php elseif ($step === "change_email"): ?>
        <form action="" method="POST">
            <input type="hidden" name="verified" value="1">
            <label for="new_email">New Email:</label>
            <input type="email" name="new_email" id="new_email" required>
            <button type="submit" name="change_email">Save New Email</button>
        </form>

    <?php elseif ($step === "done"): ?>

    <?php endif; ?>

    <div class="back-link">
        <a href="profile.php"> Back to Profile</a>
    </div>
</div>

</body>
</html>
