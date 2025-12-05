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
                        <td><?= $row['email'] ?></td>
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
                        <td><?= $row['approved_by'] ?></td>
                        <td><?= $row['catatan_admin'] ?></td>

                        <!-- AKSI (Ada SIMPAN di sini) -->
                        <td>
                            <button class="btn-save" data-id="<?= $row['id_peminjaman'] ?>">Simpan</button>
                        </td>

                    </tr>

                    <?php endwhile; ?>

                </tbody>
            </table>
        </div>

        <!-- DELETE SELECTED -->
        <div class="delete-selection">
            <i class="fa-solid fa-trash"></i>
            Hapus data yang dipilih
        </div>

    </main>
    <script>
    document.querySelectorAll('.btn-save').forEach(btn => {
        btn.addEventListener('click', () => {

            let id = btn.dataset.id;
            let status = document.querySelector(`.status-dropdown[data-id="${id}"]`).value;

            fetch("../../UpdateStatus.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: "id=" + id + "&status=" + status
                })
                .then(res => res.text())
                .then(res => {
                    alert("Status berhasil diperbarui!");
                });
        });
    });
document.querySelector('.delete-selection').addEventListener('click', () => {

    const checked = document.querySelectorAll('.row-check:checked');

    if (checked.length === 0) {
        alert("Tidak ada data yang dipilih.");
        return;
    }

    if (!confirm("Yakin ingin menghapus data yang dipilih?")) {
        return;
    }

    // ambil semua ID
    let ids = [...checked].map(c => c.value);

    // format body biar POST sesuai: ids[]=1&ids[]=2&ids[]=3
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