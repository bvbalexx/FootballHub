<?php

require_once '../views/login_view.php';
  $errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    require_once '../config/database_handler.php';
    require_once '../models/user_model.php';
    require_once '../controllers/login_controller.php';

    $username = $_POST["username"];
    $password = $_POST["password"];




    if (is_input_empty($username, $password)) {
        $errors[] = "Fill in all fields!";
    }


    $result = get_user($pdo, $username);
    if (is_username_wrong($result)) {
        $errors[] = "Incorrect login info";
    }


    if (!is_username_wrong($result) && is_password_wrong($password, $result["password"])) {
        $errors[] = "Incorrect login info";
    }


    if (!empty($errors)) {

    } else {

        require_once '../config/config_session.php';


        $newSessionId = session_create_id();
        $sessionId = $newSessionId . "_" . $result["id"];
        session_id($sessionId);

        $_SESSION["user_id"] = $result["id"];
        $_SESSION["username"] = htmlspecialchars($result["username"]);
        $_SESSION["last_regeneration"] = time();


        header("Location: home.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FootballHub</title>
    <link rel="stylesheet" href="../css/login.css">
</head>
<body>
    <div class="login-container">
        <h1>Login</h1>

        <?php

          display_not_logged_in_error();


          display_login_errors($errors);
      ?>

        <form action="" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>

            <button type="submit">Login</button>
        </form>

        <p class="signup-link">Don't have an account? <a href="signup.php">Register here</a></p>
        <p class="signup-link"><a href="../index.php">Return to Home</a></p>



    </div>
</body>
</html>
