<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require __DIR__ . '../../../Koneksi/KoneksiSasa.php';

function build_query($params)
{
    $base = "IndexPublikasi.php";
    $query = [];

    foreach ($params as $key => $value) {
        if ($value !== null && $value !== "") {
            $query[] = urlencode($key) . "=" . urlencode($value);
        }
    }

    return $base . (count($query) ? "?" . implode("&", $query) : "");
}

$search      = isset($_GET['q']) ? trim($_GET['q']) : "";
$filterJenis = isset($_GET['jenis']) ? trim($_GET['jenis']) : "";
$sort        = isset($_GET['sort']) ? $_GET['sort'] : "default";

$page  = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 8;
$offset = ($page - 1) * $limit;
$riset_map = [];

$resRiset = q("SELECT id_riset, nama_bidang_riset FROM bidangriset");

while ($r = pg_fetch_assoc($resRiset)) {
    $riset_map[$r['id_riset']] = $r['nama_bidang_riset'];
}

$jenisList = [];
$resJenis = q("SELECT DISTINCT jenis FROM publikasi ORDER BY jenis ASC");

while ($row = pg_fetch_assoc($resJenis)) {
    $jenisList[] = $row['jenis'];
}

$where = "WHERE 1=1";
$params = [];
$i = 1;

if ($search !== "") {
    $where .= " AND (judul ILIKE $" . $i . " OR author::text ILIKE $" . $i . ")";
    $params[] = '%' . $search . '%';
    $i++;
}

if ($filterJenis !== "" && in_array($filterJenis, $jenisList)) {
    $where .= " AND jenis = $" . $i;
    $params[] = $filterJenis;
    $i++;
}

switch ($sort) {
    case "latest":
        $orderSql = "ORDER BY tanggal_terbit DESC";
        $sortLabel = "Terbaru";
        break;
    case "oldest":
        $orderSql = "ORDER BY tanggal_terbit ASC";
        $sortLabel = "Terlama";
        break;
    case "az":
        $orderSql = "ORDER BY LOWER(judul) ASC";
        $sortLabel = "Judul A–Z";
        break;
    case "za":
        $orderSql = "ORDER BY LOWER(judul) DESC";
        $sortLabel = "Judul Z–A";
        break;
    default:
        $orderSql = "ORDER BY id_publikasi DESC";
        $sortLabel = "Default";
        break;
}

$totalSql = "SELECT COUNT(*) AS total FROM publikasi $where";
$resTotal = empty($params)
    ? q($totalSql)
    : qparams($totalSql, $params);

$total = (int)pg_fetch_assoc($resTotal)['total'];
$totalPages = max(1, ceil($total / $limit));

$dataSql = "
        SELECT *
        FROM publikasi
        $where
        $orderSql
        LIMIT $limit OFFSET $offset
    ";

