/* Auto Close Popup */
function getQueryParam(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

function showPopup(message) {
    const popup = document.getElementById('popup');
    const popupMessage = document.getElementById('popup-message');

    if (popup && popupMessage) {
        popupMessage.textContent = message;
        popup.style.display = 'flex';
    }
}

function closePopup() {
    const popup = document.getElementById('popup');
    if (popup) {
        popup.style.display = 'none';
    }
}

// Cek parameter di URL ketika halaman dimuat
window.onload = function() {
    const welcome = getQueryParam('welcome');
    const nama = getQueryParam('nama');

    if (welcome === '1' && nama) {
        showPopup(`Selamat datang, ${decodeURIComponent(nama)}! Akun kamu berhasil dibuat.`);
    }
}