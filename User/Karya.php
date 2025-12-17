<?php

require_once __DIR__ . '/../Admin/Koneksi/KoneksiSasa.php'; 


function esc($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}


$q     = trim($_GET['q'] ?? '');
$year  = trim($_GET['year'] ?? '');  // contoh: 2025
$page  = max(1, (int)($_GET['page'] ?? 1));
$limit = 6;
$offset = ($page - 1) * $limit;


$where = [];
$params = [];
$i = 1;

if ($q !== '') {
    $where[] = "(k.judul ILIKE $" . $i . " OR k.deskripsi ILIKE $" . $i . ")";
    $params[] = '%' . $q . '%';
    $i++;
}

if ($year !== '' && ctype_digit($year)) {
    $where[] = "EXTRACT(YEAR FROM k.uploaded_at) = $" . $i;
    $params[] = (int)$year;
    $i++;
}

$whereSql = $where ? ('WHERE ' . implode(' AND ', $where)) : '';


$sqlCount = "SELECT COUNT(*) AS total FROM karya k $whereSql";
$resCount = qparams($sqlCount, $params);
$rowCount = pg_fetch_assoc($resCount);
$total = (int)($rowCount['total'] ?? 0);
$totalPages = max(1, (int)ceil($total / $limit));
if ($page > $totalPages) {
    $page = $totalPages;
    $offset = ($page - 1) * $limit;
}


$paramsData = $params;
$paramsData[] = $limit;
$paramsData[] = $offset;

$sqlData = "
  SELECT
    k.id_karya,
    k.judul,
    k.deskripsi,
    k.link,
    k.uploaded_at,
    k.uploaded_by,
    a.nama AS uploader_nama
  FROM karya k
  LEFT JOIN anggotalab a ON a.id_anggota = k.uploaded_by
  $whereSql
  ORDER BY k.uploaded_at DESC, k.id_karya DESC
  LIMIT $" . ($i) . " OFFSET $" . ($i + 1) . "
";
$res = qparams($sqlData, $paramsData);
$karya = pg_fetch_all($res) ?: [];


$resYears = q("SELECT DISTINCT EXTRACT(YEAR FROM uploaded_at)::int AS y FROM karya ORDER BY y DESC");
$years = [];
while ($r = pg_fetch_assoc($resYears)) $years[] = (int)$r['y'];

function build_url($overrides = [])
{
    $params = $_GET;
    foreach ($overrides as $k => $v) {
        if ($v === '' || $v === null) unset($params[$k]);
        else $params[$k] = $v;
    }
    return '?' . http_build_query($params);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Karya</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="images/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />

    <link rel="stylesheet" href="../Assets/Css/Karya.css">
</head>

<body>
    <div id="header"></div>

    <div class="heading">
        <div class="heading-content">
            <h1>Karya</h1>
            <p>Kumpulan karya & hasil inovasi dari tim Laboratorium Business Analytics</p>
        </div>
    </div>

    <div class="content-container">
        <div class="content-card">

            <form class="filters" method="GET" action="karya.php" id="filtersForm">
                <div class="search-box">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" name="q" id="searchInput" placeholder="Cari judul / deskripsi karya..." value="<?= esc($q) ?>">
                </div>

                <select name="year" id="yearSelect">
                    <option value="">Semua Tahun</option>
                    <?php foreach ($years as $y): ?>
                        <option value="<?= (int)$y ?>" <?= ($year == (string)$y ? 'selected' : '') ?>><?= (int)$y ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" class="btn-filter">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    Terapkan
                </button>
            </form>

            
            <div class="publications" id="karyaList">
                <?php if (empty($karya)): ?>
                    <div class="empty-state">
                        <div class="empty-title">Tidak ada karya yang ditemukan</div>
                        <div class="empty-sub">Coba ubah kata kunci atau filter tahun.</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($karya as $k): ?>
                        <?php
                        $judul = $k['judul'] ?? '';
                        $desc  = $k['deskripsi'] ?? '';
                        $link  = trim((string)($k['link'] ?? ''));
                        $tahun = '';
                        if (!empty($k['uploaded_at'])) {
                            $tahun = date('Y', strtotime($k['uploaded_at']));
                        }
                        $uploader = $k['uploader_nama'] ?: ('ID: ' . ($k['uploaded_by'] ?? '-'));
                        ?>
                        <div class="pub-item karya-item"
                            data-title="<?= esc($judul) ?>"
                            data-desc="<?= esc($desc) ?>"
                            data-year="<?= esc($tahun) ?>">
                            <div class="karya-top">
                                <h3 class="pub-title"><?= esc($judul) ?></h3>
                                <?php if ($tahun !== ''): ?>
                                    <span class="karya-year"><?= esc($tahun) ?></span>
                                <?php endif; ?>
                            </div>

                            <?php if ($desc !== ''): ?>
                                <div class="karya-desc"><?= nl2br(esc($desc)) ?></div>
                            <?php endif; ?>

                            <div class="pub-meta">
                                <strong>Uploader:</strong> <?= esc($uploader) ?>
                                <?php if (!empty($k['uploaded_at'])): ?>
                                    | <strong>Uploaded:</strong> <?= esc(date('d M Y', strtotime($k['uploaded_at']))) ?>
                                <?php endif; ?>
                                <?php if ($link !== ''): ?>
                                    | <a href="<?= esc($link) ?>" target="_blank" rel="noopener">Buka Link</a>
                                <?php endif; ?>
                            </div>

                            <?php if ($link !== ''): ?>
                                <div class="karya-actions">
                                    <a class="btn-primary" href="DetailKarya.php?id=<?= (int)$k['id_karya'] ?>">
                                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Lihat Karya
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <a class="page-btn <?= ($page <= 1 ? 'disabled' : '') ?>" href="<?= ($page <= 1 ? '#' : esc(build_url(['page' => $page - 1]))) ?>">&lt;</a>

                    <div class="page-info">
                        Halaman <?= (int)$page ?> dari <?= (int)$totalPages ?>
                    </div>

                    <a class="page-btn <?= ($page >= $totalPages ? 'disabled' : '') ?>" href="<?= ($page >= $totalPages ? '#' : esc(build_url(['page' => $page + 1]))) ?>">&gt;</a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <div id="footer"></div>
    <script src="../Assets/Javascript/HeaderFooter.js"></script>
    <script src="../Assets/Javascript/Karya.js"></script>
</body>

</html>