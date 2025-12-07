// Assets/Javascript/Admin/Riset.js

document.addEventListener("DOMContentLoaded", function () {

    /* =======================
       1. TOMBOL TAMBAH
       ======================= */
    const btnTambah = document.getElementById("btnTambah");
    if (btnTambah) {
        btnTambah.addEventListener("click", function () {
            // arahkan ke file PHP
            window.location.href = "TambahRiset.php";
        });
    }

    /* =======================
       2. TITIK TIGA -> EditRiset.php?id=RSxx
       ======================= */
    const ellipsisButtons = document.querySelectorAll("td i.fa-ellipsis");
    if (ellipsisButtons.length > 0) {
        ellipsisButtons.forEach(btn => {
            btn.style.cursor = "pointer";

            btn.addEventListener("click", () => {
                const row    = btn.closest("tr");
                const idCell = row ? row.querySelector("td:nth-child(2)") : null;

                if (idCell) {
                    const idRiset = idCell.textContent.trim();
                    window.location.href = `EditRiset.php?id=${encodeURIComponent(idRiset)}`;
                } else {
                    window.location.href = "EditRiset.php";
                }
            });
        });
    }

    /* =======================
       3. PENCARIAN, SORT, FILTER, EXPORT, PAGINATION
       ======================= */
    const tbody         = document.querySelector(".table-container tbody");
    const pageText      = document.getElementById("page-text");
    const searchInput   = document.querySelector(".search-box input");
    const resultCountEl = document.querySelector(".search-box .result-count");
    const filterChip    = document.querySelector(".filter-chip");
    const clearFilter   = document.querySelector(".clear-filter");
    const sortBtn       = document.querySelector(".sort");
    const sortLabel     = sortBtn ? sortBtn.querySelector("strong") : null;
    const exportBtn     = document.querySelector(".export");
    const prevBtn       = document.getElementById("prev-page");
    const nextBtn       = document.getElementById("next-page");

    // state untuk pagination & filter
    let rowsPerPage  = 6;
    let currentPage  = 1;
    let sortState    = "default"; // default | asc | desc
    let emptyRow     = null;      // baris "Belum ada data."
    let allRows      = [];        // semua baris data (punya .row-check)
    let filteredRows = [];        // hasil filter + search

    if (tbody && pageText) {
        const trAll = Array.from(tbody.querySelectorAll("tr"));

        // cari baris kosong (Belum ada data) -> tidak punya .row-check
        emptyRow = trAll.find(tr => !tr.querySelector(".row-check"));
        allRows  = trAll.filter(tr => tr.querySelector(".row-check"));

        // simpan index awal untuk mode "Default"
        allRows.forEach((row, idx) => {
            row.dataset.originalIndex = idx;
        });

        filteredRows = [...allRows];

        /* ---------- helper ---------- */
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

            // cuma efek visual biar kelihatan kalau filter lagi aktif
            if (filterChip) {
                filterChip.style.opacity = active ? "1" : "0.6";
            }
            if (clearFilter) {
                clearFilter.style.visibility = active ? "visible" : "hidden";
            }
        }

        function renderPage() {
            const totalPages = getTotalPages();

            // sembunyikan semua baris data dulu
            allRows.forEach(row => {
                row.style.display = "none";
            });

            // atur tampilan baris kosong
            if (emptyRow) {
                emptyRow.style.display = filteredRows.length === 0 ? "" : "none";
            }

            if (filteredRows.length > 0) {
                filteredRows.forEach((row, index) => {
                    const page = Math.floor(index / rowsPerPage) + 1;
                    if (page === currentPage) {
                        row.style.display = "";
                    }
                });
            }

            if (pageText) {
                if (filteredRows.length === 0) {
                    pageText.textContent = "0 of 0";
                } else {
                    pageText.textContent = `${currentPage} of ${totalPages}`;
                }
            }

            if (prevBtn) prevBtn.disabled = currentPage <= 1 || filteredRows.length === 0;
            if (nextBtn) nextBtn.disabled = currentPage >= totalPages || filteredRows.length === 0;
        }

        function applySearch() {
            const keyword = searchInput ? searchInput.value.trim().toLowerCase() : "";

            filteredRows = allRows.filter(row => {
                const id   = row.cells[1].textContent.toLowerCase();
                const nama = row.cells[2].textContent.toLowerCase();
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
                if (mode === "asc")      sortLabel.textContent = "A–Z";
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
                // kembali ke urutan awal
                rowsToSort.sort((a, b) => {
                    return Number(a.dataset.originalIndex) - Number(b.dataset.originalIndex);
                });
            }

            // susun ulang DOM sesuai urutan baru
            rowsToSort.forEach(row => tbody.appendChild(row));
            allRows = rowsToSort;

            // setelah di-sort, terapkan lagi search yang sedang aktif
            applySearch();
        }

        /* ---------- event: search ---------- */
        if (searchInput) {
            searchInput.addEventListener("input", function () {
                applySearch();
            });
        }

        /* ---------- event: sort ---------- */
        if (sortBtn) {
            sortBtn.addEventListener("click", function () {
                if (sortState === "default")      applySort("asc");
                else if (sortState === "asc")     applySort("desc");
                else                               applySort("default");
            });
        }

        /* ---------- event: hapus filter (link) ---------- */
        if (clearFilter) {
            clearFilter.addEventListener("click", function (e) {
                e.preventDefault();
                if (searchInput) searchInput.value = "";
                applySort("default"); // sekalian reset sort + rerender
            });
        }

        /* ---------- event: hapus filter (ikon X di chip) ---------- */
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

        /* ---------- event: export CSV ---------- */
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

        /* ---------- event: pagination ---------- */
        if (prevBtn) {
            prevBtn.addEventListener("click", function () {
                if (currentPage > 1) {
                    currentPage--;
                    renderPage();
                }
            });
        }

        if (nextBtn) {
            nextBtn.addEventListener("click", function () {
                const totalPages = getTotalPages();
                if (currentPage < totalPages) {
                    currentPage++;
                    renderPage();
                }
            });
        }

        // initial render
        updateResultCount();
        updateFilterUI();
        renderPage();
    }

    /* =======================
       4. CHECK ALL DI HEADER TABEL
       ======================= */
    const checkAll = document.getElementById("check-all");
    if (checkAll) {
        checkAll.addEventListener("change", function () {
            const rowChecks = document.querySelectorAll(".row-check");
            rowChecks.forEach(cb => {
                cb.checked = checkAll.checked;
            });
        });
    }

    /* =======================
       5. TOMBOL HAPUS DATA TERPILIH
       ======================= */
    const btnDelete = document.getElementById("btnDelete");
    if (btnDelete) {
        btnDelete.addEventListener("click", function () {
            const form = document.getElementById("formRiset");
            if (!form) return;

            const checked = form.querySelectorAll(".row-check:checked");
            if (checked.length === 0) {
                alert("Pilih dulu minimal satu data yang ingin dihapus.");
                return;
            }

            if (confirm("Yakin ingin menghapus data yang dipilih?")) {
                form.submit();
            }
        });
    }

    /* BAGIAN AUTO-FILL EDIT DIHAPUS
       karena sekarang EditRiset.php sudah ambil data langsung dari database */
});
