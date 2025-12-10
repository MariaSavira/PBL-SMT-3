<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require __DIR__ . '/../../Koneksi/KoneksiPDO.php';

    $limit = 6;
    $page  = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $offset = ($page - 1) * $limit;

    $filterField = $_GET['filter_field'] ?? '';
    $filterValue = trim($_GET['filter_value'] ?? '');

    $conditions = [];
    $params     = [];  

    $currentFilterLabel = 'Filter';

    if ($filterField !== '' && $filterValue !== '') {
        $ff = strtolower($filterField);

        if ($ff === 'status') {
            $statusDb = strtolower($filterValue);

            $conditions[]           = "status = :status_filter";
            $params[':status_filter'] = $statusDb;

            $currentFilterLabel = 'Status: ' . $filterValue;
        }
    }

    $whereSql = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

    $sort = $_GET['sort'] ?? 'default';

    $allowedSort = ['default', 'latest', 'oldest', 'az', 'za'];
    if (!in_array($sort, $allowedSort, true)) {
        $sort = 'default';
    }

    switch ($sort) {
        case 'latest':
            $orderSql  = "ORDER BY tanggal_pengajuan DESC, id_peminjaman DESC";
            $sortLabel = "Terbaru";
            break;

        case 'oldest':
            $orderSql  = "ORDER BY tanggal_pengajuan ASC, id_peminjaman ASC";
            $sortLabel = "Terlama";
            break;

        case 'az':
            $orderSql  = "ORDER BY LOWER(nama_peminjam) ASC, id_peminjaman ASC";
            $sortLabel = "Nama A–Z";
            break;

        case 'za':
            $orderSql  = "ORDER BY LOWER(nama_peminjam) DESC, id_peminjaman DESC";
            $sortLabel = "Nama Z–A";
            break;

        case 'default':
        default:
            $orderSql  = "ORDER BY id_peminjaman DESC";
            $sortLabel = "Default";
            $sort      = "default";
            break;
    }

    $search = trim($_GET['q'] ?? '');

    $sqlCount = "SELECT COUNT(*) AS total FROM peminjaman_lab $whereSql";
    $stmtCount = $db->prepare($sqlCount);
    foreach ($params as $key => $val) {
        $stmtCount->bindValue($key, $val);
    }
    $stmtCount->execute();
    $totalData = (int)($stmtCount->fetch()['total'] ?? 0);
    $totalPages = max(1, (int)ceil($totalData / $limit));

    $sql = "
        SELECT *
        FROM peminjaman_lab
        $whereSql
        $orderSql
        LIMIT :limit OFFSET :offset
    ";
    $stmt = $db->prepare($sql);

    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $result = $stmt->fetchAll();

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
    <title>Index Peminjaman Laboratorium</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet"
    >

    <link rel="icon" type="images/x-icon"
          href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../../Assets/Css/Admin/PeminjamanLab.css">
</head>

