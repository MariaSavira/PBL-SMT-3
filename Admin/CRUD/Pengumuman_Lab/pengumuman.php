<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require_once __DIR__ . '/config.php';

$conn = getDBConnection();

function build_query(array $overrides = []): string
{
    $current = $_GET ?? [];
    foreach ($overrides as $k => $v) {
        if ($v === null || $v === '') unset($current[$k]);
        else $current[$k] = $v;
    }
    return '?' . http_build_query($current);
}

$search = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$sort   = $_GET['sort'] ?? 'default';

$perPage = 8;
$page    = max(1, (int)($_GET['page'] ?? 1));
$offset  = ($page - 1) * $perPage;

$stmtSt = $conn->query("SELECT DISTINCT status FROM pengumuman ORDER BY status ASC");
$statusList = $stmtSt->fetchAll(PDO::FETCH_COLUMN) ?: [];

$fromSql = "
    FROM pengumuman p
    LEFT JOIN anggotalab a ON a.username = p.uploader
";

$where  = [];
$params = [];

if ($status !== '' && in_array($status, $statusList, true)) {
    $where[] = "p.status = :status";
    $params[':status'] = $status;
}

if ($search !== '') {
    $where[] = "(p.isi ILIKE :q OR p.uploader ILIKE :q OR COALESCE(a.nama,'') ILIKE :q)";
    $params[':q'] = '%' . $search . '%';
}

$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

switch ($sort) {
    case 'latest':
        $orderBy   = "ORDER BY p.tanggal_terbit DESC, p.id_pengumuman DESC";
        $sortLabel = "Terbaru";
        break;
    case 'oldest':
        $orderBy   = "ORDER BY p.tanggal_terbit ASC, p.id_pengumuman ASC";
        $sortLabel = "Terlama";
        break;
    default:
        $orderBy   = "ORDER BY p.id_pengumuman DESC";
        $sortLabel = "Default";
        break;
}

$sqlCount = "SELECT COUNT(*) AS total {$fromSql} {$whereSql}";
$stmtCount = $conn->prepare($sqlCount);
$stmtCount->execute($params);
$totalData = (int)($stmtCount->fetchColumn() ?? 0);

$totalPages = max(1, (int)ceil($totalData / $perPage));
if ($page > $totalPages) $page = $totalPages;

$sqlData = "
    SELECT
        p.id_pengumuman,
        p.isi,
        p.tanggal_terbit,
        p.status,
        p.uploader,
        COALESCE(a.nama, p.uploader) AS uploader_nama
    {$fromSql}
    {$whereSql}
    {$orderBy}
    LIMIT :limit OFFSET :offset
";
$stmtData = $conn->prepare($sqlData);

foreach ($params as $k => $v) $stmtData->bindValue($k, $v);

$stmtData->bindValue(':limit', (int)$perPage, PDO::PARAM_INT);
$stmtData->bindValue(':offset', (int)$offset, PDO::PARAM_INT);

$stmtData->execute();
$rows = $stmtData->fetchAll(PDO::FETCH_ASSOC) ?: [];

