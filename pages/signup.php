<?php

require_once '../views/signup_view.php';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once '../config/database_handler.php';
    require_once '../models/user_model.php';
    require_once '../controllers/signup_controller.php';


    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];



    if (is_input_empty($username, $password, $email)) {
        $errors[] = "All fields must be completed!";
    }
    if (is_email_invalid($email)) {
        $errors[] = "Invalid email format!";
    }
    if (password_unmatched($password, $confirm_password)) {
        $errors[] = "Passwords do not match!";
    }
    if (is_username_invalid($username)) {
        $errors[] = "Username must be between 4 and 20 characters!";
    }
    if (is_password_invalid($password)) {
        $errors[] = "Password must be at least 3 characters long!";
    }
    if (check_username_exists($pdo, $username)) {
        $errors[] = "Username is already taken!";
    }

    if (empty($errors)) {
        $result = create_user($pdo, $username, $password, $email);
        if ($result) {
            header("Location: login.php?signup=success");
            exit();
        } else {
            $errors[] = "Something went wrong, please try again!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - FootballHub</title>
    <link rel="stylesheet" href="../css/signup.css">
</head>
<body>
    <div class="signup-container">
        <h1>Create an account</h1>


        <?php display_signup_errors($errors); ?>



  
        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="email">Email Address:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit">Create your account</button>
        </form>

        <p class="login-link">Already have an account? <a href="login.php">Login</a></p>
        <p class="login-link"><a href="../index.php">Return to Home</a></p>
    </div>
</body>
</html>
