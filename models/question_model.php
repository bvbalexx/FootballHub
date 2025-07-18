<?php

function get_random_quiz_questions(PDO $pdo) {
    $result = [];

    $result = array_merge(
        get_questions_by_difficulty($pdo, 'easy', 4),
        get_questions_by_difficulty($pdo, 'medium', 3),
        get_questions_by_difficulty($pdo, 'hard', 3)
    );

    shuffle($result);
    return $result;
}

function get_questions_by_difficulty(PDO $pdo, string $difficulty, int $limit): array {

    $limit = (int) $limit;


    $stmt = $pdo->prepare("SELECT * FROM questions WHERE difficulty = ? ORDER BY RAND() LIMIT $limit");
    $stmt->execute([$difficulty]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
