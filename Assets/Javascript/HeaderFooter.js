const currentPage = window.location.pathname;

function loadCSS(url){
    const link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = url;
    document.head.appendChild(link);
}

document.querySelectorAll(".menu span").forEach(a => {
    if (a.getAttribute("href") === currentPage.split("/").pop()) {
        a.classList.add("active");

        console.log(a.getAttribute())
    }
});

async function loadComponent(id, file, url){
    loadCSS(url);
    const target = document.getElementById(id);
    const result = await fetch(file); //ambil hasil dari server
    const html = await result.text(); //konversi data mentah menjadi html
    target.innerHTML = html;
    return target;
}

document.addEventListener("DOMContentLoaded", async function () {
    await loadComponent("header", "Header.html", "../Assets/Css/HeaderFooter.css");
    // loadCSS("../Assets/Css/HeaderFooter.css");
    await loadComponent("footer", "Footer.html", "../Assets/Css/HeaderFooter.css");

    const navbar = document.querySelector(".navbar");
    // const menu = document.querySelector(".menu");
    const hero = document.querySelector("#hero");

    window.addEventListener("scroll", function () {
        const scrollTrigger = hero.offsetHeight;
        
        if (window.scrollY >= scrollTrigger) {
            // menu.classList.add("menu-scrolled");
            navbar.classList.add("navbar-scrolled");
            navbar.style.position = "fixed";
        } else {
            // menu.classList.remove("menu-scrolled");
            navbar.classList.remove("navbar-scrolled");
            navbar.style.position = "absolute";
        }
    });
});

const menuLogin = document.getElementById('login');

document.addEventListener("keydown", (e) => {
    if (e.ctrlKey && e.key === "l") {
        e.preventDefault();
            menuLogin.style.display = block;
    }
});