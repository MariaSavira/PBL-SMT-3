document.addEventListener("DOMContentLoaded", async function () {
    const dropdown = document.getElementById("profileDropdown");
    const toggle = document.getElementById("profileToggle");

    if (dropdown && toggle) {
        toggle.addEventListener("click", () => {
            dropdown.classList.toggle("open");
        });
    } else {
        console.warn("Dropdown profile tidak ditemukan");
    }
});
