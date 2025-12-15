<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require 'koneksi.php';

// tampilkan error tapi sembunyikan DEPRECATED
error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

// ========================
// Ambil parameter URL (samain dengan Berita)
// ========================
$search   = trim($_GET['search'] ?? '');
$kategori = (int)($_GET['kategori'] ?? 0);
$sort     = $_GET['sort'] ?? 'default';

// pagination
$perPage = 10;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

// helper build query (buat pagination + link filter)
function build_query(array $overrides = []): string {
    $current = $_GET ?? [];
    foreach ($overrides as $k => $v) {
        if ($v === null || $v === '') unset($current[$k]);
        else $current[$k] = $v;
    }
    return '?' . http_build_query($current);
}

function activeClass($cond) { return $cond ? 'active' : ''; }

// ========================
// Ambil daftar kategori
// ========================
$kategori_options = [];
$res_kategori = pg_query($conn, "SELECT id_kategori, nama_kategori FROM kategori_karya ORDER BY nama_kategori ASC");
if ($res_kategori) {
    while ($row = pg_fetch_assoc($res_kategori)) {
        $kategori_options[(int)$row['id_kategori']] = $row['nama_kategori'];
    }
}

// validasi kategori param
if ($kategori > 0 && !isset($kategori_options[$kategori])) $kategori = 0;

// label kategori untuk tombol filter
$kategoriLabel = $kategori > 0 ? ($kategori_options[$kategori] ?? 'Semua Kategori') : 'Semua Kategori';

// ========================
// WHERE + params aman (pg_query_params)
// ========================
$where = [];
$params = [];
$idx = 1;

$sqlBase = "
    FROM karya k
    LEFT JOIN kategori_karya c ON k.id_kategori = c.id_kategori
";

if ($search !== '') {
    $where[] = "(k.judul ILIKE $" . $idx . " OR k.deskripsi ILIKE $" . ($idx+1) . " OR COALESCE(c.nama_kategori,'') ILIKE $" . ($idx+2) . " OR k.uploaded_by::text ILIKE $" . ($idx+3) . " OR COALESCE(k.link,'') ILIKE $" . ($idx+4) . ")";
    $like = "%{$search}%";
    $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
    $idx += 5;
}

if ($kategori > 0) {
    $where[] = "k.id_kategori = $" . $idx;
    $params[] = $kategori;
    $idx++;
}

$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

// ========================
// SORT (whitelist)
// ========================
switch ($sort) {
    case 'latest':
        $orderBy = "ORDER BY k.uploaded_at DESC";
        $sortLabel = "Terbaru";
        break;
    case 'oldest':
        $orderBy = "ORDER BY k.uploaded_at ASC";
        $sortLabel = "Terlama";
        break;
    case 'az':
        $orderBy = "ORDER BY LOWER(k.judul) ASC";
        $sortLabel = "Judul A–Z";
        break;
    case 'za':
        $orderBy = "ORDER BY LOWER(k.judul) DESC";
        $sortLabel = "Judul Z–A";
        break;
    default:
        $orderBy = "ORDER BY k.id_karya DESC";
        $sortLabel = "Default";
        break;
}

// ========================
// COUNT total
// ========================
$sqlCount = "SELECT COUNT(*) AS total {$sqlBase} {$whereSql}";
$resCount = pg_query_params($conn, $sqlCount, $params);
$totalData = 0;
if ($resCount) {
    $row = pg_fetch_assoc($resCount);
    $totalData = (int)($row['total'] ?? 0);
}
$totalPages = max(1, (int)ceil($totalData / $perPage));
if ($page > $totalPages) $page = $totalPages;

// ========================
// DATA per halaman
// ========================
$sqlData = "
    SELECT
        k.id_karya,
        k.judul,
        k.deskripsi,
        k.link,
        k.uploaded_at,
        k.uploaded_by,
        k.id_kategori,
        COALESCE(c.nama_kategori,'-') AS nama_kategori
    {$sqlBase}
    {$whereSql}
    {$orderBy}
    LIMIT {$perPage} OFFSET {$offset}
";

$resData = pg_query_params($conn, $sqlData, $params);
$karya_list = [];
if ($resData) {
    while ($r = pg_fetch_assoc($resData)) $karya_list[] = $r;
}

$user_name = $_SESSION['user_name'] ?? ($_SESSION['username'] ?? 'Admin');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Karya</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <link rel="icon" type="images/x-icon" href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- IMPORTANT: biar 1:1 sama tampilan index Berita -->
    <link rel="stylesheet" href="../../../Assets/Css/Admin/Berita.css">
</head>

<body>
<div id="sidebar"></div>

