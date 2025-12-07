<!DOCTYPE html>
<html>

<?php
require __DIR__ . '/../Koneksi.php';
$conn = pg_connect("host=localhost port=5432 dbname=lab_ba user=postgres password=29082006");

if (!$conn) {
    die("<h2 style='color:red;'>Koneksi gagal: " . pg_last_error() . "</h2>");
}
?>

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
                <input type="text" placeholder="Cari">
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
                    <button type="button">Export</button>
                </a>


                <button class="sort">
                    <i class="fa-solid fa-arrow-down-wide-short"></i>
                    Urutkan : <strong>Default</strong>
                </button>
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

                    <?php
                $query = "SELECT * FROM peminjaman_lab ORDER BY id_peminjaman DESC";
                $result = pg_query($conn, $query);

                while ($row = pg_fetch_assoc($result)):
                ?>
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

                    <?php endwhile; ?>

                </tbody>
            </table>
        </div>
        <!-- POPUP / MODAL UNTUK EDIT CATATAN -->
        <div id="catatan-modal" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.4); 
            align-items:center; justify-content:center; z-index:9999;">

            <div style="background:white; padding:20px; border-radius:10px; width:320px;">

                <h3 style="margin-top:0;">Edit Catatan</h3>

                <textarea id="catatan-modal-text" style="width:100%; height:100px; resize:none;"></textarea>

                <div style="margin-top:15px; display:flex; justify-content:flex-end; gap:10px;">
                    <button id="catatan-modal-cancel">Batal</button>
                    <button id="catatan-modal-save">Simpan Catatan</button>
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

    //  STATUS berubah → beri alert
    document.querySelectorAll('.status-dropdown').forEach(drop => {
        drop.addEventListener('change', () => {
            alert("Status berubah! Klik SIMPAN untuk menyimpan.");
            dirtyRows.add(drop.dataset.id);
        });
    });

    //  Klik ikon pensil → munculkan input catatan
    // --- POPUP VARIABLES ---
    let editingId = null;
    const modal = document.getElementById("catatan-modal");
    const modalTextarea = document.getElementById("catatan-modal-text");


    // --- KLIK IKON PENSIL → MUNCUL POPUP ---
    document.querySelectorAll('.edit-catatan').forEach(icon => {
        icon.addEventListener('click', () => {
            editingId = icon.dataset.id;

            const currentText = document.getElementById(`catatan-text-${editingId}`).innerText.trim();

            modalTextarea.value = currentText;
            modal.style.display = "flex";
        });
    });


    // --- TOMBOL BATAL ---
    document.getElementById("catatan-modal-cancel").addEventListener("click", () => {
        modal.style.display = "none";
        editingId = null;
    });


    // --- TOMBOL SIMPAN CATATAN DI POPUP ---
    document.getElementById("catatan-modal-save").addEventListener("click", () => {
        if (!editingId) return;

        const newText = modalTextarea.value;

        // Update di tampilan tabel
        document.getElementById(`catatan-text-${editingId}`).innerText = newText;

        // Simpan ke hidden input (ini yang akan dikirim ke database pas klik SIMPAN baris)
        document.getElementById(`catatan-input-${editingId}`).value = newText;

        modal.style.display = "none";

        alert("Catatan berhasil diperbarui.");
    });


    //  TOMBOL SIMPAN
    document.querySelectorAll('.btn-save').forEach(btn => {
        btn.addEventListener('click', () => {

            let id = btn.dataset.id;
            let status = document.querySelector(`.status-dropdown[data-id="${id}"]`).value;
            let catatan = document.getElementById(`catatan-input-${id}`).value;

            // Ambil data baris untuk email
            let row = btn.closest("tr");
            let nama = row.children[2].innerText;
            let email = row.children[3].innerText;
            let instansi = row.children[4].innerText;
            let tglPengajuan = row.children[5].innerText;
            let tglPakai = row.children[6].innerText;
            let keperluan = row.children[7].innerText;

            if (!confirm("Yakin ingin menyimpan perubahan dan menyiapkan email untuk peminjam ini?")) {
                return;
            }

            // ⬆ UPDATE DATABASE
            fetch("../../UpdateStatus.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: `id=${id}&status=${encodeURIComponent(status)}&catatan=${encodeURIComponent(catatan)}`
                })
                .then(res => res.text())
                .then(res => {
                    if (res.trim() !== "OK") {
                        alert("Gagal menyimpan: " + res);
                        return;
                    }

                    alert("Perubahan berhasil disimpan!");

                    // Update catatan di UI
                    document.getElementById(`catatan-text-${id}`).innerText = catatan;
                    document.getElementById(`catatan-input-${id}`).style.display = "none";
                    document.getElementById(`catatan-text-${id}`).style.display = "inline";

                    // ⬆ SIAPKAN EMAIL
                    let subject = `Pengajuan Peminjaman Lab - ${nama}`;

                    let body = `
                        ID Peminjaman  : ${id}
                        Nama           : ${nama}
                        Email          : ${email}
                        Instansi       : ${instansi}
                        Tgl Pengajuan  : ${tglPengajuan}
                        Tgl Pakai      : ${tglPakai}
                        Keperluan      : ${keperluan}

                        Catatan Admin:
                        ${catatan}
                                    `;

                    window.location.href =
                        `mailto:${email}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;
                });
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