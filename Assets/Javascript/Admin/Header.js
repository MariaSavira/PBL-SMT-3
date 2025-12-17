function loadCSS(url) {
    const link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = url;
    document.head.appendChild(link);
}

async function loadComponent(id, file, cssUrl) {
    loadCSS(cssUrl);

    const target = document.getElementById(id);
    const response = await fetch(file);
    const html = await response.text();

    target.innerHTML = html;
    return target;
}

document.addEventListener("DOMContentLoaded", async function () {
    
    const headerEl = await loadComponent(
        "header",
        "/PBL-SMT-3/Admin/Header.php",
        "/PBL-SMT-3/Assets/Css/Admin/Header.css"
    );

    const dropdown = document.getElementById("profileDropdown");
    const toggle = document.getElementById("profileToggle");

    if (dropdown && toggle) {
        toggle.addEventListener("click", () => {
            dropdown.classList.toggle("open");
        });
    } else {
        console.warn("Dropdown profile tidak ditemukan");
    }
    
    const currentPath = window.location.pathname.toLowerCase();

    const headerTitle = headerEl ? headerEl.querySelector(".header-title") : null;

    if (headerTitle) {
        const halamanForm =
            currentPath.includes("edit") ||
            currentPath.includes("tambah") ||
            currentPath.includes("create");

        if (halamanForm) {
            headerTitle.style.visibility = "hidden";
        } else {
            headerTitle.style.visibility = ""; 
        }
    }

    let titleText = "Laboratorium Business Analytics";

    if (currentPath.includes("/anggotalab/")) {
        titleText = "Anggota Laboratorium";
    } else if (currentPath.includes("/publikasi/")) {
        titleText = "Publikasi Laboratorium";
    } else if (currentPath.includes("/risetlab/")) {
        titleText = "Riset Laboratorium";
    } else if (currentPath.includes("/galeri_lab/")) {
        titleText = "Galeri Laboratorium";
    } else if (currentPath.includes("/berita_lab/")) {
        titleText = "Berita Laboratorium";
    } else if (currentPath.includes("/karya/")) {
        titleText = "Karya Laboratorium";
    } else if (currentPath.includes("/peminjamanlab/")) {
        titleText = "Peminjaman Laboratorium";
    } else if (currentPath.includes("/pengumuman_lab/")) {
        titleText = "Pengumuman Laboratorium";
    } else if (currentPath.endsWith("/dashboard.php")) {
        titleText = "Dashboard";
    }

    headerTitle.textContent = titleText;
});