<main class="content" id="content">
    <div id="header"></div>

    <!-- TOP CONTROLS (copy gaya Berita) -->
    <div class="top-controls">
        <div class="left-tools">

            <!-- SEARCH -->
            <div class="search-box">
                <form method="GET" class="search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Cari karya..." value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="kategori" value="<?= (int)$kategori ?>">
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                    <input type="hidden" name="page" value="1">
                </form>
            </div>

            <!-- FILTER -->
            <div class="filter-area">
                <div class="filter-dropdown">
                    <button type="button" class="filter-toggle">
                        <i class="fa-solid fa-sliders"></i>
                        <span><?= htmlspecialchars($kategoriLabel) ?></span>
                        <i class="fa-solid fa-chevron-down caret"></i>
                    </button>

                    <div class="filter-menu">
                        <div class="filter-section">
                            <div class="filter-section-title">Kategori</div>

                            <a class="filter-item <?= activeClass($kategori === 0) ?>"
                               href="<?= htmlspecialchars(build_query(['kategori' => null, 'page' => 1])) ?>">
                                Semua
                            </a>

                            <?php foreach ($kategori_options as $id_kat => $nama_kat): ?>
                                <a class="filter-item <?= activeClass($kategori === (int)$id_kat) ?>"
                                   href="<?= htmlspecialchars(build_query(['kategori' => $id_kat, 'page' => 1])) ?>">
                                    <?= htmlspecialchars($nama_kat) ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <a id="clearFilterBtn" href="<?= htmlspecialchars(build_query([
                        'kategori' => null,
                        'search'   => null,
                        'sort'     => null,
                        'page'     => 1
                    ])) ?>" class="clear-filter">
                        Hapus Filter
                    </a>
                </div>
            </div>

        </div>

        <!-- RIGHT TOOLS -->
        <div class="right-tools">

            <a href="export_karya.php<?= htmlspecialchars(build_query([])) ?>" style="text-decoration:none;">
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

            <a href="tambah_edit.php" class="btn-primary">
                <i class="fa-solid fa-plus"></i>&nbsp; Tambah
            </a>
        </div>
    </div>

    <!-- TABLE -->
    <form action="hapus.php" method="POST" id="bulkDeleteForm">
        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th><input type="checkbox" class="checkbox" id="checkAll"></th>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Kategori</th>
                    <th>Link</th>
                    <th>Uploader</th>
                    <th>Tanggal</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                <?php if (empty($karya_list)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #94a3b8;">
                            Tidak ada data karya
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($karya_list as $k): ?>
                        <?php
                            $id = (int)$k['id_karya'];
                            $judul = (string)($k['judul'] ?? '');
                            $deskripsi = (string)($k['deskripsi'] ?? '');
                            $link = (string)($k['link'] ?? '');
                            $namaKategori = (string)($k['nama_kategori'] ?? '-');
                            $uploadedBy = (string)($k['uploaded_by'] ?? '-');
                            $tanggal = !empty($k['uploaded_at']) ? date('d-m-Y', strtotime($k['uploaded_at'])) : '-';
                        ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="checkbox" name="ids[]" value="<?= $id ?>">
                            </td>

                            <td><?= str_pad((string)$id, 2, '0', STR_PAD_LEFT) ?></td>

                            <td style="max-width: 260px;">
                                <div class="berita-judul"><?= htmlspecialchars($judul) ?></div>
                            </td>

                            <td style="max-width: 420px;">
                                <div class="berita-isi">
                                    <?= htmlspecialchars(mb_substr($deskripsi, 0, 150)) ?><?= (mb_strlen($deskripsi) > 150 ? '...' : '') ?>
                                </div>
                            </td>

                            <td>
                                <span class="status-badge">
                                    <?= htmlspecialchars($namaKategori) ?>
                                </span>
                            </td>

                            <td style="max-width: 260px;">
                                <?php if ($link): ?>
                                    <a href="<?= htmlspecialchars($link) ?>" target="_blank" style="color:#1b6ce5; text-decoration:none;">
                                        <?= htmlspecialchars(mb_substr($link, 0, 35)) ?><?= (mb_strlen($link) > 35 ? '...' : '') ?>
                                    </a>
                                <?php else: ?>
                                    <span style="color:#94a3b8;">-</span>
                                <?php endif; ?>
                            </td>

                            <td><?= htmlspecialchars($uploadedBy) ?></td>
                            <td><?= htmlspecialchars($tanggal) ?></td>

                            <td class="action-cell">
                                <button class="action-toggle" onclick="toggleMenu(event, <?= $id ?>)" type="button">
                                    <i class="fa-solid fa-ellipsis-vertical"></i>
                                </button>

                                <div class="action-menu" id="menu-<?= $id ?>">
                                    <a href="tambah_edit.php?id=<?= $id ?>" class="action-item">
                                        <i class="fa-solid fa-pen"></i>
                                        <span>Edit</span>
                                    </a>

                                    <!-- SIMPLE CONFIRM (sesuai request kamu) -->
                                    <button type="button" class="action-item action-delete"
                                            onclick='return confirmDelete(<?= $id ?>, <?= json_encode($judul) ?>);'>
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

        <!-- FOOTER + PAGINATION (copy gaya Berita) -->
        <div class="table-footer" id="deleteToast">
            <div class="delete-selection" style="cursor: pointer">
                <button class="btn-delete-bulk" type="button" onclick="return confirmBulkDelete();">
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
    </form>

</main>

<script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
<script src="../../../Assets/Javascript/Admin/Header.js"></script>

<!-- JS khusus karya (ngikut behaviour Berita tapi delete = confirm) -->
<script src="../../../Assets/Javascript/Admin/Karya.js"></script>

</body>
</html>