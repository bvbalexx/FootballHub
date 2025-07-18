<?php

function is_input_empty(string $username, string $password, string $email) {

if(empty($username) || empty($password) || empty($email)) {

    return true;

}
else {
  return false;
}
}


function password_unmatched(string $password, string $confirm_password ) {

  if ($password !== $confirm_password) {

   return true;
  }
  else {

    return false;
  }

}

function is_email_invalid(string $email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}


function is_username_invalid(string $username) {
    if (strlen($username) < 4 || strlen($username) > 20) {
        return true;
    } else {
        return false;
    }
}


function is_password_invalid(string $password) {
    if (strlen($password) < 3) {
        return true;
    } else {
        return false;
    }
}

function check_username_exists(PDO $pdo, string $username) {
    return username_exists($pdo, $username);
}



 ?>
