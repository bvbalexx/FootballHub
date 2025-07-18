document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const teamsButton = document.querySelector(".tab-button:nth-child(1)");
    const playersButton = document.querySelector(".tab-button:nth-child(2)");
    const suggestionsBox = document.getElementById("suggestions");
    let currentType = "team";

    let teams = [];
    let players = [];

    function fetchData() {
      const saved = localStorage.getItem("staticFavouritesData");

      if (saved) {
          const parsed = JSON.parse(saved);
          teams = parsed.teams;
          players = parsed.players;
          console.log("📥 Loaded favourites from localStorage");
      } else {
          fetch("../config/fetch_data.php")
              .then(response => response.json())
              .then(data => {
                  teams = data.teams;
                  players = data.players;
                  localStorage.setItem("staticFavouritesData", JSON.stringify(data));
                  console.log("📦 Saved favourites in localStorage");
              })
              .catch(error => console.error("❌ Eroare la fetch:", error));
      }
  }


 fetchData();

    function updateSearchPlaceholder(type) {
        currentType = type;
        searchInput.placeholder = type === "team" ? "Search for teams..." : "Search for players...";
    }

    if (teamsButton && playersButton && searchInput) {
        teamsButton.addEventListener("click", function () {
            updateSearchPlaceholder("team");
            showTab("team");
            updateFavouritesList("team");
        });

        playersButton.addEventListener("click", function () {
            updateSearchPlaceholder("player");
            showTab("player");
            updateFavouritesList("player");
        });
    }

    function showTab(type) {
        document.getElementById("teams-section").style.display = type === "team" ? "block" : "none";
        document.getElementById("players-section").style.display = type === "player" ? "block" : "none";

        document.querySelectorAll(".tab-button").forEach((btn) => btn.classList.remove("active"));
        if (type === "team") {
            teamsButton.classList.add("active");
        } else {
            playersButton.classList.add("active");
        }
    }

    searchInput.addEventListener("input", handleSearch);

    function handleSearch() {
        let searchValue = searchInput.value.trim().toLowerCase();

        if (searchValue.length < 2) {
            suggestionsBox.innerHTML = "";
            return;
        }

        let filteredResults = [];
        if (currentType === "team") {
            filteredResults = teams.filter(team =>
                team.toLowerCase().includes(searchValue)
            );
        } else {
            filteredResults = players.filter(player =>
                player.toLowerCase().includes(searchValue)
            );
        }

        suggestionsBox.innerHTML =
      filteredResults.length > 0
          ? filteredResults
                .slice(0, 10)
                .map((result) => `<div class="suggestion-item">${result}</div>`)
                .join("")
          : "<div class='suggestion-item'>No results found</div>";

    }


    suggestionsBox.addEventListener("click", function (event) {
    if (event.target.classList.contains("suggestion-item")) {
        const selectedName = event.target.textContent;

        fetch("favourites.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `name=${encodeURIComponent(selectedName)}&category=${currentType}`
        })
        .then(response => response.json())
        .then(data => {

            const messageElementId = currentType === "team" ? "team-message" : "player-message";
            const messageElement = document.getElementById(messageElementId);

            if (messageElement) {
                messageElement.textContent = data.message;
                messageElement.style.color = data.status === "success" ? "#0f0" : "#f00";

                setTimeout(() => {
                    messageElement.textContent = "";
                }, 3000);
            }


            suggestionsBox.innerHTML = "";
            searchInput.value = "";

            if (data.status === "success") {
                updateFavouritesList(currentType);
                updateFavouriteCounters();
            }
        })
        .catch(error => console.error("❌ Error:", error));
    }
});



    function updateFavouritesList(category) {
        fetch(`favourites.php?fetch_favourites=true&type=${category}`)
            .then(response => response.json())
            .then(data => {
                let listElement = category === "team"
                    ? document.getElementById("favourite-teams")
                    : document.getElementById("favourite-players");

                listElement.innerHTML = "";

                data.forEach(item => {
                    const li = document.createElement("li");
                    li.textContent = item;


                    const deleteBtn = document.createElement("button");
                    deleteBtn.textContent = "🗑️";
                    deleteBtn.classList.add("delete-btn");
                    deleteBtn.addEventListener("click", () => {
                        deleteFavourite(item, category);
                    });

                    li.appendChild(deleteBtn);
                    listElement.appendChild(li);
                });
            })
            .catch(error => console.error("❌ Error fetching favourites:", error));
    }


    function deleteFavourite(name, category) {
      fetch("favourites.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: `name=${encodeURIComponent(name)}&category=${category}&action=delete`
      })
      .then(response => response.json())
      .then(data => {

          const messageElementId = category === "team" ? "team-message" : "player-message";
          const messageElement = document.getElementById(messageElementId);

          if (messageElement) {
              messageElement.textContent = data.message;
              messageElement.style.color = data.status === "success" ? "#0f0" : "#f00";
              setTimeout(() => {
                  messageElement.textContent = "";
              }, 3000);
          }

          if (data.status === "success") {
              updateFavouritesList(category);
              updateFavouriteCounters();
          }
      })
      .catch(error => console.error("❌ Error deleting favourite:", error));
  }

  function updateFavouriteCounters() {
    fetch("favourites.php?fetch_favourites=true&type=team")
        .then(res => res.json())
        .then(data => {
            document.getElementById("team-count").textContent = `(${data.length}/20)`;
        });

    fetch("favourites.php?fetch_favourites=true&type=player")
        .then(res => res.json())
        .then(data => {
            document.getElementById("player-count").textContent = `(${data.length}/20)`;
        });
}



    updateFavouritesList("team");
    updateFavouritesList("player");
    updateFavouriteCounters();
});
