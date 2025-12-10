const sortBtn  = document.getElementById("sort-btn");
const sortMenu = document.getElementById("sort-menu");

sortBtn.addEventListener("click", () => {
    sortMenu.classList.toggle("hidden");
});

document.addEventListener("click", (e) => {
    if (!sortMenu.contains(e.target) && !sortBtn.contains(e.target)) {
        sortMenu.classList.add("hidden");
    }
});

sortMenu.querySelectorAll("div[data-sort]").forEach(item => {
    item.addEventListener("click", () => {

        const url = new URL(window.location.href);
        url.searchParams.set("sort", item.dataset.sort);
        url.searchParams.set("page", 1);

        window.location.href = url.toString();
    });
});

document.getElementById("search-input").addEventListener("keyup", function () {
    const url = new URL(window.location.href);
    url.searchParams.set("q", this.value);
    url.searchParams.set("page", 1);
    window.location.href = url.toString();
});
