document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("filtersForm");
    const q = document.getElementById("searchInput");
    const year = document.getElementById("yearSelect");

    if (!form) return;

    if (year) {
        year.addEventListener("change", () => {

            const page = form.querySelector('input[name="page"]');
            if (page) page.value = "1";
            form.submit();
        });
    }

    let t = null;
    if (q) {
        q.addEventListener("input", () => {
            clearTimeout(t);
            t = setTimeout(() => {

                const page = form.querySelector('input[name="page"]');
                if (page) page.value = "1";
                form.submit();
            }, 400);
        });
    }
});
