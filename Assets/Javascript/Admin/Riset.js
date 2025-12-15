document.addEventListener("DOMContentLoaded", function () {
    // ================== TOMBOL TAMBAH ==================
    const btnTambah = document.getElementById("btnTambah");
    if (btnTambah) {
        btnTambah.addEventListener("click", function () {
            window.location.href = "TambahRiset.php";
        });
    }

    // ================== ELEMENT UTAMA ==================
    const tbody         = document.querySelector(".table-container tbody");
    const searchInput   = document.querySelector(".search-box input");
    const resultCountEl = document.querySelector(".search-box .result-count");
    const filterChip    = document.querySelector(".filter-chip");
    const clearFilter   = document.querySelector(".clear-filter");
    const sortBtn       = document.querySelector(".sort");
    const sortLabel     = sortBtn ? sortBtn.querySelector("strong") : null;
    const exportBtn     = document.querySelector(".export");
    const pagination    = document.querySelector(".pagination");

    if (!tbody) return;

    // ================== STATE PAGINATION & FILTER ==================
    const rowsPerPage = 6; // harus sama dengan PHP $limit
    let currentPage   = 1;
    let sortState     = "default";
    let emptyRow      = null;
    let allRows       = [];
    let filteredRows  = [];

    // set currentPage awal dari URL (?page=)
    try {
        const urlPage = new URL(window.location.href).searchParams.get("page");
        const parsed  = parseInt(urlPage || "1", 10);
        if (!isNaN(parsed) && parsed > 0) {
            currentPage = parsed;
        }
    } catch (e) {
        currentPage = 1;
    }

    const trAll = Array.from(tbody.querySelectorAll("tr"));

    emptyRow = trAll.find(tr => !tr.querySelector(".row-check"));
    allRows  = trAll.filter(tr => tr.querySelector(".row-check"));

    allRows.forEach((row, idx) => {
        row.dataset.originalIndex = idx;
    });

    filteredRows = [...allRows];

    // ================== FUNCTION HELPER ==================
    function getTotalPages() {
        return Math.max(1, Math.ceil(filteredRows.length / rowsPerPage));
    }

    function updateResultCount() {
        if (resultCountEl) {
            resultCountEl.textContent = filteredRows.length + " hasil";
        }
    }

    function updateFilterUI() {
        const hasSearch = searchInput && searchInput.value.trim() !== "";
        const hasSort   = sortState !== "default";
        const active    = hasSearch || hasSort;

        if (filterChip) {
            filterChip.style.opacity = active ? "1" : "0.6";
        }
        if (clearFilter) {
            clearFilter.style.visibility = active ? "visible" : "hidden";
        }
    }

    function getPageFromHref(href) {
        try {
            const url = new URL(href, window.location.href);
            const p   = parseInt(url.searchParams.get("page") || "1", 10);
            return isNaN(p) ? 1 : p;
        } catch (e) {
            return 1;
        }
    }

    function updateActiveLink() {
        if (!pagination) return;
        const links = pagination.querySelectorAll(".page-link");
        links.forEach(link => link.classList.remove("active"));

        links.forEach(link => {
            const target = getPageFromHref(link.href);
            if (target === currentPage) {
                link.classList.add("active");
            }
        });
    }

    function updatePrevNext() {
        if (!pagination) return;

        const prevLink   = pagination.querySelector(".page-link.prev");
        const nextLink   = pagination.querySelector(".page-link.next");
        const totalPages = getTotalPages();

        if (prevLink) {
            const isDisabled = currentPage <= 1 || totalPages === 0;
            prevLink.classList.toggle("disabled", isDisabled);
            prevLink.setAttribute("aria-disabled", isDisabled ? "true" : "false");
            prevLink.style.pointerEvents = isDisabled ? "none" : "";
        }

        if (nextLink) {
            const isDisabled = currentPage >= totalPages || totalPages === 0;
            nextLink.classList.toggle("disabled", isDisabled);
            nextLink.setAttribute("aria-disabled", isDisabled ? "true" : "false");
            nextLink.style.pointerEvents = isDisabled ? "none" : "";
        }
    }

    function renderPage() {
        const totalPages = getTotalPages();

        allRows.forEach(row => {
            row.style.display = "none";
        });

        if (emptyRow) {
            emptyRow.style.display = filteredRows.length === 0 ? "" : "none";
        }

        filteredRows.forEach((row, index) => {
            const page = Math.floor(index / rowsPerPage) + 1;
            if (page === currentPage) {
                row.style.display = "";
            }
        });

        updateActiveLink();
        updatePrevNext();
    }

    function applySearch() {
        const keyword = searchInput ? searchInput.value.trim().toLowerCase() : "";

        filteredRows = allRows.filter(row => {
            const id   = row.cells[1]?.textContent.toLowerCase() || "";
            const nama = row.cells[2]?.textContent.toLowerCase() || "";
            return id.includes(keyword) || nama.includes(keyword);
        });

        currentPage = 1;
        updateResultCount();
        updateFilterUI();
        renderPage();
    }

    function applySort(mode) {
        sortState = mode;

        if (sortLabel) {
            if (mode === "asc")       sortLabel.textContent = "A–Z";
            else if (mode === "desc") sortLabel.textContent = "Z–A";
            else                      sortLabel.textContent = "Default";
        }

        let rowsToSort = [...allRows];

        if (mode === "asc" || mode === "desc") {
            rowsToSort.sort((a, b) => {
                const nameA = a.cells[2].textContent.trim().toLowerCase();
                const nameB = b.cells[2].textContent.trim().toLowerCase();
                if (nameA < nameB) return mode === "asc" ? -1 : 1;
                if (nameA > nameB) return mode === "asc" ?  1 : -1;
                return 0;
            });
        } else {
            rowsToSort.sort((a, b) => {
                return Number(a.dataset.originalIndex) - Number(b.dataset.originalIndex);
            });
        }

        rowsToSort.forEach(row => tbody.appendChild(row));
        allRows = rowsToSort;

        applySearch();
    }

    // ================== EVENT LISTENERS ==================
    if (searchInput) {
        searchInput.addEventListener("input", applySearch);
    }

    if (sortBtn) {
        sortBtn.addEventListener("click", function () {
            if (sortState === "default")      applySort("asc");
            else if (sortState === "asc")     applySort("desc");
            else                              applySort("default");
        });
    }

    if (clearFilter) {
        clearFilter.addEventListener("click", function (e) {
            e.preventDefault();
            if (searchInput) searchInput.value = "";
            applySort("default");
        });
    }

    if (filterChip) {
        const removeIcon = filterChip.querySelector(".remove-chip");
        if (removeIcon) {
            removeIcon.style.cursor = "pointer";
            removeIcon.addEventListener("click", function () {
                if (searchInput) searchInput.value = "";
                applySort("default");
            });
        }
    }

    if (exportBtn) {
        exportBtn.addEventListener("click", function () {
            if (!filteredRows || filteredRows.length === 0) {
                alert("Tidak ada data untuk diexport.");
                return;
            }

            let csv = "id_riset,nama_riset\n";
            filteredRows.forEach(row => {
                const id   = row.cells[1].textContent.trim();
                const nama = row.cells[2].textContent.trim();
                const safeNama = '"' + nama.replace(/"/g, '""') + '"';
                csv += `${id},${safeNama}\n`;
            });

            const blob = new Blob([csv], { type: "text/csv;charset=utf-8;" });
            const url  = URL.createObjectURL(blob);
            const a    = document.createElement("a");
            a.href     = url;
            a.download = "data_riset.csv";
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        });
    }

    // Pagination
    if (pagination) {
        pagination.addEventListener("click", function (e) {
            const link = e.target.closest(".page-link");
            if (!link) return;

            if (link.classList.contains("disabled")) {
                e.preventDefault();
                return;
            }

            e.preventDefault();

            const totalPages = getTotalPages();

            if (link.classList.contains("prev")) {
                if (currentPage > 1) {
                    currentPage--;
                    renderPage();
                }
            } else if (link.classList.contains("next")) {
                if (currentPage < totalPages) {
                    currentPage++;
                    renderPage();
                }
            } else {
                const targetPage = getPageFromHref(link.href);
                if (targetPage >= 1 && targetPage <= totalPages) {
                    currentPage = targetPage;
                    renderPage();
                }
            }
        });
    }

    // Checkbox "check all"
    const checkAll = document.getElementById("check-all");
    if (checkAll) {
        checkAll.addEventListener("change", function () {
            const rowChecks = document.querySelectorAll(".row-check");
            rowChecks.forEach(cb => {
                cb.checked = checkAll.checked;
            });
        });
    }

    // BULK DELETE: cek dulu ada yang kepilih
    const btnDelete = document.getElementById("btnDelete");
    if (btnDelete) {
        btnDelete.addEventListener("click", function (e) {
            const form = document.getElementById("formRiset");
            if (!form) return;

            const checked = form.querySelectorAll(".row-check:checked");
            if (checked.length === 0) {
                e.preventDefault();
                alert("Pilih dulu minimal satu data yang ingin dihapus.");
                return;
            }

            if (!confirm("Yakin ingin menghapus data yang dipilih?")) {
                e.preventDefault();
            }
        });
    }

    // ================== ACTION MENU (per-row) ==================
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
        });

        document.addEventListener("click", function (e) {
            if (!e.target.closest(".action-cell")) {
                closeAllActionMenus();
            }
        });
    }

    // ================== INIT AWAL ==================
    updateResultCount();
    updateFilterUI();
    renderPage();

    // (optional) kalau nanti kamu punya filter dropdown
    const toggleFilter = document.querySelector(".filter-toggle");
    const filterMenu   = document.querySelector(".filter-menu");

    if (toggleFilter && filterMenu) {
        toggleFilter.addEventListener("click", function (e) {
            e.stopPropagation();
            filterMenu.classList.toggle("open");
        });

        document.addEventListener("click", function (e) {
            if (!filterMenu.contains(e.target) && !toggleFilter.contains(e.target)) {
                filterMenu.classList.remove("open");
            }
        });
    }
});
