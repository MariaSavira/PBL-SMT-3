document.addEventListener("DOMContentLoaded", function () {

    /* BUTTON TAMBAH -> TambahRiset.php */
    const btnTambah = document.getElementById("btnTambah");
    if (btnTambah) {
        btnTambah.addEventListener("click", function () {
            // arahkan ke file PHP, bukan HTML
            window.location.href = "TambahRiset.php";
        });
    }

    /* TITIK TIGA -> EditRiset.php?id=RSxx
       (dipakai di IndexRiset.php) */
    const ellipsisButtons = document.querySelectorAll("td i.fa-ellipsis");
    if (ellipsisButtons.length > 0) {
        ellipsisButtons.forEach(btn => {
            btn.style.cursor = "pointer";

            btn.addEventListener("click", () => {
                // cari <tr> terdekat
                const row = btn.closest("tr");
                // ambil teks id_riset dari kolom ke-2
                const idCell = row ? row.querySelector("td:nth-child(2)") : null;

                if (idCell) {
                    const idRiset = idCell.textContent.trim(); // contoh: "RS01"
                    // kirim id lewat query string ke EditRiset.php
                    window.location.href = `EditRiset.php?id=${encodeURIComponent(idRiset)}`;
                } else {
                    // fallback kalau struktur tabel berubah
                    window.location.href = "EditRiset.php";
                }
            });
        });
    }

    /* PAGINATION (cuma jalan di IndexRiset.php) */
    const tbody = document.querySelector(".table-container tbody");
    const pageText = document.getElementById("page-text");

    if (tbody && pageText) { // supaya nggak error di halaman lain
        const rows = Array.from(tbody.querySelectorAll("tr"));

        const rowsPerPage = 6;
        const totalPages = Math.max(1, Math.ceil(rows.length / rowsPerPage));
        let currentPage = 1;

        const prevBtn = document.getElementById("prev-page");
        const nextBtn = document.getElementById("next-page");

        function renderPage() {
            rows.forEach((row, index) => {
                const page = Math.floor(index / rowsPerPage) + 1;
                row.style.display = (page === currentPage) ? "" : "none";
            });

            pageText.textContent = `${currentPage} of ${totalPages}`;

            if (prevBtn) prevBtn.disabled = currentPage === 1;
            if (nextBtn) nextBtn.disabled = currentPage === totalPages;
        }

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
                if (currentPage < totalPages) {
                    currentPage++;
                    renderPage();
                }
            });
        }

        renderPage();
    }

    /* BAGIAN AUTO-FILL EDIT DIHAPUS
       karena sekarang EditRiset.php sudah ambil data langsung dari database */
});
