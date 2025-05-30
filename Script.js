/* See Password Toggle */
document.addEventListener("DOMContentLoaded", function () {
    const toggles = document.querySelectorAll(".password-toggle");

    toggles.forEach(toggle => {
        const targetId = toggle.getAttribute("data-target");
        const input = document.getElementById(targetId);

        toggle.addEventListener("click", function () {
            const isPassword = input.type === "password";
            input.type = isPassword ? "text" : "password";

            toggle.src = isPassword ? "Element/Icon/eyeopen.png" : "Element/Icon/eyeclosed.png";
            toggle.alt = isPassword ? "Hide Password" : "Show Password";
        });
    });
});

/* Form Validation Data Type */
document.addEventListener("DOMContentLoaded", function () {
    const phoneInput = document.getElementById("phone");

    phoneInput.addEventListener("input", function () {
        // Hapus semua karakter yang bukan angka
        this.value = this.value.replace(/\D/g, "");
    });

    const form = document.querySelector("form");
    
    form.addEventListener("submit", function (e) {
    const phone = phoneInput.value;
    if (phone.length < 10 || phone.length > 15) {
        alert("Nomor HP harus 10 hingga 15 digit.");
        e.preventDefault();
    }
    });
});

/* Form Email Validation */
document.getElementById("email").addEventListener("input", function () {
    const email = this.value;
    const emailPattern = /^[a-zA-Z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$/;

    if (!emailPattern.test(email)) {
        this.setCustomValidity("Format email tidak valid.");
    } else {
        this.setCustomValidity("");
    }
});

