/* Leaderboard Page Specific JavaScript */
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const resultsContainer = document.getElementById("searchResults");
    const leaderboardTableBody = document.getElementById("leaderboardTableBody");
    const profileToggle = document.getElementById("profileToggle");
    const profileDropdown = document.getElementById("profileDropdown");

    let games = [];

    // --- Profile Dropdown Toggle ---
    if (profileToggle) {
        profileToggle.addEventListener("click", function() {
            if (profileDropdown) {
                profileDropdown.classList.toggle("show");
            }
        });
    }

    // Close the dropdown if the user clicks outside of it
    window.addEventListener("click", function(event) {
        if (profileToggle && !profileToggle.contains(event.target) && profileDropdown && !profileDropdown.contains(event.target)) {
            if (profileDropdown.classList.contains('show')) {
                profileDropdown.classList.remove('show');
            }
        }
    });

    // --- Search Functionality ---
    if (searchInput) {
        fetch("get_games.php")
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                games = data;
            })
            .catch(error => {
                console.error("Gagal mengambil data games:", error);
            });

        searchInput.addEventListener("input", function () {
            const query = this.value.toLowerCase();
            if (resultsContainer) {
                resultsContainer.innerHTML = "";
            }

            if (query.length > 0) {
                const filteredGames = games.filter(game => game.toLowerCase().startsWith(query));

                filteredGames.forEach(game => {
                    const listItem = document.createElement("li");
                    listItem.textContent = game;
                    listItem.classList.add("search-result-item");
                    listItem.onclick = function () {
                        if (game.toLowerCase() === "mobile legends") {
                            window.location.href = "Top UP ML/topupfix.html";
                        } else {
                            alert("Halaman topup belum tersedia untuk: " + game);
                        }
                    };
                    if (resultsContainer) {
                        resultsContainer.appendChild(listItem);
                    }
                });
            }
        });
    }

    document.addEventListener("click", function (event) {
        if (searchInput && resultsContainer && !searchInput.contains(event.target) && !resultsContainer.contains(event.target)) {
            resultsContainer.innerHTML = "";
        }
    });

    // --- JavaScript for Leaderboard ---
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    function fetchLeaderboardData() {
        fetch("get_leaderboard.php")
            .then(response => {
                if (!response.ok) {
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        return response.json().then(err => { throw new Error(err.error || `HTTP error! status: ${response.status}`); });
                    } else {
                        throw new Error(`HTTP error! status: ${response.status} - Non-JSON response.`);
                    }
                }
                return response.json();
            })
            .then(data => {
                if (leaderboardTableBody) {
                    leaderboardTableBody.innerHTML = "";

                    if (data.error) {
                        const row = leaderboardTableBody.insertRow();
                        const cell = row.insertCell();
                        cell.colSpan = 3;
                        cell.textContent = "Error loading leaderboard: " + data.error;
                        return;
                    }

                    if (data.length === 0) {
                        const row = leaderboardTableBody.insertRow();
                        const cell = row.insertCell();
                        cell.colSpan = 3;
                        cell.textContent = "No data available for the leaderboard.";
                        return;
                    }

                    data.forEach((entry, index) => {
                        const row = leaderboardTableBody.insertRow();
                        const rank = index + 1;

                        const nameCell = row.insertCell();
                        nameCell.textContent = `${rank}. ${entry.nama}`;

                        const totalOrdersCell = row.insertCell();
                        totalOrdersCell.textContent = `${entry.total_pesanan} Pesanan`;

                        const totalMoneyCell = row.insertCell();
                        totalMoneyCell.textContent = formatRupiah(entry.total_pengeluaran_pesanan);
                    });
                }
            })
            .catch(error => {
                console.error("Error fetching leaderboard data:", error);
                if (leaderboardTableBody) {
                    leaderboardTableBody.innerHTML = `<tr><td colspan="3">Failed to load leaderboard. Please try again later.</td></tr>`;
                }
            });
    }

    if (leaderboardTableBody) {
        fetchLeaderboardData();
    }
});