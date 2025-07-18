<?php


function member_from(string $member_since): int {
    $registrationDate = new DateTime($member_since);
    $currentDate = new DateTime('now');
    $interval = $registrationDate->diff($currentDate);

    return $interval->days;
}


 ?>
