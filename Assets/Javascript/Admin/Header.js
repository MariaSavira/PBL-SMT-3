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
        "../../../Admin/Header.php",
        "../../../Assets/Css/Admin/Header.css"
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
});
