const currentPage = window.location.pathname;

function loadCSS(url) {
    const link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = url;
    document.head.appendChild(link);
}

async function loadComponent(id, file, url) { 
    loadCSS(url);
    const target = document.getElementById(id);
    const result = await fetch(file); 
    const html = await result.text(); 
    target.innerHTML = html;
    return target;
}

document.addEventListener("DOMContentLoaded", async function () {
    await loadComponent(
        "sidebar",
        "/PBL-SMT-3/Admin/Sidebar.html",
        "/PBL-SMT-3/Assets/Css/Admin/Sidebar.css"); 

    const sidebar = document.getElementById("ini-sidebar");
    const content = document.getElementById("content");
    const logo = document.getElementById("sidebar-toggle");

    logo.addEventListener("click", () => {
        
        sidebar.classList.toggle("collapsed");
        content.classList.toggle("collapsed");
    });

    document.querySelectorAll("li a").forEach(a => {
  
        const link = a.getAttribute("href");

        if (a.getAttribute("href") === currentPage.split("/")) {
            a.classList.add("active");
        }
    });

    let currentFile = window.location.pathname.split("/").pop();

    const menuItems = document.querySelectorAll(".sidebar-menu .menu-item a");

    menuItems.forEach(link => {
        
        let linkFile = link.getAttribute("href").split("/").pop();

        if (currentFile === linkFile) {
            link.parentElement.classList.add("active");
        }
    });
});
