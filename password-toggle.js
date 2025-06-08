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