<body>
    <div id="sidebar"></div>

    <main class="content" id="content">
        <div id="header"></div>

        <div class="top-controls">
            
            <div class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    id="search-input"
                    type="text"
                    placeholder="Cari"
                    value="<?= htmlspecialchars($search) ?>"
                >
            </div>

            <div class="filter-area">
                <div class="filter-dropdown">
                    <button type="button" class="filter-toggle">
                        <i class="fa-solid fa-sliders"></i>
                        <span><?= htmlspecialchars($currentFilterLabel) ?></span>
                        <i class="fa-solid fa-chevron-down caret"></i>
                    </button>

                    <div class="filter-menu">
                        <div class="filter-section">
                            <div class="filter-section-title">Status</div>

                            <a href="<?= htmlspecialchars(build_query([
                                            'filter_field' => 'status',
                                            'filter_value' => 'Disetujui',
                                            'page'        => 1
                                        ])) ?>" class="filter-item">Disetujui</a>

                            <a href="<?= htmlspecialchars(build_query([
                                            'filter_field' => 'status',
                                            'filter_value' => 'Pending',
                                            'page'        => 1
                                        ])) ?>" class="filter-item">Pending</a>

                            <a href="<?= htmlspecialchars(build_query([
                                            'filter_field' => 'status',
                                            'filter_value' => 'Ditolak',
                                            'page'        => 1
                                        ])) ?>" class="filter-item">Ditolak</a>

                        </div>
                    </div>
                </div>

                <a href="<?= htmlspecialchars(build_query([
                                'filter_field' => null,
                                'filter_value' => null,
                                'page'        => 1
                            ])) ?>" class="clear-filter">Hapus Filter</a>
            </div>

            
            <div class="right-actions">
                <a href="export.php" style="text-decoration:none;">
                    <button type="button" class="export">
                        <i class="fa-solid fa-arrow-up-from-bracket"></i>
                        Export
                    </button>
                </a>

                <div class="sort-wrapper">
                    <button id="sort-btn" class="sort">
                        <i class="fa-solid fa-arrow-down-wide-short"></i>
                        Urutkan : <strong id="sort-label"><?= htmlspecialchars($sortLabel) ?></strong>
                    </button>

                    
                    <div id="sort-menu" class="sort-menu hidden">
                        <div data-sort="default" class="<?= $sort === 'default' ? 'active' : '' ?>">Default</div>
                        <div data-sort="latest"  class="<?= $sort === 'latest'  ? 'active' : '' ?>">Terbaru</div>
                        <div data-sort="oldest"  class="<?= $sort === 'oldest'  ? 'active' : '' ?>">Terlama</div>
                        <div data-sort="az"      class="<?= $sort === 'az'      ? 'active' : '' ?>">Nama A–Z</div>
                        <div data-sort="za"      class="<?= $sort === 'za'      ? 'active' : '' ?>">Nama Z–A</div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Nama Peminjam</th>
                        <th>Email</th>
                        <th>Instansi</th>
                        <th>Tanggal Pengajuan</th>
                        <th>Tanggal Pakai</th>
                        <th>Keperluan</th>
                        <th>Status</th>
                        <th>Approved</th>
                        <th>Catatan</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                <?php if (!$result): ?>
                    <tr>
                        <td colspan="12">Belum ada data peminjaman.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($result as $row): ?>
                        <tr data-row-status="<?= htmlspecialchars($row['status']) ?>">
                            <td>
                                <input
                                    type="checkbox"
                                    class="row-check"
                                    value="<?= $row['id_peminjaman'] ?>">
                            </td>

                            <td><?= htmlspecialchars($row['id_peminjaman']) ?></td>
                            <td><?= htmlspecialchars($row['nama_peminjam']) ?></td>

                            <td class="email-column">
                                <?= htmlspecialchars($row['email']) ?>
                            </td>

                            <td><?= htmlspecialchars($row['instansi']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_pengajuan']) ?></td>
                            <td><?= htmlspecialchars($row['tanggal_pakai']) ?></td>
                            <td><?= htmlspecialchars($row['keperluan']) ?></td>

                            <td>
                                <select class="status-dropdown"
                                        data-id="<?= $row['id_peminjaman']; ?>">
                                    <option value="pending"
                                        <?= $row['status'] == "pending" ? "selected" : "" ?>>
                                        Pending
                                    </option>
                                    <option value="disetujui"
                                        <?= $row['status'] == "disetujui" ? "selected" : "" ?>>
                                        Disetujui
                                    </option>
                                    <option value="ditolak"
                                        <?= $row['status'] == "ditolak" ? "selected" : "" ?>>
                                        Ditolak
                                    </option>
                                </select>
                            </td>

                            <td class="approved-by">
                                <?= !empty($row['approved_by'])
                                    ? htmlspecialchars($row['approved_by'])
                                    : '-' ?>
                            </td>

                            <td>
                                <div style="display:flex; align-items:center; gap:8px;">
                                    <span id="catatan-text-<?= $row['id_peminjaman'] ?>">
                                        <?= $row['catatan_admin']
                                            ? htmlspecialchars($row['catatan_admin'])
                                            : '' ?>
                                    </span>

                                    <i class="fa-solid fa-pen-to-square edit-catatan"
                                       data-id="<?= $row['id_peminjaman'] ?>"
                                       style="cursor:pointer;"></i>

                                    <input type="hidden"
                                           id="catatan-input-<?= $row['id_peminjaman'] ?>"
                                           value="<?= $row['catatan_admin']
                                               ? htmlspecialchars($row['catatan_admin'])
                                               : '' ?>">
                                </div>
                            </td>

                            <td>
                                <button class="btn-save"
                                        data-id="<?= $row['id_peminjaman'] ?>">
                                    Simpan
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div id="catatan-modal" class="modal-overlay">
            <div class="modal-box">
                <h3 class="modal-title">Edit Catatan</h3>

                <textarea id="catatan-modal-text"
                          class="modal-textarea"></textarea>

                <div class="modal-actions">
                    <button id="catatan-modal-cancel" class="btn-cancel">
                        Batal
                    </button>
                    <button id="catatan-modal-save" class="btn-save">
                        Simpan Catatan
                    </button>
                </div>
            </div>
        </div>

        
        <div class="table-footer">
            <div class="delete-selection" style="cursor: pointer">
                <i class="fa-solid fa-trash"></i>
                Hapus data yang dipilih
            </div>

            <div class="pagination">
                <?php
                // PREV
                if ($page > 1) {
                    echo "<a href='" . htmlspecialchars(build_query(['page' => $page - 1])) . "' class='page-link prev'>&laquo; Sebelumnya</a>";
                }

                // Page 1
                if ($page > 3) {
                    echo "<a href='" . htmlspecialchars(build_query(['page' => 1])) . "' class='page-link'>1</a>";
                    echo "<span class='dots'>...</span>";
                }

                // current-1, current, current+1
                for ($i = max(1, $page - 1); $i <= min($totalPages, $page + 1); $i++) {
                    $activeClass = ($i == $page) ? "active" : "";
                    echo "<a href='" . htmlspecialchars(build_query(['page' => $i])) . "' class='page-link {$activeClass}'>{$i}</a>";
                }

                // last page
                if ($page < $totalPages - 2) {
                    echo "<span class='dots'>...</span>";
                    echo "<a href='" . htmlspecialchars(build_query(['page' => $totalPages])) . "' class='page-link'>{$totalPages}</a>";
                }

                // NEXT
                if ($page < $totalPages) {
                    echo "<a href='" . htmlspecialchars(build_query(['page' => $page + 1])) . "' class='page-link next'>Berikutnya &raquo;</a>";
                }
                ?>
            </div>
        </div>
    </main>

    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script src="../../../Assets/Javascript/Admin/Header.js"></script>
    <script src="../../../Assets/Javascript/Admin/PeminjamanLab.js"></script>
</body>
</html>