$result = empty($params)
    ? q($dataSql)
    : qparams($dataSql, $params);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Publikasi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="icon" type="images/x-icon"
        href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="../../../Assets/Css/Admin/Publikasi.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div id="sidebar"></div>

    <main class="content" id="content">
        <div id="header"></div>

        <div class="top-controls">
            <div class="left-tools">
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input
                        id="search-input"
                        type="text"
                        placeholder="Cari"
                        value="<?= htmlspecialchars($search) ?>">
                </div>

                <div class="filter-area">
                    <div class="filter-dropdown">
                        <button type="button" class="filter-toggle">
                            <i class="fa-solid fa-sliders"></i>
                            <span><?= $filterJenis ?: "Semua Jenis" ?></span>
                            <i class="fa-solid fa-chevron-down caret"></i>
                        </button>

                        <div class="filter-menu">
                            <div class="filter-section">
                                <div class="filter-section-title">Jenis Publikasi</div>

                                <a class="filter-item <?= $filterJenis == "" ? "active" : "" ?>"
                                    href="<?= htmlspecialchars(build_query([
                                                'jenis' => null,
                                                'page' => 1,
                                                'q' => $search,
                                                'sort' => $sort
                                            ])) ?>">
                                    Semua
                                </a>

                                <?php foreach ($jenisList as $j): ?>
                                    <a class="filter-item <?= $filterJenis === $j ? "active" : "" ?>"
                                        href="<?= htmlspecialchars(build_query([
                                                    'jenis' => $j,
                                                    'page' => 1,
                                                    'q' => $search,
                                                    'sort' => $sort
                                                ])) ?>">
                                        <?= htmlspecialchars($j) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <a href="<?= htmlspecialchars(build_query([
                                    'jenis' => null,
                                    'page' => 1,
                                    'q' => $search,
                                    'sort' => $sort
                                ])) ?>" class="clear-filter">Hapus Filter
                    </a>
                </div>
            </div>
            <div class="right-tools">

                <a href="ExportPublikasi.php" style="text-decoration:none;">
                    <button type="button" class="export">
                        <i class="fa-solid fa-arrow-up-from-bracket"></i> Export
                    </button>
                </a>

                <div class="sort-wrapper">
                    <button id="sort-btn" class="sort">
                        <i class="fa-solid fa-arrow-down-wide-short"></i>
                        Urutkan: <strong id="sort-label"><?= $sortLabel ?></strong>
                    </button>

                    <div id="sort-menu" class="sort-menu hidden">
                        <div data-sort="default" class="<?= $sort === 'default' ? 'active' : '' ?>">Default</div>
                        <div data-sort="latest" class="<?= $sort === 'latest' ? 'active' : '' ?>">Terbaru</div>
                        <div data-sort="oldest" class="<?= $sort === 'oldest' ? 'active' : '' ?>">Terlama</div>
                        <div data-sort="az" class="<?= $sort === 'az' ? 'active' : '' ?>">A–Z</div>
                        <div data-sort="za" class="<?= $sort === 'za' ? 'active' : '' ?>">Z–A</div>
                    </div>
                </div>

                <a href="TambahEditPublikasi.php" class="btn-primary">
                    <i class="fa-solid fa-plus"></i>&nbsp; Tambah
                </a>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>Judul</th>
                        <th>Jenis</th>
                        <th>Author</th>
                        <th>Riset</th>
                        <th>Tanggal Terbit</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    <?php if ($total === 0): ?>
                        <tr>
                            <td colspan="9" class="no-data">Belum ada data.</td>
                        </tr>

                    <?php else: ?>
                        <?php while ($row = pg_fetch_assoc($result)): ?>
                            <tr>
                                <td>
                                    <input
                                        type="checkbox"
                                        class="row-check"
                                        value="<?= htmlspecialchars($row['id_publikasi']) ?>">
                                </td>

                                <td><?= $row['id_publikasi'] ?></td>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td><?= htmlspecialchars($row['jenis']) ?></td>

                                <td>
                                    <?php
                                    $a = json_decode($row['author'], true);
                                    echo htmlspecialchars(is_array($a) ? implode(", ", $a) : $row['author']);
                                    ?>
                                </td>

                                <td>
                                    <?php
                                    $id_r = $row['id_riset'];
                                    echo htmlspecialchars($riset_map[$id_r] ?? "-");
                                    ?>
                                </td>

                                <td><?= htmlspecialchars($row['tanggal_terbit']) ?></td>

                                <td>
                                    <?php
                                    $status = in_array($row['status'], ['t', '1', 'true', 'Aktif'], true)
                                        ? "<span class='badge aktif'>Aktif</span>"
                                        : "<span class='badge draft'>Draft</span>";
                                    echo $status;
                                    ?>
                                </td>

                                <td class="action-cell">
                                    <button
                                        type="button"
                                        class="action-toggle"
                                        aria-haspopup="true"
                                        aria-expanded="false">
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>

                                    <div class="action-menu">
                                        <a href="TambahEditPublikasi.php?id=<?= $row['id_publikasi'] ?>" class="action-item">
                                            <i class="fa-solid fa-pen"></i>
                                            <span>Edit</span>
                                        </a>

                                        <button
                                            type="button"
                                            class="action-item action-delete"
                                            data-id="<?= $row['id_publikasi'] ?>" style="border-radius: 0">
                                            <i class="fa-solid fa-trash-can"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </div>

                                    <!-- form POST untuk delete -->
                                    <form method="post"
                                        action="DeletePublikasi.php"
                                        class="delete-publikasi-form">
                                        <input type="hidden" name="id_publikasi"
                                            value="<?= $row['id_publikasi'] ?>">
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="delete-selection" style="cursor: pointer">
                <i class="fa-solid fa-trash"></i>
                Hapus data yang dipilih
            </div>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?>&q=<?= urlencode($search) ?>&jenis=<?= urlencode($filterJenis) ?>&sort=<?= $sort ?>" class="page-link prev">&laquo;</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <a class="page-link <?= $i == $page ? 'active' : '' ?>"
                        href="?page=<?= $i ?>&q=<?= urlencode($search) ?>&jenis=<?= urlencode($filterJenis) ?>&sort=<?= $sort ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?= $page + 1 ?>&q=<?= urlencode($search) ?>&jenis=<?= urlencode($filterJenis) ?>&sort=<?= $sort ?>" class="page-link next">&raquo;</a>
                <?php endif; ?>
            </div>
        </div>

    </main>

    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script src="../../../Assets/Javascript/Admin/Header.js"></script>
    <script src="../../../Assets/Javascript/Admin/Publikasi.js"></script>

</body>

</html>