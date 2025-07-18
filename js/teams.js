document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("team-search");
    const suggestionsBox = document.getElementById("team-suggestions");
    let teams = [];

    function fetchTeams() {
        const saved = localStorage.getItem("teamsList");

        if (saved) {
            teams = JSON.parse(saved);
        } else {
            fetch("../config/fetch_data.php")
                .then(response => response.json())
                .then(data => {
                    teams = data.teams;
                    localStorage.setItem("teamsList", JSON.stringify(teams));
                })
                .catch(error => console.error("Fetch error:", error));
        }
    }

    fetchTeams();

    searchInput.addEventListener("input", function () {
        const query = this.value.trim().toLowerCase();

        if (query.length < 2) {
            suggestionsBox.innerHTML = "";
            return;
        }

        const filtered = teams.filter(team =>
            team.toLowerCase().includes(query)
        );

        suggestionsBox.innerHTML =
            filtered.length > 0
                ? filtered.slice(0, 10).map(name => `<div class="suggestion-item">${name}</div>`).join("")
                : "<div class='suggestion-item'>No results found</div>";
    });

    suggestionsBox.addEventListener("click", function (e) {
        if (e.target.classList.contains("suggestion-item")) {
            const teamName = e.target.textContent;

            fetch(`../config/get_team_info.php?name=${encodeURIComponent(teamName)}`)
                .then(response => response.json())
                .then(team => {
                    if (team && !team.error) {
                        displayTeamInfo(team);
                    } else {
                        alert("Team not found!");
                    }
                    suggestionsBox.innerHTML = "";
                    searchInput.value = "";
                })
                .catch(err => {
                    console.error("Error fetching team details:", err);
                });
        }
    });

    function displayTeamInfo(team) {
    const container = document.getElementById("team-details-container");

    container.innerHTML = `
        <div class="team-menu">
            <div class="team-tabs">
                <button class="tab-btn active" data-tab="details">Details</button>
                <button class="tab-btn" data-tab="squad">Squad</button>
                <button class="tab-btn" data-tab="coach">Coach</button>

            </div>
            <div id="tab-content" class="tab-content-area"></div>
        </div>
    `;
    container.classList.remove("hidden");

    loadTeamTab("details", team);

    const tabButtons = container.querySelectorAll(".tab-btn");
    tabButtons.forEach(btn => {
        btn.addEventListener("click", function () {
            tabButtons.forEach(b => b.classList.remove("active"));
            this.classList.add("active");

            const selectedTab = this.getAttribute("data-tab");
            loadTeamTab(selectedTab, team);
        });
    });
}

function loadTeamTab(tab, team) {
    const content = document.getElementById("tab-content");

    switch (tab) {
      case "details":
        content.innerHTML = `
        <div class="team-details-header">
          <img src="${team.crest_url}" alt="${team.name} crest" class="team-crest">
          <h3>${team.name}</h3>
          </div>
                <p><strong>League:</strong> ${team.league_name}</p>
                <p><strong>Founded:</strong> ${team.founded_year}</p>
                <p><strong>TLA:</strong> ${team.tla}</p>
                <p><strong>Club Colors:</strong> ${team.club_colors}</p>
                <p><strong>Stadium:</strong> ${team.venue}</p>

                `;
                break;

      case "squad":


              fetch(`../config/get_team_squad.php?team=${encodeURIComponent(team.name)}`)
                  .then(res => res.json())
                  .then(players => {
                      if (!players.length) {
                          content.innerHTML = "<p>No squad data available.</p>";
                          return;
                      }

                      const grouped = {
                          Goalkeepers: [],
                          Defenders: [],
                          Midfielders: [],
                          Strikers: []
                      };

                      players.forEach(p => {
                          const pos = p.position.toLowerCase();

                          if (pos.includes("goalkeeper")) {
                              grouped.Goalkeepers.push(p);
                          } else if (
                              pos.includes("defence") ||
                              pos.includes("centre-back") ||
                              pos.includes("left-back") ||
                              pos.includes("right-back")
                          ) {
                              grouped.Defenders.push(p);
                          } else if (
                              pos.includes("midfield")
                          ) {
                              grouped.Midfielders.push(p);
                          } else if (
                              pos.includes("offence") ||
                              pos.includes("forward") ||
                              pos.includes("winger")
                          ) {
                              grouped.Strikers.push(p);
                          }
                      });

                      content.innerHTML = '';
                      for (const role in grouped) {
                        if (grouped[role].length > 0) {
                          content.innerHTML += `
                          <div class="squad-section">
                          <h4>${role}</h4>
                          <div class="squad-table">
                        <div class="squad-header">
                            <span>#</span>
                            <span>Name</span>
                            <span>Nationality</span>
                              </div>
                        ${grouped[role].map(p => `
                            <div class="squad-row">
                                <span>${p.shirt_number ?? '-'}</span>
                                  <span>${p.name}</span>
                                  <span>${p.nationality}</span>
                                  </div>
                                  `).join('')}
                                  </div>
                                  </div>
                                  `;
                                }
                              }

                  })
                  .catch(err => {
                      content.innerHTML = `<p>Error loading squad data.</p>`;
                      console.error(err);
                  });
              break;

              case "coach":
      fetch(`../config/get_team_coach.php?team=${encodeURIComponent(team.name)}`)
          .then(response => response.json())
          .then(coach => {
              if (!coach || coach.error) {
                  content.innerHTML = "<p>Coach data not available.</p>";
                  return;
              }

              content.innerHTML = `
                  <div class="coach-card">
                      <p>${coach.name}</p>
                      <p><strong>Nationality:</strong> ${coach.nationality}</p>
                      <p><strong>Date of Birth:</strong> ${coach.date_of_birth}</p>
                      <p><strong>Contract Start:</strong> ${coach.contract_start}</p>
                      <p><strong>Contract Until:</strong> ${coach.contract_until}</p>
                  </div>
              `;
          })
          .catch(err => {
              content.innerHTML = "<p>Error loading coach data.</p>";
              console.error("Coach fetch error:", err);
          });
      break;


        case "fixtures":
            content.innerHTML = `<p>Loading fixtures...</p>`;
            break;
        default:
            content.innerHTML = "";
    }
}

});
