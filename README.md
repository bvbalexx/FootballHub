# FootballHub

## Overview
FootballHub is a web application built as a bachelor’s‐thesis project, designed for football enthusiasts to access up‑to‑date information on the world’s top five leagues. It pulls real data daily from trusted APIs, storing it in MySQL, and presents news, stats and interactive features through a clean PHP‑driven interface enhanced with JavaScript, HTML and CSS.

## Features

- **User Authentication & Registration**  
  Secure signup and login system with password hashing and session management.

- **Home (Personalized News Feed)**  
  Daily‐updated, personalized football news sourced from GNews API—tailored to the user’s favorite teams and players.

- **Players Directory**  
  Browse detailed profiles and statistics for players across the Premier League, La Liga, Serie A, Bundesliga and Ligue 1.

- **Teams Overview**  
  View squad lists, club badges, fixtures and basic club info for each of the big five leagues.

- **Standings & Top Scorers**  
  Live league tables and goalscoring charts refreshed every 24 hours via Football‑Data.org API.

- **Quizzes**  
  Interactive football trivia quizzes to test your knowledge of players, teams and leagues.

- **Favorites**  
  Mark your favorite players and teams for quick access on your dashboard.

- **User Profile**  
  Edit personal details and security settings (password, email) in a dedicated profile section.

## Data Sources
- **Football‑Data.org API**  
  • Fixtures, standings, scorers, team & player stats for top five European leagues  
- **GNews API**  
  • Football news articles aggregated and filtered by relevance  

All external calls are rate‑controlled and cached in a MySQL database to ensure performance and API quota compliance.

## Architecture & Workflow
1. **MVC Pattern (Extended/Adapted)**  
   - Core structure follows Model‑View‑Controller to separate concerns.  
   - Models handle database logic, Views render HTML/CSS, Controllers coordinate data flow.  
   - Pattern was later extended and adapted to fit additional features such as quizzes and personalized feeds.
2. **Backend (PHP)**  
   - API clients fetch and parse JSON data.  
   - Business logic assembles pages and enforces access control.
3. **Database (MySQL)**  
   - Schemas for users, players, teams, standings, news and quizzes.  
   - Cron job schedules daily data‑sync routines.
4. **Frontend (HTML, CSS, JavaScript)**  
   - Responsive layouts, dynamic UI updates via AJAX.  
   - Form validation and interactive quiz engine.
5. **Server Stack (XAMPP)**  
   - Apache HTTP Server and MySQL running locally for development.

## Tools & Technologies
- **Languages:** PHP (primary), JavaScript, HTML5, CSS3, SQL  
- **Database:** MySQL (via XAMPP)  
- **Server:** Apache (XAMPP)  
- **APIs:** Football‑Data.org, GNews  
- **Libraries & Utilities:**  
  - cURL / Guzzle (HTTP requests)  
  - Composer (dependency management)  
  - AJAX (dynamic content loading)  
- **Development Environment:** XAMPP (Apache, MySQL, PHP 7.4+), Git

---

> **Note:** All data is refreshed daily via scheduled scripts to keep standings, scorers and news current.  
