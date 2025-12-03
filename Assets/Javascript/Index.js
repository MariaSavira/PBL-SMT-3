// === AUTO-INJECT CSS TANPA EDIT FILE CSS ===
const style = document.createElement("style");
style.textContent = `
    .auto-muncul {
        opacity: 0;
        transform: translateY(40px);
        transition: 0.7s ease-out;
    }
    .auto-muncul.show {
        opacity: 1 !important;
        transform: translateY(0) !important;
    }
`;
document.head.appendChild(style);

// === PILIH SEMUA SECTION & DIV BESAR SECARA OTOMATIS ===
const elements = document.querySelectorAll("section, .fokus-riset-section, .news-section, .galeri-section, .partnership-section, .profil-lab");

// Tambah class otomatis tanpa ubah HTML
elements.forEach(el => el.classList.add("auto-muncul"));

// === LOGIKA ANIMASI MUNCUL SAAT SCROLL ===
function checkScroll() {
    elements.forEach(el => {
        const pos = el.getBoundingClientRect().top;
        const tinggi = window.innerHeight;

        if (pos < tinggi - 80) {
            el.classList.add("show");
        }
    });
}

window.addEventListener("scroll", checkScroll);
checkScroll();
