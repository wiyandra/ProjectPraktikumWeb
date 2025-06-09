document.addEventListener("DOMContentLoaded", function () {
    const riwayatTableBody = document.getElementById("riwayatTableBody");
    const searchIdInput = document.getElementById("searchIdInput");
    const searchIdButton = document.getElementById("searchIdButton");

    // Helper function to format currency (assuming Rupiah based on previous context)
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    }

    function renderTable(data) {
        riwayatTableBody.innerHTML = ""; // Clear existing rows

        if (data.length === 0) {
            const row = riwayatTableBody.insertRow();
            const cell = row.insertCell();
            cell.colSpan = 3;
            cell.textContent = "No transaction history found.";
            return;
        }

        data.forEach(entry => {
            const row = riwayatTableBody.insertRow();

            const tanggalCell = row.insertCell();
            tanggalCell.textContent = entry.tanggal;

            const idPembelianCell = row.insertCell();
            idPembelianCell.textContent = entry.idbeli;

            const nominalCell = row.insertCell();
            nominalCell.textContent = formatRupiah(entry.harga);
        });
    }

    function fetchRiwayatData() {
        if (!riwayatTableBody) {
            console.error("Element with ID 'riwayatTableBody' not found.");
            return;
        }

        fetch("get_riwayat.php")
            .then(response => {
                if (!response.ok) {
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.indexOf("application/json") !== -1) {
                        return response.json().then(err => {
                            if (err.not_logged_in) {
                                alert("You need to be logged in to view your transaction history.");
                                window.location.href = "login.html";
                                throw new Error("Not logged in, redirecting.");
                            }
                            throw new Error(err.error || `HTTP error! status: ${response.status}`);
                        });
                    } else {
                        throw new Error(`HTTP error! status: ${response.status} - Non-JSON response.`);
                    }
                }
                return response.json();
            })
            .then(data => {
                riwayatTableBody.innerHTML = ""; // Clear existing rows

                if (data.error) {
                    const row = riwayatTableBody.insertRow();
                    const cell = row.insertCell();
                    cell.colSpan = 3;
                    cell.textContent = "Error loading history: " + data.error;
                    return;
                }

                // Store the full data for client-side filtering
                allRiwayatData = data; 

                // Render the initial table with all data
                renderTable(allRiwayatData);
            })
            .catch(error => {
                console.error("Error fetching transaction history:", error);
                if (error.message !== "Not logged in, redirecting.") {
                    if (riwayatTableBody) {
                        riwayatTableBody.innerHTML = `<tr><td colspan="3">Failed to load transaction history. Please try again later.</td></tr>`; // Changed colspan to 3
                    }
                }
            });
    }

    // Search functionality
    function performSearch() {
        const searchTerm = searchIdInput.value.toLowerCase().trim();
        const filteredData = allRiwayatData.filter(entry =>
            entry.idbeli.toLowerCase().includes(searchTerm)
        );
        renderTable(filteredData);
    }
    
    // Store all data for client-side filtering
    if (searchIdButton) {
        searchIdButton.addEventListener("click", performSearch);
    } else {
        console.error("Search button (id='searchIdButton') not found.");
    }

    if (searchIdInput) {
        searchIdInput.addEventListener("keyup", function(event) {
            if (event.key === "Enter") {
                performSearch();
            }
        });
    } else {
        console.error("Search input (id='searchIdInput') not found.");
    }

    fetchRiwayatData();
});