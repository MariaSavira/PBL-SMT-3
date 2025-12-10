<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require __DIR__ . '../../../Koneksi/KoneksiSasa.php';

    $perPage = 6;
    $page    = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    if ($page < 1) $page = 1;

    $search      = trim($_GET['q'] ?? '');
    $filterField = $_GET['filter_field'] ?? '';
    $filterValue = trim($_GET['filter_value'] ?? '');
    $sort        = $_GET['sort'] ?? 'default';

    $conditions = [];
    $params     = [];
    $idx        = 1;

    if ($filterField !== '' && $filterValue !== '') {
        $ff = strtolower($filterField);
        $fv = strtolower($filterValue);

        if ($ff === 'jabatan') {
            $conditions[] = "LOWER(jabatan) = $" . $idx;
            $params[]     = $fv;
            $idx++;
        } elseif ($ff === 'status') {
            if ($fv === 'aktif') {
                $conditions[] = "status = TRUE";
            } elseif ($fv === 'nonaktif') {
                $conditions[] = "status = FALSE";
            }
        } elseif ($ff === 'keahlian') {
            $conditions[] = "keahlian::text ILIKE $" . $idx;
            $params[]     = '%' . $filterValue . '%';
            $idx++;
        }
    }

    $whereSql = $conditions ? 'WHERE ' . implode(' AND ', $conditions) : '';

    switch ($sort) {
        case 'nama_asc':
            $orderSql  = 'ORDER BY nama ASC';
            $sortLabel = 'Nama A-Z';
            break;
        case 'nama_desc':
            $orderSql  = 'ORDER BY nama DESC';
            $sortLabel = 'Nama Z-A';
            break;
        default:
            $orderSql  = 'ORDER BY id_anggota ASC';
            $sortLabel = 'Default';
            $sort      = 'default';
    }

    $currentFilterLabel = 'Filter';
    if ($filterField !== '' && $filterValue !== '') {
        if ($filterField === 'jabatan') {
            $currentFilterLabel = 'Jabatan: ' . $filterValue;
        } elseif ($filterField === 'status') {
            $currentFilterLabel = 'Status: ' . ucfirst($filterValue);
        } elseif ($filterField === 'keahlian') {
            $currentFilterLabel = 'Keahlian: ' . $filterValue;
        }
    }

    if (isset($_GET['export']) && $_GET['export'] === '1') {
        $sqlExport = "SELECT id_anggota, nama, keahlian, jabatan, foto, status
                        FROM mv_anggota_keahlian
                        $whereSql
                        $orderSql";

        if ($params) {
            $resExport = qparams($sqlExport, $params);
        } else {
            $resExport = q($sqlExport);
        }

        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="anggota_lab.csv"');

        $out = fopen('php://output', 'w');
        fputcsv($out, ['id_anggota', 'nama', 'keahlian', 'jabatan', 'foto', 'status']);

        while ($row = pg_fetch_assoc($resExport)) {
            fputcsv($out, [
                $row['id_anggota'],
                $row['nama'],
                $row['keahlian'],
                $row['jabatan'],
                $row['foto'],
                ($row['status'] === 't' || $row['status'] === true) ? 'Aktif' : 'Nonaktif',
            ]);
        }

        fclose($out);
        exit;
    }

    if ($params) {
        $sqlCount = "SELECT COUNT(*) AS total
                        FROM mv_anggota_keahlian
                        $whereSql";
        $resCount = qparams($sqlCount, $params);
    } else {
        $resCount = q("SELECT COUNT(*) AS total FROM mv_anggota_keahlian");
    }

    $countRow  = pg_fetch_assoc($resCount);
    $totalData = (int) ($countRow['total'] ?? 0);
    $totalPages = max(1, (int) ceil($totalData / $perPage));

    if ($page > $totalPages) {
        $page = $totalPages;
    }

    $offset = ($page - 1) * $perPage;

    $paramsWithPage   = $params;
    $paramsWithPage[] = $perPage;
    $paramsWithPage[] = $offset;

    $sql = "SELECT *
                FROM mv_anggota_keahlian
                $whereSql
                $orderSql
                LIMIT $" . $idx . " OFFSET $" . ($idx + 1);

    $res  = qparams($sql, $paramsWithPage);
    $rows = pg_fetch_all($res) ?: [];

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
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index Anggota Lab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="icon" type="images/x-icon"
        href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../../Assets/Css/Admin/AnggotaLab.css">
</head>

