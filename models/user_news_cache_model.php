<?php

require_once "../config/database_handler.php";

function insert_news_cache(object $pdo, int $user_id, string $keyword, string $title, string $description, string $url, string $image): bool {
    $query = "INSERT INTO user_news_cache (user_id, keyword, title, description, url, image)
              VALUES (:user_id, :keyword, :title, :description, :url, :image)";
    $stmt = $pdo->prepare($query);
    return $stmt->execute([
        ':user_id' => $user_id,
        ':keyword' => $keyword,
        ':title' => $title,
        ':description' => $description,
        ':url' => $url,
        ':image' => $image
    ]);
}


function delete_news_cache_for_user(PDO $pdo, int $user_id): bool {
    $stmt = $pdo->prepare("DELETE FROM user_news_cache WHERE user_id = :user_id");
    return $stmt->execute([':user_id' => $user_id]);
}

function get_cached_news_for_user(PDO $pdo, int $user_id): array {
    $stmt = $pdo->prepare("SELECT title, description, url, image FROM user_news_cache WHERE user_id = :user_id");
    $stmt->execute([':user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC); 
}





 ?>
