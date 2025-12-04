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
    const result = await fetch(file); //ambil hasil dari server
    const html = await result.text(); //konversi data mentah menjadi html
    // console.log(target);
    // console.log(html);
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

    window.addEventListener("load", function () {
        const hero = document.querySelector("#hero");

        if (filename === "Index.html" && hero) {
            const scrollTrigger = hero.offsetHeight;
            console.log(scrollTrigger);

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
    });

    if (toggle && dropdown) {
        toggle.addEventListener("click", (e) => {
            e.preventDefault();
            dropdown.classList.toggle("active");
        });

        document.addEventListener("click", (e) => {
            if (!dropdown.contains(e.target)) {
                dropdown.classList.remove("active");
            }
        });
    }
});
