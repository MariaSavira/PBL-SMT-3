document.addEventListener("DOMContentLoaded", function () {
    const navbar = document.querySelector(".navbar");
    const menu = document.querySelector(".menu");
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
