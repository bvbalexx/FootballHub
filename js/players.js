document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("player-search");
    const suggestionsBox = document.getElementById("suggestions");
    let players = [];

    function fetchPlayers() {
        const saved = localStorage.getItem("playersList");

        if (saved) {
            players = JSON.parse(saved);
        } else {
            fetch("../config/fetch_data.php")
                .then(response => response.json())
                .then(data => {
                    players = data.players;
                    localStorage.setItem("playersList", JSON.stringify(players));
                })
                .catch(error => console.error("Fetch error:", error));
        }
    }

    fetchPlayers();

    searchInput.addEventListener("input", function () {
        const query = this.value.trim().toLowerCase();

        if (query.length < 2) {
            suggestionsBox.innerHTML = "";
            return;
        }

        const filtered = players.filter(player =>
            player.toLowerCase().includes(query)
        );

        suggestionsBox.innerHTML =
            filtered.length > 0
                ? filtered.slice(0, 10).map(name => `<div class="suggestion-item">${name}</div>`).join("")
                : "<div class='suggestion-item'>No results found</div>";
    });

    

    suggestionsBox.addEventListener("click", function (e) {
        if (e.target.classList.contains("suggestion-item")) {
            const playerName = e.target.textContent;

            fetch(`../config/get_player_info.php?name=${encodeURIComponent(playerName)}`)
                .then(response => response.json())
                .then(player => {
                    if (player && !player.error) {
                        displayPlayerInfo(player);
                    } else {
                        alert("Player not found!");
                    }
                    suggestionsBox.innerHTML = "";
                    searchInput.value = "";
                })
                .catch(err => {
                    console.error("Eroare la preluarea detaliilor jucătorului:", err);
                });
        }
    });

    function displayPlayerInfo(player) {
    const container = document.getElementById("player-details-container");

    container.innerHTML = `
        <div class="player-info-card">
            <h2 class="player-name">${player.name}</h2>

            <div class="team-header">
                ${player.team_crest ? `<img src="${player.team_crest}" class="team-emblem" alt="Team Emblem">` : ""}
                <h3 class="team-name">${player.team_name}</h3>
            </div>

            <p><strong>Nationality:</strong> ${player.nationality}</p>
            <p><strong>Position:</strong> ${player.position}</p>
            <p><strong>Shirt Number:</strong> ${player.shirt_number}</p>
            <p><strong>Contract Start:</strong> ${player.contract_start}</p>
            <p><strong>Contract Until:</strong> ${player.contract_until}</p>
            <p><strong>Birth Date:</strong> ${player.birth_date}</p>
        </div>
    `;

    container.classList.remove("hidden");
}

});
