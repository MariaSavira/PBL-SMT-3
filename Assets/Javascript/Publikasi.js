document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("filtersForm");
    const q = document.getElementById("searchInput");
    const tag = document.getElementById("tagSelect");
    const year = document.getElementById("yearSelect");

    if (!form) return;

    const submitNow = () => {
        const url = new URL(window.location.href);
        url.searchParams.set("page", "1");

        if (q) url.searchParams.set("q", q.value.trim());
        if (tag) url.searchParams.set("tag", tag.value.trim());
        if (year) url.searchParams.set("year", year.value.trim());

        ["q", "tag", "year"].forEach(k => {
            if (!url.searchParams.get(k)) url.searchParams.delete(k);
        });

        window.location.href = url.toString();
    };

    if (tag) tag.addEventListener("change", submitNow);
    if (year) year.addEventListener("change", submitNow);

    let t = null;
    if (q) {
        q.addEventListener("input", () => {
            clearTimeout(t);
            t = setTimeout(submitNow, 400);
        });
    }
});