function e($s)
{
    return htmlspecialchars((string)$s);
}
function fmtDate($d)
{
    if (!$d) return '-';
    $ts = strtotime($d);
    return $ts ? date('d-m-Y', $ts) : e($d);
}
function ellipsis($text, $max = 90)
{
    $t = trim((string)$text);
    if (mb_strlen($t) <= $max) return $t;
    return mb_substr($t, 0, $max) . '...';
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pengumuman</title>

    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <link rel="icon" type="images/x-icon" href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    
    <link rel="stylesheet" href="../../../Assets/Css/Admin/Publikasi.css" />

    <style>
        .pengumuman-isi {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.5;
            color: #64748b;
            max-width: 520px;
        }
    </style>
</head>

<body>
    <div id="sidebar"></div>

    <main class="content" id="content">
        <div id="header"></div>

        <div class="top-controls">
            <div class="left-tools">
                
                <div class="search-box">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input id="search-input" type="text" placeholder="Cari" value="<?= htmlspecialchars($search) ?>">
                </div>

                
                <div class="filter-area">
                    <div class="filter-dropdown">
                        <button type="button" class="filter-toggle">
                            <i class="fa-solid fa-sliders"></i>
                            <span><?= htmlspecialchars($status ?: "Semua Status") ?></span>
                            <i class="fa-solid fa-chevron-down caret"></i>
                        </button>

                        <div class="filter-menu">
                            <div class="filter-section">
                                <div class="filter-section-title">Status</div>

                                <a class="filter-item <?= $status === '' ? 'active' : '' ?>"
                                    href="<?= htmlspecialchars(build_query(['status' => null, 'page' => 1])) ?>">
                                    Semua
                                </a>

                                <?php foreach ($statusList as $st): ?>
                                    <a class="filter-item <?= $status === $st ? 'active' : '' ?>"
                                        href="<?= htmlspecialchars(build_query(['status' => $st, 'page' => 1])) ?>">
                                        <?= htmlspecialchars($st) ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <a class="clear-filter"
                            href="<?= htmlspecialchars(build_query(['status' => null, 'q' => null, 'sort' => null, 'page' => 1])) ?>">
                            Hapus Filter
                        </a>
                    </div>
                </div>
            </div>

            <div class="right-tools">
                
                <a href="export_pengumuman.php<?= htmlspecialchars(build_query([])) ?>" style="text-decoration:none;">
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
                        <div data-sort="latest" class="<?= $sort === 'latest'  ? 'active' : '' ?>">Terbaru</div>
                        <div data-sort="oldest" class="<?= $sort === 'oldest'  ? 'active' : '' ?>">Terlama</div>
                    </div>
                </div>

                
                <a href="tambah_pengumuman.php" class="btn-primary">
                    <i class="fa-solid fa-plus"></i>&nbsp; Tambah
                </a>
            </div>
        </div>

        
        <form action="delete_pengumuman.php" method="POST" id="bulkDeleteForm">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><input type="checkbox" class="checkbox" id="checkAll"></th>
                            <th>ID</th>
                            <th>Isi</th>
                            <th>Tanggal</th>
                            <th>Uploader</th>
                            <th>Status</th>
                            <th></th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (empty($rows)): ?>
                            <tr>
                                <td colspan="7" style="text-align:center; padding:40px; color:#94a3b8;">
                                    Tidak ada data pengumuman
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($rows as $p): ?>
                                <?php
                                $id = (int)$p['id_pengumuman'];
                                $isi = (string)($p['isi'] ?? '');
                                $tgl = !empty($p['tanggal_terbit']) ? date('d-m-Y', strtotime($p['tanggal_terbit'])) : '-';
                                $upl = (string)($p['uploader_nama'] ?? '-');
                                $st  = (string)($p['status'] ?? '-');
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" class="checkbox row-check" name="selected_ids[]" value="<?= $id ?>">
                                    </td>

                                    <td><?= str_pad((string)$id, 2, '0', STR_PAD_LEFT) ?></td>

                                    <td style="max-width:560px;">
                                        <div class="pengumuman-isi" title="<?= htmlspecialchars($isi) ?>">
                                            <?= htmlspecialchars($isi) ?>
                                        </div>
                                    </td>

                                    <td><?= htmlspecialchars($tgl) ?></td>
                                    <td><?= htmlspecialchars($upl) ?></td>

                                    <td>
                                        <?php
                                        $rawStatus = trim((string)($p['status'] ?? ''));

                                        $isAktif = in_array($rawStatus, ['t', '1', 'true', 'Aktif'], true);
                                        $isNonaktif = in_array($rawStatus, ['f', '0', 'false', 'Nonaktif'], true);

                                        if ($isAktif) {
                                            echo "<span class='badge aktif'>Aktif</span>";
                                        } elseif ($isNonaktif) {
                                            echo "<span class='badge nonaktif'>Nonaktif</span>";
                                        } else {

                                            echo "<span class='badge draft'>" . htmlspecialchars($rawStatus ?: '-') . "</span>";
                                        }
                                        ?>
                                    </td>

                                    <td class="action-cell">
                                        <button class="action-toggle" type="button">
                                            <i class="fa-solid fa-ellipsis-vertical"></i>
                                        </button>

                                        <div class="action-menu" id="menu-<?= $id ?>">
                                            <a href="edit_pengumuman.php?id=<?= $id ?>" class="action-item">
                                                <i class="fa-solid fa-pen"></i>
                                                <span>Edit</span>
                                            </a>

                                            <button type="button" class="action-item action-delete"
                                                onclick="return confirmDelete(<?= $id ?>);">
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
                <div class="delete-selection" style="cursor:pointer">
                    <button class="btn-delete-bulk" type="button" onclick="return confirmBulkDelete();" style="background: transparent; color: #1E5AA8;">
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

    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const toggle = document.querySelector(".filter-toggle");
            const menu = document.querySelector(".filter-menu");

            if (toggle && menu) {
                toggle.addEventListener("click", function(e) {
                    e.stopPropagation();
                    menu.classList.toggle("open");
                });

                document.addEventListener("click", function(e) {
                    if (!menu.contains(e.target) && !toggle.contains(e.target)) {
                        menu.classList.remove("open");
                    }
                });
            }
        });

        document.getElementById('search-input')?.addEventListener('keydown', function(e) {
            if (e.key !== 'Enter') return;
            const url = new URL(window.location.href);
            url.searchParams.set('q', this.value.trim());
            url.searchParams.set('page', '1');
            window.location.href = url.toString();
        });

        const sortBtn = document.getElementById('sort-btn');
        const sortMenu = document.getElementById('sort-menu');
        sortBtn?.addEventListener('click', (e) => {
            e.stopPropagation();
            sortMenu?.classList.toggle('hidden');
        });
        sortMenu?.querySelectorAll('[data-sort]')?.forEach(el => {
            el.addEventListener('click', () => {
                const s = el.dataset.sort || 'default';
                const url = new URL(window.location.href);
                url.searchParams.set('sort', s);
                url.searchParams.set('page', '1');
                window.location.href = url.toString();
            });
        });

        function closeAllMenus() {
            document.querySelectorAll('.action-menu').forEach(m => m.classList.remove('open'));
        }
        document.querySelectorAll('.action-toggle').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const cell = btn.closest('.action-cell');
                const menu = cell?.querySelector('.action-menu');
                closeAllMenus();
                menu?.classList.toggle('open');
            });
        });
        document.addEventListener('click', () => {
            closeAllMenus();
            sortMenu?.classList.add('hidden');
        });

        function confirmDelete(id) {
            if (!confirm('Hapus pengumuman ini?')) return false;
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'delete_pengumuman.php';
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'id_pengumuman';
            input.value = id;
            form.appendChild(input);
            document.body.appendChild(form);
            form.submit();
            return true;
        }

        function confirmBulkDelete() {
            const checked = document.querySelectorAll('.row-check:checked');
            if (checked.length === 0) {
                alert('Pilih minimal 1 data dulu.');
                return false;
            }
            if (!confirm(`Hapus ${checked.length} data yang dipilih?`)) return false;
            document.getElementById('bulkDeleteForm').submit();
            return true;
        }

        document.getElementById('checkAll')?.addEventListener('change', function() {
            document.querySelectorAll('.row-check').forEach(cb => cb.checked = this.checked);
        });
    </script>
</body>

</html>