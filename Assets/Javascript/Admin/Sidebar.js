function loadCSS(url){
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

async function loadComponent(id, file, url){
    loadCSS(url);
    const target = document.getElementById(id);
    const result = await fetch(file); //ambil hasil dari server
    const html = await result.text(); //konversi data mentah menjadi html
    target.innerHTML = html;
    return target;
}

document.addEventListener("DOMContentLoaded", async function () {
    await loadComponent("sidebar", "Sidebar.html", "../,./");
});