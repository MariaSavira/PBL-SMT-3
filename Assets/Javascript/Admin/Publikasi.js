document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.querySelector(".filter-toggle");
    const menu   = document.querySelector(".filter-menu");

    if (toggle && menu) {
        toggle.addEventListener("click", function (e) {
            e.stopPropagation();
            menu.classList.toggle("open");
        });

        document.addEventListener("click", function (e) {
            if (!menu.contains(e.target) && !toggle.contains(e.target)) {
                menu.classList.remove("open");
            }
        });
    }
    
    const sortBtn = document.getElementById("sort-btn");
    const sortMenu = document.getElementById("sort-menu");

    if (sortBtn && sortMenu) {
        sortBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            sortMenu.classList.toggle("hidden");
        });

        document.addEventListener("click", (e) => {
            if (!sortMenu.contains(e.target) && !sortBtn.contains(e.target)) {
                sortMenu.classList.add("hidden");
            }
        });

        sortMenu.querySelectorAll("[data-sort]").forEach(item => {
            item.addEventListener("click", () => {
                const url = new URL(window.location.href);
                url.searchParams.set("sort", item.dataset.sort);
                url.searchParams.set("page", 1);
                window.location.href = url.toString();
            });
        });
    }

    const searchInput = document.getElementById("search-input");

    if (searchInput) {
        let timer = null;

        searchInput.addEventListener("keyup", function () {
            clearTimeout(timer);

            timer = setTimeout(() => {
                const url = new URL(window.location.href);
                url.searchParams.set("q", searchInput.value);
                url.searchParams.set("page", 1);
                window.location.href = url.toString();
            }, 350);
        });
    }

    
    const table = document.querySelector(".table-container");

    function closeAllActionMenus() {
        document.querySelectorAll(".action-menu.open").forEach(menu => {
            menu.classList.remove("open");
        });
    }

    if (table) {
        table.addEventListener("click", function (e) {

            
            const toggleBtn = e.target.closest(".action-toggle");
            if (toggleBtn) {
                const cell = toggleBtn.closest(".action-cell");
                const menu = cell?.querySelector(".action-menu");
                if (!menu) return;

                const isOpen = menu.classList.contains("open");
                closeAllActionMenus();
                if (!isOpen) menu.classList.add("open");

                e.stopPropagation();
                return;
            }

            
            const deleteBtn = e.target.closest(".action-delete");
            if (deleteBtn) {
                const cell = deleteBtn.closest(".action-cell");
                const form = cell?.querySelector(".delete-publikasi-form");
                if (!form) return;

                if (confirm("Yakin ingin menghapus publikasi ini?")) {
                    form.submit();
                }

                e.stopPropagation();
                return;
            }
        });

        document.addEventListener("click", function (e) {
            if (!e.target.closest(".action-cell")) {
                closeAllActionMenus();
            }
        });
    }

    
    const deleteSelectionBtn = document.querySelector(".delete-selection");

    if (deleteSelectionBtn) {
        deleteSelectionBtn.addEventListener("click", () => {

            const checked = document.querySelectorAll(".row-check:checked");
            if (checked.length === 0) {
                alert("Tidak ada data yang dipilih.");
                return;
            }

            if (!confirm("Yakin ingin menghapus publikasi yang dipilih?")) return;

            const ids = Array.from(checked).map(c => c.value);
            const body = ids.map(id => `ids[]=${encodeURIComponent(id)}`).join("&");

            fetch("DeletePublikasi.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body
            })
                .then(res => {
                    if (!res.ok) throw new Error("Gagal menghapus (status " + res.status + ")");
                    return res.text();
                })
                .then(() => location.reload())
                .catch(err => alert("Error: " + err.message));
        });
    }
});