<body>
    <div id="sidebar"></div>
    <main class="content" id="content">

        <div id="header"></div>

        <div class="top-controls">
            <form method="get" class="search-box">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input
                    type="text"
                    name="q"
                    placeholder="Cari"
                    value="<?= htmlspecialchars($search) ?>">
                <?php if ($filterField !== '' && $filterValue !== ''): ?>
                    <input type="hidden" name="filter_field" value="<?= htmlspecialchars($filterField) ?>">
                    <input type="hidden" name="filter_value" value="<?= htmlspecialchars($filterValue) ?>">
                <?php endif; ?>
                <?php if ($sort !== 'default'): ?>
                    <input type="hidden" name="sort" value="<?= htmlspecialchars($sort) ?>">
                <?php endif; ?>
            </form>

            <div class="filter-area">
                <div class="filter-dropdown">
                    <button type="button" class="filter-toggle">
                        <i class="fa-solid fa-sliders"></i>
                        <span><?= htmlspecialchars($currentFilterLabel) ?></span>
                        <i class="fa-solid fa-chevron-down caret"></i>
                    </button>

                    <div class="filter-menu">
                        <div class="filter-section">
                            <div class="filter-section-title">Jabatan</div>
                            <a href="<?= htmlspecialchars(build_query([
                                            'filter_field' => 'jabatan',
                                            'filter_value' => 'Kepala Lab',
                                            'page'        => 1
                                        ])) ?>" class="filter-item">Kepala Lab</a>

                            <a href="<?= htmlspecialchars(build_query([
                                            'filter_field' => 'jabatan',
                                            'filter_value' => 'Peneliti',
                                            'page'        => 1
                                        ])) ?>" class="filter-item">Peneliti</a>
                        </div>

                        <div class="filter-section">
                            <div class="filter-section-title">Status</div>
                            <a href="<?= htmlspecialchars(build_query([
                                            'filter_field' => 'status',
                                            'filter_value' => 'aktif',
                                            'page'        => 1
                                        ])) ?>" class="filter-item">Aktif</a>

                            <a href="<?= htmlspecialchars(build_query([
                                            'filter_field' => 'status',
                                            'filter_value' => 'nonaktif',
                                            'page'        => 1
                                        ])) ?>" class="filter-item">Nonaktif</a>
                        </div>

                        <div class="filter-section">
                            <div class="filter-section-title">Keahlian</div>
                            <!-- contoh, silakan tambah/ubah sesuai data -->
                            <a href="<?= htmlspecialchars(build_query([
                                            'filter_field' => 'keahlian',
                                            'filter_value' => 'Data Science',
                                            'page'        => 1
                                        ])) ?>" class="filter-item">Data Science</a>

                            <a href="<?= htmlspecialchars(build_query([
                                            'filter_field' => 'keahlian',
                                            'filter_value' => 'Natural Language Processing',
                                            'page'        => 1
                                        ])) ?>" class="filter-item">Natural Language Processing</a>

                            <a href="<?= htmlspecialchars(build_query([
                                            'filter_field' => 'keahlian',
                                            'filter_value' => 'Business Intelligence',
                                            'page'        => 1
                                        ])) ?>" class="filter-item">Business Intelligence</a>

                            <a href="<?= htmlspecialchars(build_query([
                                            'filter_field' => 'keahlian',
                                            'filter_value' => 'IT Governance',
                                            'page'        => 1
                                        ])) ?>" class="filter-item">IT Governance</a>
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
                <?php
                $exportUrl = build_query([
                    'export' => 1,
                    'page'   => null,
                ]);

                $nextSort = $sort === 'default'
                    ? 'nama_asc'
                    : ($sort === 'nama_asc' ? 'nama_desc' : 'default');

                $sortUrl = build_query([
                    'sort' => $nextSort,
                    'page' => 1,
                ]);
                ?>

                <button
                    type="button"
                    class="export"
                    onclick="window.location.href='<?= htmlspecialchars($exportUrl, ENT_QUOTES) ?>'">
                    <i class="fa-solid fa-arrow-up-from-bracket"></i> Export
                </button>

                <button
                    type="button"
                    class="sort"
                    onclick="window.location.href='<?= htmlspecialchars($sortUrl, ENT_QUOTES) ?>'">
                    <i class="fa-solid fa-arrow-down-wide-short"></i>
                    Urutkan : <strong><?= htmlspecialchars($sortLabel) ?></strong>
                </button>

                <button class="add">
                    <a href="createAnggota.php" style="link">
                        <i class="fa-solid fa-plus"></i> Tambah</a>
                </button>
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
                                <td><input type="checkbox" style="width: 15px; height: 15px;"></td>
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

                                <td class="action-cell">
                                    <button
                                        type="button"
                                        class="action-toggle"
                                        aria-haspopup="true"
                                        aria-expanded="false"
                                    >
                                        <i class="fa-solid fa-ellipsis-vertical"></i>
                                    </button>

                                    <div class="action-menu">
                                        <a href="EditAnggota.php?id=<?= $row['id_anggota'] ?>" class="action-item">
                                            <i class="fa-solid fa-pen"></i>
                                            <span>Edit</span>
                                        </a>

                                        <button
                                            type="button"
                                            class="action-item action-delete"
                                            data-id="<?= $row['id_anggota'] ?>" style="border-radius: 0">
                                            <i class="fa-solid fa-trash-can"></i>
                                            <span>Hapus</span>
                                        </button>
                                    </div>

                                    <!-- form POST untuk delete -->
                                    <form method="post"
                                        action="DeleteAnggota.php"
                                        class="delete-anggota-form">
                                        <input type="hidden" name="id_anggota"
                                            value="<?= $row['id_anggota'] ?>">
                                    </form>
                                </td>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <div class="delete-selection" style="cursor: pointer">
                <i class="fa-solid fa-trash"></i> Hapus data yang dipilih
            </div>

            <div class="pagination">
                <?php if ($totalPages > 1): ?>

                    <?php if ($page > 1): ?>
                        <a href="?page=<?= $page - 1 ?>" class="page-link prev">
                            &laquo; Sebelumnya
                        </a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i ?>"
                            class="page-link <?= ($i === $page) ? 'active' : '' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $totalPages): ?>
                        <a href="?page=<?= $page + 1 ?>" class="page-link next">
                            Berikutnya &raquo;
                        </a>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
        </div>
    </main>
    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script src="../../../Assets/Javascript/Admin/Header.js"></script>

    <script>
        window.pageStatus  = <?= json_encode($_GET['status']  ?? '') ?>;
        window.pageMessage = <?= json_encode($_GET['message'] ?? '') ?>;
    </script>
    <script src="../../../Assets/Javascript/Admin/AnggotaLab.js"></script>
</body>

</html>