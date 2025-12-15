<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require_once 'config.php';

    $user_name = $_SESSION['user_name'] ?? 'Maria Savira';

    // ========================
    // Ambil parameter URL
    // ========================
    $search       = trim($_GET['search'] ?? '');
    $filterStatus = trim($_GET['status'] ?? '');
    $sort         = $_GET['sort'] ?? 'default';

    // ========================
    // Pagination
    // ========================
    $perPage = 10;
    $page    = max(1, (int)($_GET['page'] ?? 1));
    $offset  = ($page - 1) * $perPage;

    // ========================
    // Helper: build_query (karena config.php kamu gak punya)
    // ========================
    function build_query(array $overrides = []): string {
        $current = $_GET ?? [];
        foreach ($overrides as $k => $v) {
            if ($v === null || $v === '') {
                unset($current[$k]);
            } else {
                $current[$k] = $v;
            }
        }
        return '?' . http_build_query($current);
    }

    function activeClass($cond) {
        return $cond ? 'active' : '';
    }

    // ========================
    // Dropdown status (ambil dari DB) - PDO
    // ========================
    $statusList = [];
    $stmtStatus = $pdo->query("SELECT DISTINCT status FROM berita ORDER BY status ASC");
    $statusList = $stmtStatus->fetchAll(PDO::FETCH_COLUMN);

    // ========================
    // Build WHERE + params (pakai placeholder ? konsisten)
    // ========================
    $where  = [];
    $params = [];

    $sqlBase = "
        FROM berita b
        LEFT JOIN anggotalab a ON b.uploaded_by = a.id_anggota
    ";

    if ($search !== '') {
        $where[]  = "(b.judul ILIKE ? OR b.isi ILIKE ?)";
        $params[] = "%{$search}%";
        $params[] = "%{$search}%";
    }

    if ($filterStatus !== '') {
        $where[]  = "b.status = ?";
        $params[] = $filterStatus;
    }

    $whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

    // ========================
    // Sort
    // ========================
    switch ($sort) {
        case "latest":
            $orderBy   = "ORDER BY b.tanggal DESC";
            $sortLabel = "Terbaru";
            break;
        case "oldest":
            $orderBy   = "ORDER BY b.tanggal ASC";
            $sortLabel = "Terlama";
            break;
        case "az":
            $orderBy   = "ORDER BY LOWER(b.judul) ASC";
            $sortLabel = "Judul A–Z";
            break;
        case "za":
            $orderBy   = "ORDER BY LOWER(b.judul) DESC";
            $sortLabel = "Judul Z–A";
            break;
        default:
            $orderBy   = "ORDER BY b.id_berita DESC";
            $sortLabel = "Default";
            break;
    }

    // ========================
    // Hitung total data (PDO)
    // ========================
    $sqlCount = "SELECT COUNT(*) AS total {$sqlBase} {$whereSql}";
    $stmtCount = $pdo->prepare($sqlCount);
    $stmtCount->execute($params);
    $totalData = (int)($stmtCount->fetch()['total'] ?? 0);
    $totalPages = max(1, (int)ceil($totalData / $perPage));

    // ========================
    // Ambil data per halaman (PDO)
    // ========================
    $sqlData = "
        SELECT b.*, a.nama AS nama_uploader
        {$sqlBase}
        {$whereSql}
        {$orderBy}
        LIMIT {$perPage} OFFSET {$offset}
    ";
    $stmt = $pdo->prepare($sqlData);
    $stmt->execute($params);
    $berita_list = $stmt->fetchAll();

    $total_hasil = $totalData;
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Berita</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="icon" type="images/x-icon" href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../../Assets/Css/Admin/Berita.css">
</head>

<body>
<div id="sidebar"></div>

