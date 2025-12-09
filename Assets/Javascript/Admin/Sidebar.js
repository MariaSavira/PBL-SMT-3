const currentPage = window.location.pathname;

function loadCSS(url) {
    const link = document.createElement("link");
    link.rel = "stylesheet";
    link.href = url;
    document.head.appendChild(link);
}
// document.querySelectorAll(".menu span").forEach(a => {
//     if (a.getAttribute("href") === currentPage.split("/").pop()) {
//         a.classList.add("active");

//         console.log(a.getAttribute())
//     }
// });

async function loadComponent(id, file, url) { //async function maksundya asinkronus, dia hasilnya terlambat
    loadCSS(url);
    const target = document.getElementById(id);
    const result = await fetch(file); //ambil hasil dari server
    const html = await result.text(); //konversi data mentah menjadi html
    target.innerHTML = html;
    return target;
}

document.addEventListener("DOMContentLoaded", async function () {
    await loadComponent(
        "sidebar",
        "../../../Admin/Sidebar.html",
        "../../../Assets/Css/Admin/Sidebar.css"); //fungsi async bisa pake await

    const sidebar = document.getElementById("ini-sidebar");
    const content = document.getElementById("content");
    const logo = document.getElementById("sidebar-toggle");

    logo.addEventListener("click", () => {
        // sidebar.classList.toggle("sidebar");
        sidebar.classList.toggle("collapsed");
        content.classList.toggle("collapsed");
    });

    document.querySelectorAll("li a").forEach(a => {
        // if (a.getAttribute("href") === currentPage.split("/")) {
        //     a.classList.add("active");
        //     console.log(a.getAttribute("href"))
        // }
        const link = a.getAttribute("href");

        if (a.getAttribute("href") === currentPage.split("/")) {
            a.classList.add("active");
        }
    });
});
