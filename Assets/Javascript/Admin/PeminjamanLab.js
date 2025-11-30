document.addEventListener("DOMContentLoaded", () => {
    const btn = document.querySelector(".status-trigger");
    const dropdown = document.querySelector(".status-dropdown");

    // Klik tombol → show/hide dropdown
    btn.addEventListener("click", (e) => {
        e.stopPropagation();
        dropdown.style.display =
            dropdown.style.display === "block" ? "none" : "block";
    });

    // Klik luar → dropdown hilang
    document.addEventListener("click", () => {
        dropdown.style.display = "none";
    });

    // Pilih item
    dropdown.querySelectorAll("div").forEach(item => {
        item.addEventListener("click", () => {
            console.log("Kamu memilih:", item.dataset.value);
            dropdown.style.display = "none";
        });
    });
});
