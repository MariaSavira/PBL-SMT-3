<!DOCTYPE html>
<html>
<?php
require __DIR__ . '../../koneksi.php';

$res = q('SELECT * from mv_anggota_keahlian ORDER BY id_anggota ASC');
$rows = pg_fetch_all($res) ?: [];
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Analytics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../../Assets/Css/Admin/AnggotaLab.css">
</head>

<body>
    <!-- SIDEBAR -->
    <div id="sidebar"></div>

    <!-- <aside class="sidebar">
        <div class="sidebar-logo">
            <img src="logo.png" alt="logo">
        </div>
        
        <ul class="sidebar-menu">
            <li class="active"><i class="fa-solid fa-house"></i></li>
            <li><i class="fa-solid fa-users"></i></li>
            <li><i class="fa-solid fa-file-lines"></i></li>
            <li><i class="fa-solid fa-book"></i></li>
            <li><i class="fa-solid fa-chalkboard-user"></i></li>
            <li><i class="fa-solid fa-user"></i></li>
        </ul>
    </aside> -->
    <main class="content collapsed" id="content">

        <div class="content-header">
            <h1>Anggota Laboratorium</h1>
            <div class="profile">
                <span>Maria Savira</span>
                <i class="fa-solid fa-circle-user"></i>
            </div>
        </div>

        <div class="top-controls">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Cari">
            </div>

            <div class="filter-info">
                <span><i class="fa-solid fa-sliders"></i> Peneliti</span>
                <a href="#">Hapus Filter</a>
            </div>

            <div class="right-actions">
                <button class="export"><i class="fa-solid fa-arrow-up-from-bracket"></i> Export</button>

                <button class="sort">
                    <i class="fa-solid fa-arrow-down-wide-short"></i>
                    Urutkan : <strong>Default</strong>
                </button>

                <button class="add"><i class="fa-solid fa-plus"></i> Tambah</button>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>id</th>
                        <th>nama</th>
                        <th>keahlian</th>
                        <th>jabatan</th>
                        <th>foto</th>
                        <th>status</th>
                        <th></th>
                    </tr>
                </thead>

                <tbody>
                    <?php if (!$rows): ?>
                        <tr>
                            <td colspan="6">Belum ada data.</td>
                        </tr>
                    <?php else: ?>
                        <?php $i = 1;
                        foreach ($rows as $row): ?>
                            <tr>
                                <td><input type="checkbox" style="width: 15px; height: 15px"></td>
                                <td class="text"><?= htmlspecialchars($row["id_anggota"]) ?></td>
                                <td class="text"><?= htmlspecialchars($row["nama"]) ?></td>
                                <td>
                                    <?php
                                    $keahlianList = $row["keahlian"]
                                        ? explode(',', trim($row["keahlian"], '{}'))
                                        : [];

                                    $keahlianList = array_map(fn($k) => trim($k, '"'), $keahlianList);
                                    ?>

                                    <?php if (empty($keahlianList)): ?>
                                        <span class="tag orange">-</span>
                                    <?php else: ?>
                                        <?php foreach ($keahlianList as $tag): ?>
                                            <span class="tag orange"><?= htmlspecialchars($tag) ?></span>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </td>
                                <td><span class="tag blue"><?= htmlspecialchars($row["jabatan"]) ?></span></td>
                                <td>
                                    <?php
                                        $folder = '../../../Assets/Image/AnggotaLab/';
                                        $foto = $row['foto'];

                                        $src = (!empty($foto) && file_exists($folder . $foto))
                                            ? $folder . $foto
                                            : $folder . 'No-Picture.jpg';
                                    ?>
                                    <img src="<?= $src ?>" alt="Foto User" class="user-foto">
                                </td>
                                <td><span class="status aktif"><?= ($row["status"] === 't' || $row["status"] === true) ? 'Aktif' : 'Nonaktif'; ?></span></td>
                                <td><i class="fa-solid fa-ellipsis-vertical"></i></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="delete-selection">
            <i class="fa-solid fa-trash"></i> Hapus data yang dipilih
        </div>

    </main>
    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
</body>

</html>