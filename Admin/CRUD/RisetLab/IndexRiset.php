<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require __DIR__ . '../../../Koneksi/KoneksiSasa.php';

    $limit = 6; // harus sama dengan rowsPerPage di JS
    $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;

    // AMBIL SEMUA DATA (tanpa LIMIT/OFFSET, paging dihandle JS)
    $res  = q('SELECT id_riset, nama_bidang_riset FROM bidangriset ORDER BY id_riset ASC');
    $rows = pg_fetch_all($res) ?: [];

    // HITUNG TOTAL DATA & TOTAL HALAMAN
    $totalData  = count($rows);
    $totalPages = max(1, (int)ceil($totalData / $limit));

    function build_query(array $extra = []): string
    {
        $base = $_GET;
        foreach ($extra as $k => $v) {
            if ($v === null) {
                unset($base[$k]);
            } else {
                $base[$k] = $v;
            }
        }
        return '?' . http_build_query($base);
    }
?>
<!DOCTYPE html>
<html lang="id">

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

        <div id="header"></div>

        <div class="top-controls">

            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" placeholder="Cari">
                <!-- <span class="result-count"><?= $totalData ?> hasil</span> -->
            </div>

            <div class="right-actions">
                <button type="button" class="export">
                    <i class="fa-solid fa-arrow-up-from-bracket"></i> Export
                </button>

                <button type="button" class="sort">
                    <i class="fa-solid fa-arrow-down-wide-short"></i>
                    Urutkan : <strong>Default</strong>
                </button>

                <button type="button" class="add" id="btnTambah">
                    <i class="fa-solid fa-plus"></i> Tambah
                </button>
            </div>
        </div>

        <div class="table-container">
            <!-- FORM BESAR UNTUK BULK DELETE -->
            <form id="formRiset" method="post" action="DeleteRiset.php">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="check-all"></th>
                            <th>ID_RISET</th>
                            <th>NAMA RISET</th>
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

                                    <td class="action-cell">
                                        <button
                                            type="button"
                                            class="action-toggle"
                                            aria-haspopup="true"
                                            aria-expanded="false">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>

                                        <div class="action-menu">
                                            <a href="EditRiset.php?id=<?= $row['id_riset'] ?>" class="action-item">
                                                <i class="fa-solid fa-pen"></i>
                                                <span>Edit</span>
                                            </a>

                                            <!-- HAPUS SATUAN: pakai GET ?id=... -->
                                            <a href="DeleteRiset.php?id=<?= $row['id_riset'] ?>"
                                               class="action-item action-delete"
                                               onclick="return confirm('Yakin ingin menghapus riset ini?');">
                                                <i class="fa-solid fa-trash-can"></i>
                                                <span>Hapus</span>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </form>
        </div>

        <div class="table-footer">
            <!-- BULK DELETE: submit formRiset -->
            <button
                type="submit"
                form="formRiset"
                class="delete-selection" style="background: transparent;"
                id="btnDelete">
                <i class="fa-solid fa-trash"></i>
                Hapus data yang dipilih
            </button>

            <div class="pagination">
                <?php
                $prevPage = max(1, $page - 1);
                $nextPage = min($totalPages, $page + 1);

                echo "<a href='" . htmlspecialchars(build_query(['page' => $prevPage])) . "' class='page-link prev'>&laquo; Sebelumnya</a>";

                // Page 1 + dots
                if ($page > 3) {
                    echo "<a href='" . htmlspecialchars(build_query(['page' => 1])) . "' class='page-link'>1</a>";
                    echo "<span class='dots'>...</span>";
                }

                // current-1, current, current+1
                for ($i = max(1, $page - 1); $i <= min($totalPages, $page + 1); $i++) {
                    $activeClass = ($i == $page) ? "active" : "";
                    echo "<a href='" . htmlspecialchars(build_query(['page' => $i])) . "' class='page-link {$activeClass}'>{$i}</a>";
                }

                // last page + dots
                if ($page < $totalPages - 2) {
                    echo "<span class='dots'>...</span>";
                    echo "<a href='" . htmlspecialchars(build_query(['page' => $totalPages])) . "' class='page-link'>{$totalPages}</a>";
                }

                echo "<a href='" . htmlspecialchars(build_query(['page' => $nextPage])) . "' class='page-link next'>Berikutnya &raquo;</a>";
                ?>
            </div>
        </div>
    </main>

    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script src="../../../Assets/Javascript/Admin/Header.js"></script>
    <script src="../../../Assets/Javascript/Admin/Riset.js"></script>
</body>

</html>
