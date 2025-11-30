<!DOCTYPE html>
<html>

<?php
require __DIR__ . '../../../../Admin/CRUD/Koneksi.php';
require __DIR__ . '../../../../Assets/Css/Admin/PeminjamanLab.css';
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
            <button class="export"><i class="fa-solid fa-arrow-up-from-bracket"></i> Export</button>

            <button class="sort">
                <i class="fa-solid fa-arrow-down-wide-short"></i>
                Urutkan : <strong>Default</strong>
            </button>

            <button class="add" onclick="window.location='tambah.php'">
                <i class="fa-solid fa-plus"></i> Tambah
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
                    <td><input type="checkbox"></td>
                    <td><?= $row['id_peminjaman'] ?></td>
                    <td><?= $row['nama_peminjam'] ?></td>
                    <td><?= $row['email'] ?></td>
                    <td><?= $row['instansi'] ?></td>
                    <td><?= $row['tanggal_pengajuan'] ?></td>
                    <td><?= $row['tanggal_pakai'] ?></td>
                    <td><?= $row['keperluan'] ?></td>
                    <td><?= $row['status'] ?></td>
                    <td><?= $row['approved_by'] ?></td>
                    <td><?= $row['catatan_admin'] ?></td>

                    <td>
                        <span class="status 
                            <?= strtolower($row['status']) ?>">
                            <?= $row['status'] ?>
                        </span>
                    </td>

                    <td><?= $row['approved_by'] ?></td>
                    <td><?= $row['catatan_admin'] ?></td>

                    <td>
                        <a href="edit.php?id_peminjaman=<?= $row['id_peminjaman'] ?>" class="btn-edit">Edit</a>
                        <a href="hapus.php?id_peminjaman=<?= $row['id_peminjaman'] ?>" class="btn-delete"
                           onclick="return confirm('Yakin mau hapus?');">Hapus</a>
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

<script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
</body>
</html>
