(() => {
    "use strict";

    function qs(sel, root = document) { return root.querySelector(sel); }
    function qsa(sel, root = document) { return Array.from(root.querySelectorAll(sel)); }

    // =========================
    // Action dropdown (⋮)
    // =========================
    function toggleMenu(event, id) {
        event.stopPropagation();
        const menu = document.getElementById("menu-" + id);
        if (!menu) return;

        const isOpen = menu.classList.contains("show");
        qsa(".action-menu").forEach(m => m.classList.remove("show"));
        if (!isOpen) menu.classList.add("show");
    }

    function initGlobalCloseMenus() {
        document.addEventListener("click", function () {
            qsa(".action-menu").forEach(m => m.classList.remove("show"));
        });
    }

    // =========================
    // Filter dropdown
    // =========================
    function initFilterDropdown() {
        const filterToggle = qs(".filter-toggle");
        const filterMenu = qs(".filter-menu");
        if (!filterToggle || !filterMenu) return;

        filterToggle.addEventListener("click", function (e) {
            e.stopPropagation();
            filterMenu.classList.toggle("open");
        });

        document.addEventListener("click", function (e) {
            if (!filterMenu.contains(e.target) && !filterToggle.contains(e.target)) {
                filterMenu.classList.remove("open");
            }
        });
    }

    // =========================
    // Sort dropdown
    // =========================
    function initSortDropdown() {
        const sortBtn = document.getElementById("sort-btn");
        const sortMenu = document.getElementById("sort-menu");
        if (!sortBtn || !sortMenu) return;

        sortBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            sortMenu.classList.toggle("hidden");
        });

        qsa("[data-sort]", sortMenu).forEach(item => {
            item.addEventListener("click", function () {
                const sort = this.dataset.sort;
                const url = new URL(window.location.href);
                url.searchParams.set("sort", sort);
                url.searchParams.set("page", "1");
                window.location.href = url.toString();
            });
        });

        document.addEventListener("click", function () {
            sortMenu.classList.add("hidden");
        });
    }

    // =========================
    // Search debounce
    // =========================
    function initSearchDebounce() {
        let t;
        const searchInput = qs('input[name="search"]');
        if (!searchInput || !searchInput.form) return;

        searchInput.addEventListener("input", function () {
            clearTimeout(t);
            t = setTimeout(() => this.form.submit(), 450);
        });
    }

    // =========================
    // Bulk selection toast
    // =========================
    function updateToast() {
        const checked = qsa("tbody .checkbox:checked");
        const toast = document.getElementById("deleteToast");
        if (!toast) return;

        if (checked.length > 0) toast.classList.add("show");
        else toast.classList.remove("show");
    }

    function initBulkSelection() {
        const checkAllBox = document.getElementById("checkAll");
        const rowCheckboxes = qsa("tbody .checkbox");

        if (checkAllBox) {
            checkAllBox.addEventListener("change", function () {
                rowCheckboxes.forEach(cb => cb.checked = this.checked);
                updateToast();
            });
        }

        rowCheckboxes.forEach(cb => {
            cb.addEventListener("change", function () {
                updateToast();
                const all = qsa("tbody .checkbox");
                const checked = qsa("tbody .checkbox:checked");
                if (checkAllBox) checkAllBox.checked = (all.length === checked.length && checked.length > 0);
            });
        });
    }

    // =========================
    // SIMPLE CONFIRM delete
    // =========================
    function confirmDelete(id, judul) {
        const ok = confirm(`Yakin ingin menghapus karya "${judul}"?\nTindakan ini tidak dapat dibatalkan.`);
        if (!ok) return false;
        window.location.href = "hapus.php?id=" + encodeURIComponent(id);
        return false;
    }

    function confirmBulkDelete() {
        const form = document.getElementById("bulkDeleteForm");
        const checked = qsa("tbody .checkbox:checked");
        if (checked.length === 0) {
            alert("Pilih setidaknya satu karya untuk dihapus.");
            return false;
        }

        const ok = confirm(`Yakin ingin menghapus ${checked.length} karya yang dipilih?\nTindakan ini tidak dapat dibatalkan.`);
        if (!ok) return false;

        form.submit();
        return false;
    }

    // =========================
    // Boot
    // =========================
    document.addEventListener("DOMContentLoaded", function () {
        initFilterDropdown();
        initSortDropdown();
        initSearchDebounce();
        initBulkSelection();
        initGlobalCloseMenus();
    });

    // expose global untuk inline onclick
    window.toggleMenu = toggleMenu;
    window.confirmDelete = confirmDelete;
    window.confirmBulkDelete = confirmBulkDelete;
})();
