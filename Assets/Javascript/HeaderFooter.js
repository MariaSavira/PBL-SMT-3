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
    const header = await loadComponent("header", "Header.html", "../Assets/Css/HeaderFooter.css");
    await loadComponent("footer", "Footer.html", "../Assets/Css/HeaderFooter.css");
    const navbar = header.querySelector(".navbar");
    const dropdown = header.querySelector(".dropdown");
    const toggle = header.querySelector(".dropdown-toggle");
    const filename = window.location.pathname.split("/").pop();
    const hero = document.querySelector("#hero");
    const scrollTrigger = hero ? hero.offsetHeight : 0;

    if (filename === "Index.html" && hero) {
        window.addEventListener("scroll", function () {
            if (window.scrollY >= scrollTrigger) {
                navbar.classList.add("navbar-scrolled");
                navbar.style.position = "fixed";
            } else {
                navbar.classList.remove("navbar-scrolled");
                navbar.style.position = "absolute";
            }
        });
    } else {
        navbar.classList.add("navbar-scrolled");
        navbar.style.position = "fixed";
    }

    document.querySelectorAll(".menu span a").forEach(a => {
        if (a.getAttribute("href") === filename) {
            a.classList.add("active");
        } 
    });

    const menuLogin = document.getElementById('menuLogin');
    console.log(menuLogin);
    
    document.addEventListener("keydown", (e) => {
        if (e.ctrlKey && e.key === "l") {
            e.preventDefault();
            
            if (menuLogin) {
                if (menuLogin.style.display === "block") {
                    menuLogin.style.display = "none";
                } else {
                    menuLogin.style.display = "block"; 
                }
            } 
        } 
    });

    if (toggle && dropdown) {
        toggle.addEventListener("click", (e) => {
            e.preventDefault();
            dropdown.classList.toggle("active");
        });
        document.addEventListener("click", (e) => {
            if (!dropdown.contains(e.target) && !toggle.contains(e.target)) {
                dropdown.classList.remove("active");
            }
        });
    }
});
