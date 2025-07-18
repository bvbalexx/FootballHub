<?php
session_start();

require_once "../models/user_news_cache_model.php";
require_once "../models/user_model.php";
require_once "../api/fetch_data_and_cache_news.php";


if (!isset($_SESSION["user_id"])) {
    header("Location: ../pages/login.php?error=not_logged_in");
    exit();
}



$user_id = $_SESSION["user_id"];

if (((should_fetch_news_today($pdo, $user_id)) && user_has_favourites($pdo, $user_id))) {
    fetch_and_cache_user_news($pdo, $user_id);
    update_news_last_fetched($pdo, $user_id);
}

$cached_news = get_cached_news_for_user($pdo, $user_id);
$has_favourites = user_has_favourites($pdo, $user_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FootballHub - Home</title>
  <link rel="stylesheet" href="../css/home.css">
</head>
<body>


  <nav class="navbar">
    <div class="container">
      <a href="#" class="brand">FootballHub</a>
      <ul class="nav-links">
        <li><a href="standings.php">Standings</a></li>
        <li><a href="players.php">Players</a></li>
        <li><a href="teams.php">Teams</a></li>
        <li><a href="quizzes.php">Quizzes</a></li>
        <li><a href="favourites.php">Favourites</a></li>
      </ul>
      <div class="user-area">
        <a href="profile.php"><?php echo htmlspecialchars($_SESSION["username"]); ?></a>
        <a href="logout.php" class="btn-logout">Sign Out</a>
      </div>
    </div>
  </nav>


  <div class="home-welcome">
    <h1>Welcome back to FootballHub!</h1>
    <p>Explore personalized football news and insights.</p>
  </div>

  <div class="news-section">
    <h2>Daily Personalized News</h2>

    <?php if (!$has_favourites): ?>
        <p class="no-news-msg">You have no favourite players or teams yet. Start adding them to receive personalized news!</p>
    <?php elseif (empty($cached_news)): ?>
        <p class="no-news-msg">No news available yet. Please check back later.</p>
    <?php else: ?>
      <div class="news-grid">
       <?php foreach ($cached_news as $article): ?>
           <div class="news-card">
               <img src="<?php echo htmlspecialchars($article['image']); ?>" alt="News Image">
               <div class="news-content">
                   <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                   <p><?php echo htmlspecialchars($article['description']); ?></p>
                   <a href="<?php echo htmlspecialchars($article['url']); ?>" target="_blank">Read more</a>
               </div>
           </div>
       <?php endforeach; ?>
   </div>
    <?php endif; ?>
</div>


</body>
</html>
