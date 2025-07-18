<?php

declare(strict_types=1);

function get_random_news(PDO $pdo, int $limit = 5) {
    $stmt = $pdo->prepare("SELECT * FROM news ORDER BY RAND() LIMIT :limit");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
