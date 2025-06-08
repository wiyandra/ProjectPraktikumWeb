document.addEventListener("DOMContentLoaded", function () {
    const riwayatTableBody = document.getElementById("riwayatTableBody");

    // Helper function to format currency (assuming Rupiah based on previous context)
    function formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
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
                    cell.colSpan = 3; // Changed from 4 to 3 columns
                    cell.textContent = "Error loading history: " + data.error;
                    return;
                }

                if (data.length === 0) {
                    const row = riwayatTableBody.insertRow();
                    const cell = row.insertCell();
                    cell.colSpan = 3; // Changed from 4 to 3 columns
                    cell.textContent = "No transaction history found.";
                    return;
                }

                data.forEach(entry => {
                    const row = riwayatTableBody.insertRow();

                    const tanggalCell = row.insertCell();
                    tanggalCell.textContent = entry.tanggal; // Changed to entry.tanggal

                    const idPembelianCell = row.insertCell();
                    idPembelianCell.textContent = entry.idbeli; // Changed to entry.idbeli

                    // Removed the cell for 'Katalog Pembelian' as it's no longer fetched
                    // const katalogCell = row.insertCell();
                    // katalogCell.textContent = entry.katalog_pembelian;

                    const nominalCell = row.insertCell();
                    nominalCell.textContent = formatRupiah(entry.harga); // Still displaying formatted price
                });
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

    // Call the function to fetch and display data when the page loads
    fetchRiwayatData();
});