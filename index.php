<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>FootballHub</title>
  <link rel="stylesheet" href="css/index.css">
</head>
<body>


  <div class="landing-container">
    <h1>Welcome to FootballHub</h1>
    <p class="subtext">Join for free to access all of our features</p>

    <div class="button-group">
      <a href="pages/login.php"  class="auth-button">Log In</a>
      <a href="pages/signup.php" class="auth-button">Register</a>
    </div>

    <div class="gallery">
      <div class="gallery-item"><img src="media/screen1.png" alt="News"></div>
      <div class="gallery-item"><img src="media/screen2.png" alt="Teams"></div>
      <div class="gallery-item"><img src="media/screen3.png" alt="Favourites"></div>
    </div>
  </div>


  <div class="video-container">
    <video controls muted>
      <source src="media/hero.mp4" type="video/mp4">
      Your browser does not support the video tag.
    </video>
  </div>

  
  <div id="lightbox" class="lightbox">
    <span class="close">&times;</span>
    <img class="lightbox-img" src="" alt="Preview">
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const lb = document.getElementById('lightbox'),
            img = document.querySelector('.lightbox-img'),
            closeBtn = lb.querySelector('.close');

      document.querySelectorAll('.gallery-item img').forEach(th => {
        th.addEventListener('click', () => {
          img.src = th.src;
          lb.style.display = 'flex';
        });
      });
      closeBtn.addEventListener('click', () => lb.style.display = 'none');
      lb.addEventListener('click', e => {
        if (e.target === lb) lb.style.display = 'none';
      });
    });
  </script>

</body>
</html>
