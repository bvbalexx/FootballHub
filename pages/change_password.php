<?php
session_start();

if (!isset($_SESSION["user_id"])) {
     header("Location: login.php?error=not_logged_in");
    exit();
}

require_once '../config/database_handler.php';
require_once '../models/user_model.php';

$step = 'verify';
$successMessage = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_SESSION["username"];

    if (isset($_POST['verify_password'])) {
        $currentPassword = $_POST['current_password'];
        if (verify_user_password($pdo, $username, $currentPassword)) {
            $step = 'change';
        } else {
            $error = "Incorrect password!";
        }
    }

    if (isset($_POST['change_password']) && isset($_POST['verified'])) {
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if (strlen($newPassword) < 3) {
            $error = "Password must be at least 3 characters.";
            $step = 'change';
        } elseif ($newPassword !== $confirmPassword) {
            $error = "Passwords do not match.";
            $step = 'change';
        } else {
            update_password($pdo, $username, $newPassword);
            session_unset();
            session_destroy();
            header("Location: login.php?message=password_changed");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change Password</title>
    <link rel="stylesheet" href="../css/change_password.css">
</head>
<body>
    <div class="change-password-container">
        <h2>Change Password</h2>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($step === 'verify'): ?>
            <form action="" method="POST">
                <label for="current_password">Current Password:</label>
                <input type="password" name="current_password" id="current_password" required>
                <button type="submit" name="verify_password">Verify Password</button>
            </form>
        <?php elseif ($step === 'change'): ?>
            <form action="" method="POST">
                <input type="hidden" name="verified" value="1">
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required>

                <label for="confirm_password">Confirm Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>

                <button type="submit" name="change_password">Save New Password</button>
            </form>
        <?php endif; ?>

        <div class="back-link">
            <a href="profile.php">Back to Profile</a>
        </div>
    </div>
</body>
</html>
