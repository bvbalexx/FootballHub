<?php


function is_username_wrong(bool|array $result) {

if(!$result) {

  return true;

} else {

  return false;
}

}

function is_password_wrong(string $password, string $hashedpwd) {

if(!password_verify($password, $hashedpwd)) {

  return true;

} else {

  return false;
}

}


function is_input_empty(string $username, string $password) {

if(empty($username) || empty($password)) {

  return true;
}
  else {

    return false;
  }

}
