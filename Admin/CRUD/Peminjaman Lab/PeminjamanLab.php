<?php
require __DIR__ . '/../koneksi.php';

$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Hitung total data
$totalQuery = $db->query("SELECT COUNT(*) AS total FROM peminjaman_lab");
$totalData = $totalQuery->fetch()['total'];
$totalPages = ceil($totalData / $limit);

// Ambil data
$stmt = $db->prepare("
    SELECT *
    FROM peminjaman_lab
    ORDER BY id_peminjaman DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

$result = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman Laboratorium</title>
    <link rel="stylesheet" href="../../../Assets/Css/Admin/PeminjamanLab.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

    <div id="sidebar"></div>

    <main class="content collapsed" id="content">

        <!-- HEADER -->
        <div class="content-header">
            <h1>Peminjaman Laboratorium</h1>
            <div class="profile">
                <span>Maria Savira</span>
                <i class="fa-solid fa-circle-user"></i>
            </div>
        </div>

        <!-- TOP CONTROLS -->
        <div class="top-controls">

            <!-- SEARCH -->
            <div class="search-box">
    <i class="fa-solid fa-magnifying-glass"></i>
    <input id="search-input" type="text" placeholder="Cari">
</div>


            <!-- FILTER -->
            <div class="filter-info">
                <span class="filter-chip">
                    <i class="fa-solid fa-sliders"></i>
                    Peminjaman Laboratorium
                    <i class="fa-solid fa-xmark remove-chip"></i>
                </span>
                <a href="#" class="clear-filter">Hapus Filter</a>
            </div>

            <!-- ACTION BUTTONS -->
<div class="right-actions">
    <a href="export.php" style="text-decoration:none;">
        <button type="button" class="btn-export">
            <i class="fa-solid fa-arrow-up-from-bracket"></i> Export
        </button>
    </a>

                 <div class="sort-wrapper">
        <button id="sort-btn" class="sort">
            <i class="fa-solid fa-arrow-down-wide-short"></i>
            Urutkan : <strong id="sort-label">Default</strong>
        </button>

        <!-- Dropdown muncul saat tombol diklik -->
        <div id="sort-menu" class="sort-menu hidden">
            <div data-sort="default">Default</div>
            <div data-sort="latest">Terbaru</div>
            <div data-sort="oldest">Terlama</div>
            <div data-sort="az">Nama A–Z</div>
            <div data-sort="za">Nama Z–A</div>
        </div>
    </div>
</div>

            <!-- PAGINATION  -->
    <div class="pagination-top">
        <span class="page-info"><?= $page ?> of <?= $totalPages ?></span>

        <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>" class="page-nav">
                <i class="fa-solid fa-chevron-left"></i>
            </a>
        <?php else: ?>
            <span class="page-nav disabled"><i class="fa-solid fa-chevron-left"></i></span>
        <?php endif; ?>

        <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>" class="page-nav">
                <i class="fa-solid fa-chevron-right"></i>
            </a>
        <?php else: ?>
            <span class="page-nav disabled"><i class="fa-solid fa-chevron-right"></i></span>
        <?php endif; ?>
    </div>

</div>



        <!-- TABLE -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>id</th>
                        <th>nama peminjam</th>
                        <th>email</th>
                        <th>instansi</th>
                        <th>tanggal pengajuan</th>
                        <th>tanggal pakai</th>
                        <th>keperluan</th>
                        <th>status</th>
                        <th>approved</th>
                        <th>catatan</th>
                        <th>aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($result as $row): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="row-check" value="<?= $row['id_peminjaman'] ?>">
                        </td>
                        <td><?= $row['id_peminjaman'] ?></td>
                        <td><?= $row['nama_peminjam'] ?></td>
                        <td class="email-column"><?= $row['email'] ?></td>
                        <td><?= $row['instansi'] ?></td>
                        <td><?= $row['tanggal_pengajuan'] ?></td>
                        <td><?= $row['tanggal_pakai'] ?></td>
                        <td><?= $row['keperluan'] ?></td>
                        <td>
                            <select class="status-dropdown" data-id="<?= $row['id_peminjaman']; ?>">
                                <option value="pending" <?= $row['status']=="pending" ? "selected" : "" ?>>Pending
                                </option>
                                <option value="disetujui" <?= $row['status']=="disetujui" ? "selected" : "" ?>>Disetujui
                                </option>
                                <option value="ditolak" <?= $row['status']=="ditolak" ? "selected" : "" ?>>Ditolak
                                </option>
                            </select>
                        </td>
                        <td class="approved-by">
                            <?= !empty($row['approved_by']) ? htmlspecialchars($row['approved_by']) : '-' ?>
                        </td>


                        <td>
                            <div style="display:flex; align-items:center; gap:8px;">

                                <!-- Teks catatan -->
                                <span id="catatan-text-<?= $row['id_peminjaman'] ?>">
                                    <?= $row['catatan_admin'] ? htmlspecialchars($row['catatan_admin']) : '' ?>
                                </span>

                                <!-- ICON PENSIL -->
                                <i class="fa-solid fa-pen-to-square edit-catatan" data-id="<?= $row['id_peminjaman'] ?>"
                                    style="cursor:pointer;"></i>

                                <!-- INPUT HIDDEN (tempat nyimpen catatan setelah popup) -->
                                <input type="hidden" id="catatan-input-<?= $row['id_peminjaman'] ?>"
                                    value="<?= $row['catatan_admin'] ? htmlspecialchars($row['catatan_admin']) : '' ?>">
                            </div>
                        </td>



                        <!-- AKSI (Ada SIMPAN di sini) -->
                        <td>
                            <button class="btn-save" data-id="<?= $row['id_peminjaman'] ?>">Simpan</button>
                        </td>

                    </tr>

                    <?php endforeach; ?>

                </tbody>
            </table>
        </div>
        
        <!-- POPUP / MODAL UNTUK EDIT CATATAN -->
<div id="catatan-modal" class="modal-overlay">
  <div class="modal-box">
    <h3 class="modal-title">Edit Catatan</h3>

    <textarea id="catatan-modal-text" class="modal-textarea"></textarea>

    <div class="modal-actions">
      <button id="catatan-modal-cancel" class="btn-cancel">Batal</button>
      <button id="catatan-modal-save" class="btn-save">Simpan Catatan</button>
    </div>
  </div>
</div>

        <!-- DELETE SELECTED -->
        <div class="delete-selection">
            <i class="fa-solid fa-trash"></i>
            Hapus data yang dipilih
        </div>

    </main>
    <script>
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
    modal.classList.remove('open');   // hapus class .open supaya modal tersembunyi
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

const sortBtn   = document.getElementById("sort-btn");
const sortMenu  = document.getElementById("sort-menu");
const sortLabel = document.getElementById("sort-label");

// ambil semua baris tabel sekali di awal
const tbody         = document.querySelector("table tbody");
const originalRows  = Array.from(tbody.querySelectorAll("tr"));

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
    </script>

    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
</body>

</html>