<main class="content" id="content">
    <div id="header"></div>

    <div class="top-controls">
        <div class="left-tools">
            <!-- SEARCH -->
            <div class="search-box">
                <form method="GET" class="search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Cari" value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="status" value="<?= htmlspecialchars($filterStatus) ?>">
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                    <input type="hidden" name="page" value="1">
                </form>
            </div>

            <div class="filter-area">
                <div class="filter-dropdown">
                    <button type="button" class="filter-toggle">
                        <i class="fa-solid fa-sliders"></i>
                        <span><?= $filterStatus ?: "Semua Status" ?></span>
                        <i class="fa-solid fa-chevron-down caret"></i>
                    </button>

                    <div class="filter-menu">
                        <div class="filter-section">
                            <div class="filter-section-title">Status</div>

                            <a class="filter-item <?= activeClass($filterStatus === '') ?>"
                               href="<?= htmlspecialchars(build_query([
                                   'status' => null,
                                   'page'   => 1,
                                   'search' => $search,
                                   'sort'   => $sort
                               ])) ?>">
                                Semua
                            </a>

                            <?php foreach ($statusList as $s): ?>
                                <a class="filter-item <?= activeClass($filterStatus === $s) ?>"
                                   href="<?= htmlspecialchars(build_query([
                                       'status' => $s,
                                       'page'   => 1,
                                       'search' => $search,
                                       'sort'   => $sort
                                   ])) ?>">
                                    <?= htmlspecialchars($s) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <a id="clearFilterBtn" href="<?= htmlspecialchars(build_query([
                        'status' => null,
                        'page'   => 1
                        ])) ?>" class="clear-filter">
                    Hapus Filter
                    </a>

                </div>
            </div>
        </div>

        <!-- RIGHT TOOLS -->
        <div class="right-tools">
            <a href="ExportBerita.php" style="text-decoration:none;">
                <button type="button" class="export">
                    <i class="fa-solid fa-arrow-up-from-bracket"></i> Export
                </button>
            </a>

            <!-- SORT -->
            <div class="sort-wrapper">
                <button id="sort-btn" class="sort" type="button">
                    <i class="fa-solid fa-arrow-down-wide-short"></i>
                    Urutkan: <strong id="sort-label"><?= htmlspecialchars($sortLabel) ?></strong>
                </button>

                <div id="sort-menu" class="sort-menu hidden">
                    <div data-sort="default" class="<?= $sort === 'default' ? 'active' : '' ?>">Default</div>
                    <div data-sort="latest"  class="<?= $sort === 'latest'  ? 'active' : '' ?>">Terbaru</div>
                    <div data-sort="oldest"  class="<?= $sort === 'oldest'  ? 'active' : '' ?>">Terlama</div>
                    <div data-sort="az"      class="<?= $sort === 'az'      ? 'active' : '' ?>">A–Z</div>
                    <div data-sort="za"      class="<?= $sort === 'za'      ? 'active' : '' ?>">Z–A</div>
                </div>
            </div>

            <a href="tambah_berita.php" class="btn-primary">
                <i class="fa-solid fa-plus"></i>&nbsp; Tambah
            </a>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-container">
        <table>
            <thead>
            <tr>
                <th><input type="checkbox" class="checkbox" id="checkAll"></th>
                <th>ID</th>
                <th>judul</th>
                <th>isi</th>
                <th>gambar</th>
                <th>tanggal</th>
                <th>uploader</th>
                <th>status</th>
                <th></th>
            </tr>
            </thead>

            <tbody>
            <?php if (empty($berita_list)): ?>
                <tr>
                    <td colspan="9" style="text-align: center; padding: 40px; color: #94a3b8;">
                        Tidak ada data berita
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($berita_list as $berita): ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="checkbox" value="<?= (int)$berita['id_berita'] ?>">
                        </td>
                        <td><?= str_pad((string)$berita['id_berita'], 2, '0', STR_PAD_LEFT) ?></td>
                        <td style="max-width: 300px;">
                            <div class="berita-judul"><?= htmlspecialchars($berita['judul']) ?></div>
                        </td>
                        <td style="max-width: 400px;">
                            <div class="berita-isi"><?= htmlspecialchars(mb_substr($berita['isi'], 0, 150)) ?>...</div>
                        </td>
                        <td>
                            <?php if (!empty($berita['gambar'])): ?>
                                <img src="../../../Assets/Image/Galeri-Berita/<?= htmlspecialchars($berita['gambar']) ?>"
                                     alt="Gambar Berita"
                                     class="berita-thumbnail"
                                     style="width: 120px; height: 80px; border-radius: 8px;">
                            <?php else: ?>
                                <div style="width: 120px; height: 80px; background: #f1f5f9; border-radius: 8px; display:flex; align-items:center; justify-content:center; color:#94a3b8;">
                                    <img src="../../../Assets/Image/Galeri-Berita/No-Picture.jpg" alt="No Picture">
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?= !empty($berita['tanggal']) ? date('d-m-Y', strtotime($berita['tanggal'])) : '-' ?></td>
                        <td><?= htmlspecialchars($berita['nama_uploader'] ?? 'Unknown') ?></td>
                        <td>
                            <span class="status-badge status-<?= strtolower((string)$berita['status']) ?>">
                                <?= htmlspecialchars(ucfirst((string)$berita['status'])) ?>
                            </span>
                        </td>
                        <td class="action-cell">
                            <button class="action-toggle" onclick="toggleMenu(event, <?= (int)$berita['id_berita'] ?>)" type="button">
                                <i class="fa-solid fa-ellipsis-vertical"></i>
                            </button>

                            <div class="action-menu" id="menu-<?= (int)$berita['id_berita'] ?>">
                                <a href="edit_berita.php?id=<?= (int)$berita['id_berita'] ?>" class="action-item">
                                    <i class="fa-solid fa-pen"></i>
                                    <span>Edit</span>
                                </a>

                                <button type="button" class="action-item action-delete"
                                    onclick='return confirmDelete(<?= (int)$berita["id_berita"] ?>, <?= json_encode($berita["judul"]) ?>);' style="border-radius: 0px">
                                            <i class="fa-solid fa-trash-can"></i>
                                            <span>Hapus</span>
                                </button>

                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- FOOTER + PAGINATION -->
    <div class="table-footer" id="deleteToast">
        <div class="delete-selection" style="cursor: pointer">
            <button class="btn-delete-bulk" onclick="return confirmBulkDelete()" type="button" style="background: transparent; color: #1E5AA8;">
                <i class="fa-solid fa-trash"></i>
                Hapus data yang dipilih
            </button>
        </div>

        <div class="pagination">
            <?php if ($page > 1): ?>
                <a class="page-link prev" href="<?= htmlspecialchars(build_query(['page' => $page - 1])) ?>">&laquo;</a>
            <?php endif; ?>

            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
                <a class="page-link <?= $p == $page ? 'active' : '' ?>"
                   href="<?= htmlspecialchars(build_query(['page' => $p])) ?>">
                    <?= $p ?>
                </a>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a class="page-link next" href="<?= htmlspecialchars(build_query(['page' => $page + 1])) ?>">&raquo;</a>
            <?php endif; ?>
        </div>
    </div>
</main>

<script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
<script src="../../../Assets/Javascript/Admin/Header.js"></script>
<script src="../../../Assets/Javascript/Admin/berita.js"></script>

<script>
  // untuk notif di berita.js kamu (kalau dipakai)
  window.profileStatus = <?= json_encode($_GET['status_notif'] ?? '') ?>;
  window.profileMessage = <?= json_encode($_GET['message'] ?? '') ?>;
  window.profileRedirectUrl = <?= json_encode($_GET['redirect'] ?? '') ?>;
</script>

</body>
</html>
