<?php
require_once '../models/favourites_model.php';
require_once '../models/user_news_cache_model.php';

function fetch_and_cache_user_news(PDO $pdo, int $user_id): void {
    $apiKey = "10f951625ae3c6629dd4702f7b0beb8f";

    $players = get_favourite_players($pdo, $user_id);
    $teams = get_favourite_teams($pdo, $user_id);

    shuffle($players);
    shuffle($teams);

    $keywords = array_slice($players, 0, 2);
    $keywords = array_merge($keywords, array_slice($teams, 0, 2));

    delete_news_cache_for_user($pdo, $user_id);

    foreach ($keywords as $keyword) {
        $url = "https://gnews.io/api/v4/search?q=" . urlencode($keyword) . "&apikey=$apiKey&country=gb";

        $response = fetch_from_gnews($url);
        if (!$response) continue;

        $data = json_decode($response, true);
        if (!empty($data['articles'][0])) {
            $article = $data['articles'][0];

            insert_news_cache(
                $pdo,
                $user_id,
                $keyword,
                $article['title'] ?? '',
                $article['description'] ?? '',
                $article['url'] ?? '',
                $article['image'] ?? ''
            );
        }
    }
}

function fetch_from_gnews(string $url): ?string {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if (curl_errno($ch)) {
        error_log("cURL error: " . curl_error($ch));
    }

    curl_close($ch);

    if ($httpCode !== 200) {
        error_log("GNews API failed with status $httpCode for URL: $url");
        return null;
    }

    return $result;
}







 ?>
