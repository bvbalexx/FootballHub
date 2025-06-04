<?php

function display_news_items(array $newsItems): void {
?>
    <div class="news">
        <?php foreach ($newsItems as $news): ?>
            <div class="news-item">
                <h3><?php echo htmlspecialchars($news['title']); ?></h3>
                <p><?php echo htmlspecialchars($news['content']); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
<?php
}
?>
