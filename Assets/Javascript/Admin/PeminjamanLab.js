const dirtyRows = new Set();

//  POPUP VARIABLES 
let editingId = null;
const modal = document.getElementById("catatan-modal");
const modalTextarea = document.getElementById("catatan-modal-text");

//  KLIK IKON PENSIL → MUNCUL POPUP 
document.querySelectorAll('.edit-catatan').forEach(icon => {
    icon.addEventListener('click', () => {
        editingId = icon.dataset.id;

        const currentText = document.getElementById(`catatan-text-${editingId}`).innerText.trim();
        modalTextarea.value = currentText;

        // tambahkan class .open untuk menampilkan modal
        modal.classList.add('open');
    });
});

// TOMBOL BATAL 
document.getElementById("catatan-modal-cancel").addEventListener("click", () => {
    modal.classList.remove('open'); // hapus class .open supaya modal tersembunyi
    editingId = null;
});

// TOMBOL SIMPAN CATATAN DI POPUP 
document.getElementById("catatan-modal-save").addEventListener("click", () => {
    if (!editingId) return;

    const newText = modalTextarea.value;

    // Update di tampilan tabel
    document.getElementById(`catatan-text-${editingId}`).innerText = newText;

    // Simpan ke hidden input (yang nanti ikut dikirim ke DB)
    document.getElementById(`catatan-input-${editingId}`).value = newText;

    // Tutup modal
    modal.classList.remove('open');

    alert("Catatan berhasil diperbarui.");
});


// TOMBOL SIMPAN 
document.querySelectorAll('.btn-save').forEach(btn => {
    btn.addEventListener('click', () => {

        let id = btn.dataset.id;
        let status = document.querySelector(`.status-dropdown[data-id="${id}"]`).value;
        let catatan = document.getElementById(`catatan-input-${id}`).value;

        // Konfirmasi dulu
        if (!confirm("Yakin ingin menyimpan perubahan ini?")) {
            return;
        }

        // Kirim ke server
        fetch("../../UpdateStatus.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `id=${id}&status=${encodeURIComponent(status)}&catatan=${encodeURIComponent(catatan)}`
        })
            .then(res => res.text())
            .then(res => {
                console.log(res); // buat debugging

                if (res.startsWith("ERROR")) {
                    alert(" Gagal menyimpan: " + res);
                    return;
                }

                // Kalau sukses
                alert("Perubahan berhasil disimpan!\n" + "Email pemberitahuan telah dikirim ke peminjam.");

                // Update tampilan tabel
                document.getElementById(`catatan-text-${id}`).innerText = catatan;
                document.getElementById(`catatan-input-${id}`).style.display = "none";
                document.getElementById(`catatan-text-${id}`).style.display = "inline";
            })
            .catch(err => {
                alert("Terjadi kesalahan: " + err);
            });
    });
});
// SEARCH REALTIME TANPA RELOAD
document.getElementById("search-input").addEventListener("keyup", function () {

    let keyword = this.value.toLowerCase().trim();
    let rows = document.querySelectorAll("tbody tr");

    rows.forEach(row => {
        let rowText = row.innerText.toLowerCase();

        // Jika baris mengandung keyword → tampilkan
        if (rowText.includes(keyword)) {
            row.style.display = "";
        } else {
            row.style.display = "none";
        }
    });
});

// --- SORTING ---

const sortBtn = document.getElementById("sort-btn");
const sortMenu = document.getElementById("sort-menu");
const sortLabel = document.getElementById("sort-label");

// ambil semua baris tabel sekali di awal
const tbody = document.querySelector("table tbody");
const originalRows = Array.from(tbody.querySelectorAll("tr"));

// simpan index asli, buat nanti balik ke default
originalRows.forEach((row, idx) => {
    row.dataset.originalIndex = idx;
});

// buka / tutup dropdown
sortBtn.addEventListener("click", () => {
    sortMenu.classList.toggle("hidden");
});

// kalau klik di luar dropdown → tutup
document.addEventListener("click", (e) => {
    if (!sortMenu.contains(e.target) && !sortBtn.contains(e.target)) {
        sortMenu.classList.add("hidden");
    }
});

// fungsi bantu buat apply urutan baru ke tbody
function applyOrder(rows) {
    rows.forEach(tr => tbody.appendChild(tr));
}

// fungsi sortir
function sortRows(type) {
    let rows = Array.from(tbody.querySelectorAll("tr"));

    if (type === "default") {
        // urutkan kembali sesuai index asli
        rows.sort((a, b) => a.dataset.originalIndex - b.dataset.originalIndex);
        sortLabel.textContent = "Default";
    }

    if (type === "latest") {
        // tanggal pakai di kolom ke-7 (index 6)
        // kalau mau pakai tanggal pengajuan, ganti 6 -> 5
        rows.sort((a, b) => {
            const tA = new Date(a.children[6].innerText);
            const tB = new Date(b.children[6].innerText);
            return tB - tA; // terbaru dulu
        });
        sortLabel.textContent = "Terbaru";
    }

    if (type === "oldest") {
        rows.sort((a, b) => {
            const tA = new Date(a.children[6].innerText);
            const tB = new Date(b.children[6].innerText);
            return tA - tB; // terlama dulu
        });
        sortLabel.textContent = "Terlama";
    }

    if (type === "az") {
        // nama peminjam di kolom ke-3 (index 2)
        rows.sort((a, b) => {
            const nA = a.children[2].innerText.toLowerCase();
            const nB = b.children[2].innerText.toLowerCase();
            return nA.localeCompare(nB);
        });
        sortLabel.textContent = "Nama A–Z";
    }

    if (type === "za") {
        rows.sort((a, b) => {
            const nA = a.children[2].innerText.toLowerCase();
            const nB = b.children[2].innerText.toLowerCase();
            return nB.localeCompare(nA);
        });
        sortLabel.textContent = "Nama Z–A";
    }

    applyOrder(rows);
}

// klik opsi di dropdown
sortMenu.querySelectorAll("div[data-sort]").forEach(item => {
    item.addEventListener("click", () => {
        const type = item.dataset.sort;
        sortRows(type);
        sortMenu.classList.add("hidden");
    });
});



// SCRIPT DELETE TETAP, JANGAN DIUBAH
document.querySelector('.delete-selection').addEventListener('click', () => {
    const checked = document.querySelectorAll('.row-check:checked');

    if (checked.length === 0) {
        alert("Tidak ada data yang dipilih.");
        return;
    }

    if (!confirm("Yakin ingin menghapus data yang dipilih?")) {
        return;
    }

    let ids = [...checked].map(c => c.value);
    let formBody = ids.map(id => `ids[]=${encodeURIComponent(id)}`).join("&");

    fetch("hapuspeminjaman.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: formBody
    })
        .then(res => res.text())
        .then(msg => {
            alert(msg);
            location.reload();
        })
        .catch(err => alert("Terjadi error: " + err));
});