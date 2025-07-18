<?php


declare(strict_types = 1);


function insert_user(object $pdo, string $username, string $password, string $email) {


$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

  $query = "INSERT INTO users (username, password, email) VALUES
 (:username, :password, :email);";

 $stmt = $pdo->prepare($query);

 $stmt->bindParam(":username", $username);
 $stmt->bindParam(":password", $hashedPassword);
 $stmt->bindParam(":email", $email);

 if ($stmt->execute()) {
      return true;
  } else {
      return false;
  }


}

function create_user(object $pdo, string $username, string $password, string $email) {

  return insert_user( $pdo, $username, $password, $email);
}

function get_username(object $pdo, string $username) {

 $query = "SELECT username FROM users WHERE username = :username;";


 $stmt = $pdo->prepare($query);
 $stmt->bindParam(":username", $username);
 $stmt->execute();

 $result = $stmt->fetch(PDO::FETCH_ASSOC);
 return $result;



}

function get_email(object $pdo, string $username) {

 $query = "SELECT email FROM users WHERE username = :username;";


 $stmt = $pdo->prepare($query);
 $stmt->bindParam(":username", $username);
 $stmt->execute();

 $result = $stmt->fetch(PDO::FETCH_ASSOC);
 return $result;

}



 function username_exists(object $pdo, string $username) {
     $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
     $stmt->execute([$username]);
     return $stmt->fetch() !== false;
 }

 function get_user(object $pdo, string $username) {

   $query = "SELECT * FROM users WHERE username = :username;";


   $stmt = $pdo->prepare($query);
   $stmt->bindParam(":username", $username);
   $stmt->execute();

   $result = $stmt->fetch(PDO::FETCH_ASSOC);
   return $result;



 }


function get_date_joined(PDO $pdo, string $username): ?string {
    $query = "SELECT date_joined FROM users WHERE username = :username;";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ? $result['date_joined'] : null;
}

function verify_user_password(PDO $pdo, string $username, string $password): bool {
    $query = "SELECT password FROM users WHERE username = :username";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && password_verify($password, $result['password'])) {
        return true;
    } else {
        return false;
    }
}


function update_user_email(PDO $pdo, string $username, string $new_email): bool {
    $query = "UPDATE users SET email = :email WHERE username = :username";

    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':email', $new_email, PDO::PARAM_STR);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);

    return $stmt->execute();
}


function update_password(object $pdo, string $username, string $new_password): bool {
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    $query = "UPDATE users SET password = :password WHERE username = :username";
    $stmt = $pdo->prepare($query);

    $stmt->bindParam(":password", $hashed_password);
    $stmt->bindParam(":username", $username);

    return $stmt->execute();
}

function update_news_last_fetched(PDO $pdo, int $user_id): void {
    $stmt = $pdo->prepare("UPDATE users SET news_last_fetched = CURDATE() WHERE id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
}

function should_fetch_news_today(PDO $pdo, int $user_id): bool {
    $stmt = $pdo->prepare("SELECT news_last_fetched FROM users WHERE id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $date = $stmt->fetchColumn();

    return $date !== date('Y-m-d');
}


function can_start_quiz_today(PDO $pdo, int $user_id): bool {
    $stmt = $pdo->prepare("SELECT quiz_last_date FROM users WHERE id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    $date = $stmt->fetchColumn();

    return $date !== date('Y-m-d');
}

function update_quiz_date(PDO $pdo, int $user_id, string $date): void {
    $stmt = $pdo->prepare("UPDATE users SET quiz_last_date = :date WHERE id = :user_id");
    $stmt->execute([':date' => $date, ':user_id' => $user_id]);
}

function update_user_quiz_result(PDO $pdo, int $user_id, int $points, string $date): void {
    $stmt = $pdo->prepare(" UPDATE users
        SET quiz_points = quiz_points + :points,
            quiz_points_today = :points,
            quiz_last_date = :date
        WHERE id = :user_id
    ");
    $stmt->execute([
        ':points' => $points,
        ':date' => $date,
        ':user_id' => $user_id
    ]);
}

function get_today_quiz_points(PDO $pdo, int $user_id): ?int {
    $stmt = $pdo->prepare("SELECT quiz_points_today FROM users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $score = $stmt->fetchColumn();

    return $score !== false ? (int)$score : null;
}

function get_quiz_points(PDO $pdo, int $user_id): ?int {
    $stmt = $pdo->prepare("SELECT quiz_points FROM users WHERE id = :id");
    $stmt->execute([':id' => $user_id]);
    $score = $stmt->fetchColumn();

    return $score !== false ? (int)$score : null;
}

function get_leaderboard_top(PDO $pdo, int $limit = 3): array {
    $stmt = $pdo->prepare("SELECT username, quiz_points FROM users ORDER BY quiz_points DESC LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
