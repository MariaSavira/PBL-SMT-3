<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require __DIR__ . '../../../Koneksi/KoneksiSasa.php';

    $res  = q('SELECT id_riset, nama_bidang_riset FROM bidangriset ORDER BY id_riset ASC');
    $rows = pg_fetch_all($res) ?: [];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Riset</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="images/x-icon"
        href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../../Assets/Css/Admin/Riset.css">
</head>
<body>
    <div id="sidebar"></div>
    <main class="content" id="content">

        <div class="content-header">
            <h1>Riset</h1>
            <div class="profile">
                <span>Maria Savira</span>
                <i class="fa-solid fa-circle-user"></i>
            </div>
        </div>

        <div class="top-controls">
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Cari">
                <span class="result-count"><?= count($rows) ?> hasil</span>
            </div>

            <div class="filter-info">
                <span class="filter-chip">
                    <i class="fa-solid fa-sliders"></i>
                    Riset
                    <i class="fa-solid fa-xmark remove-chip"></i>
                </span>
                <a href="#" class="clear-filter">Hapus Filter</a>
            </div>

            <div class="right-actions">
                <button class="export"><i class="fa-solid fa-arrow-up-from-bracket"></i> Export</button>

                <button class="sort">
                    <i class="fa-solid fa-arrow-down-wide-short"></i>
                    Urutkan : <strong>Default</strong>
                </button>

                <button class="add" id="btnTambah">
                    <i class="fa-solid fa-plus"></i> Tambah
                </button>
            </div>
        </div>

        <div class="page-nav">
            <span id="page-text">1 of 1</span>
            <button id="prev-page" disabled><i class="fa-solid fa-chevron-left"></i></button>
            <button id="next-page" disabled><i class="fa-solid fa-chevron-right"></i></button>
        </div>

        <div class="table-container">
            <form id="formRiset" method="post" action="DeleteRiset.php">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="check-all"></th>
                            <th>id_riset</th>
                            <th>nama riset</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!$rows): ?>
                        <tr>
                            <td colspan="4">Belum ada data.</td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $row): ?>
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="row-check"
                                        name="selected_ids[]"
                                        value="<?= htmlspecialchars($row['id_riset']) ?>">
                                </td>
                                <td><?= htmlspecialchars($row['id_riset']) ?></td>
                                <td><?= htmlspecialchars($row['nama_bidang_riset']) ?></td>
                                <td><i class="fa-solid fa-ellipsis"></i></td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
        </div>

        <!-- TOMBOL HAPUS (SEBAGAI DIV, JS DI FILE Riset.js) -->
        <div class="delete-selection" id="btnDelete">
            <i class="fa-solid fa-trash"></i>
            Hapus data yang dipilih
        </div>

    </main>
    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script src="../../../Assets/Javascript/Admin/Riset.js"></script>

</body>
</html>
