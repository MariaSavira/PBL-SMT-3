<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require 'koneksi.php';

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

$search = trim($_GET['search'] ?? '');
$sort   = $_GET['sort'] ?? 'default';

$perPage = 6;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

function build_query(array $overrides = []): string {
    $current = $_GET ?? [];
    foreach ($overrides as $k => $v) {
        if ($v === null || $v === '') unset($current[$k]);
        else $current[$k] = $v;
    }
    return '?' . http_build_query($current);
}

function activeClass($cond) { return $cond ? 'active' : ''; }

$sqlBase = "
    FROM karya k
    LEFT JOIN anggotalab a ON k.uploaded_by = a.id_anggota
";

$where  = [];
$params = [];
$idx    = 1;

if ($search !== '') {
    $where[] = "(
        k.judul ILIKE $" . $idx . "
        OR k.deskripsi ILIKE $" . ($idx+1) . "
        OR COALESCE(a.nama,'') ILIKE $" . ($idx+2) . "
        OR COALESCE(k.link,'') ILIKE $" . ($idx+3) . "
    )";
    $like = "%{$search}%";
    $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
    $idx += 4;
}

$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

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

$sqlCount = "SELECT COUNT(*) AS total {$sqlBase} {$whereSql}";
$resCount = pg_query_params($conn, $sqlCount, $params);
$totalData = 0;
if ($resCount) {
    $row = pg_fetch_assoc($resCount);
    $totalData = (int)($row['total'] ?? 0);
}
$totalPages = max(1, (int)ceil($totalData / $perPage));
if ($page > $totalPages) $page = $totalPages;

$sqlData = "
    SELECT
        k.id_karya,
        k.judul,
        k.deskripsi,
        k.link,
        k.uploaded_at,
        COALESCE(a.nama, '-') AS uploader_nama
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

    <link rel="stylesheet" href="../../../Assets/Css/Admin/Berita.css">
</head>

<body>
<div id="sidebar"></div>

<main class="content" id="content">
    <div id="header"></div>
    
    <div class="top-controls">
        <div class="left-tools">
            
            <div class="search-box">
                <form method="GET" class="search-container">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" name="search" placeholder="Cari karya..." value="<?= htmlspecialchars($search) ?>">
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                    <input type="hidden" name="page" value="1">
                </form>
            </div>

        </div>
        
        <div class="right-tools">

            <a href="export_karya.php<?= htmlspecialchars(build_query([])) ?>" style="text-decoration:none;">
                <button type="button" class="export">
                    <i class="fa-solid fa-arrow-up-from-bracket"></i> Export
                </button>
            </a>

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

    
    <form action="hapus.php" method="POST" id="bulkDeleteForm">
        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th><input type="checkbox" class="checkbox" id="checkAll"></th>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Deskripsi</th>
                    <th>Link</th>
                    <th>Uploader</th>
                    <th>Tanggal</th>
                    <th></th>
                </tr>
                </thead>

                <tbody>
                <?php if (empty($karya_list)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #94a3b8;">
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
                            $uploadedBy = (string)($k['uploader_nama'] ?? '-');
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

                            <td>
                                <div class="deskripsi-ellipsis" title="<?= htmlspecialchars($deskripsi) ?>">
                                    <?= htmlspecialchars($deskripsi) ?>
                                </div>
                            </td>

                            <td style="max-width: 350px;">
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

        
        <div class="table-footer" id="deleteToast">
            <div class="delete-selection" style="cursor: pointer">
                <button class="btn-delete-bulk" type="button" onclick="return confirmBulkDelete();" style="background: transparent; color: #1E5AA8">
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
<script src="../../../Assets/Javascript/Admin/Karya.js"></script>

</body>
</html>
