<?php
require_once __DIR__ . '/../Admin/Koneksi/KoneksiSasa.php'; // sesuaikan path kalau beda

function esc($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}
function parse_authors($raw): string
{
    if ($raw === null || $raw === '') return '';
    if (is_array($raw)) $data = $raw;
    else {
        $data = json_decode((string)$raw, true);
        if (!is_array($data)) return '';
    }

    $names = [];

    // jika object tunggal
    if (isset($data['name']) || isset($data['nama'])) {
        $n = $data['name'] ?? $data['nama'];
        if (is_string($n) && trim($n) !== '') $names[] = trim($n);
        return implode(', ', $names);
    }

    // jika array
    foreach ($data as $item) {
        if (is_string($item)) {
            $n = trim($item);
            if ($n !== '') $names[] = $n;
        } elseif (is_array($item)) {
            $n = $item['name'] ?? ($item['nama'] ?? ($item['username'] ?? ''));
            $n = is_string($n) ? trim($n) : '';
            if ($n !== '') $names[] = $n;
        }
    }

    return implode(', ', array_unique($names));
}

/** jenis bisa satu tag atau "tag1, tag2" */
function parse_tags($jenis): array
{
    $s = trim((string)$jenis);
    if ($s === '') return [];
    $parts = preg_split('/\s*,\s*/', $s);
    $out = [];
    foreach ($parts as $p) {
        $p = trim($p);
        if ($p !== '') $out[] = $p;
    }
    return array_values(array_unique($out));
}

$q      = trim($_GET['q'] ?? '');
$tag    = trim($_GET['tag'] ?? '');   
$year   = trim($_GET['year'] ?? '');  
$page   = max(1, (int)($_GET['page'] ?? 1));
$limit  = 6;
$offset = ($page - 1) * $limit;

$where  = ["p.status = TRUE"]; // hanya yang publish/aktif
$params = [];
$i = 1;

if ($q !== '') {
    $where[] = "(p.judul ILIKE $" . $i . " OR p.abstrak ILIKE $" . $i . ")";
    $params[] = '%' . $q . '%';
    $i++;
}

if ($tag !== '') {
    
    $where[] = "p.jenis ILIKE $" . $i;
    $params[] = '%' . $tag . '%';
    $i++;
}

if ($year !== '' && ctype_digit($year)) {
    $where[] = "EXTRACT(YEAR FROM p.tanggal_terbit) = $" . $i;
    $params[] = (int)$year;
    $i++;
}

$whereSql = "WHERE " . implode(" AND ", $where);


$sqlCount = "SELECT COUNT(*) AS total FROM publikasi p $whereSql";
$resCount = qparams($sqlCount, $params);
$total = (int)(pg_fetch_assoc($resCount)['total'] ?? 0);
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
    p.id_publikasi,
    p.judul,
    p.jenis,
    p.abstrak,
    p.link,
    p.tanggal_terbit,
    p.author,
    p.id_riset,
    a.nama AS uploader_nama
  FROM publikasi p
  LEFT JOIN anggotalab a ON a.id_anggota = p.uploaded_by
  $whereSql
  ORDER BY p.tanggal_terbit DESC NULLS LAST, p.id_publikasi DESC
  LIMIT $" . ($i) . " OFFSET $" . ($i + 1) . "
";
$res = qparams($sqlData, $paramsData);
$rows = pg_fetch_all($res) ?: [];


$resTags = q("SELECT DISTINCT jenis FROM publikasi WHERE status = TRUE AND jenis IS NOT NULL AND trim(jenis) <> '' ORDER BY jenis ASC");
$tags = [];
while ($r = pg_fetch_assoc($resTags)) {
    foreach (parse_tags($r['jenis'] ?? '') as $t) $tags[$t] = true;
}
$tags = array_keys($tags);
sort($tags);


$resYears = q("SELECT DISTINCT EXTRACT(YEAR FROM tanggal_terbit)::int AS y
              FROM publikasi
              WHERE status = TRUE AND tanggal_terbit IS NOT NULL
              ORDER BY y DESC");
$years = [];
while ($r = pg_fetch_assoc($resYears)) $years[] = (int)$r['y'];

function build_url($overrides = [])
{
    $p = $_GET;
    foreach ($overrides as $k => $v) {
        if ($v === '' || $v === null) unset($p[$k]);
        else $p[$k] = $v;
    }
    return '?' . http_build_query($p);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Publikasi</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../Assets/Css/Publikasi.css">
    <link rel="icon" type="images/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />
</head>

<body>
    <div id="header"></div>

    <div class="heading">
        <div class="heading-content">
            <h1>Publikasi</h1>
            <p>Kumpulan penelitian dan inovasi terkini dari tim Laboratorium Business Analytics</p>
        </div>
    </div>

    <div class="content-container">
        <div class="content-card">

            
            <form class="filters" method="GET" action="publikasi.php" id="filtersForm">
                <div class="search-box">
                    <svg class="search-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8" />
                        <path d="m21 21-4.35-4.35" />
                    </svg>
                    <input type="text" name="q" id="searchInput" placeholder="Cari judul publikasi..." value="<?= esc($q) ?>">
                </div>

                <select name="tag" id="tagSelect">
                    <option value="">No Tag</option>
                    <?php foreach ($tags as $t): ?>
                        <option value="<?= esc($t) ?>" <?= ($tag === $t ? 'selected' : '') ?>><?= esc($t) ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="year" id="yearSelect">
                    <option value="">Semua Tahun</option>
                    <?php foreach ($years as $y): ?>
                        <option value="<?= (int)$y ?>" <?= ($year == (string)$y ? 'selected' : '') ?>><?= (int)$y ?></option>
                    <?php endforeach; ?>
                </select>
            </form>

            
            <div class="publications">
                <?php if (empty($rows)): ?>
                    <div class="empty-state">
                        <div class="empty-title">Tidak ada publikasi yang ditemukan</div>
                        <div class="empty-sub">Coba ubah kata kunci atau filter.</div>
                    </div>
                <?php else: ?>
                    <?php foreach ($rows as $p): ?>
                        <?php
                        $judul = $p['judul'] ?? '';
                        $authorsText = parse_authors($p['author'] ?? '');
                        $jenisTags = parse_tags($p['jenis'] ?? '');
                        $link = trim((string)($p['link'] ?? ''));
                        if ($link !== '' && !preg_match('~^https?://~i', $link)) $link = 'https://' . $link;

                        $yearPub = '';
                        if (!empty($p['tanggal_terbit'])) $yearPub = date('Y', strtotime($p['tanggal_terbit']));

                        $venue = !empty($p['id_riset']) ? ('Riset #' . (int)$p['id_riset']) : 'Laboratorium Business Analytics';
                        ?>
                        <div class="pub-item">
                            <h3 class="pub-title"><?= esc($judul) ?></h3>

                            <?php if ($authorsText !== ''): ?>
                                <div class="pub-authors"><?= esc($authorsText) ?></div>
                            <?php endif; ?>

                            <div class="pub-meta">
                                <strong>Published:</strong> <?= esc($yearPub ?: '-') ?>
                                <strong> | Venue:</strong> <?= esc($venue) ?>
                                <?php if ($link !== ''): ?>
                                    <strong> |</strong> <a href="<?= esc($link) ?>" target="_blank" rel="noopener">Link</a>
                                <?php endif; ?>
                            </div>

                            <?php if (!empty($p['abstrak'])): ?>
                                <div class="pub-abstract"><?= esc($p['abstrak']) ?></div>
                            <?php endif; ?>

                            <?php if (!empty($jenisTags)): ?>
                                <div class="tags">
                                    <?php foreach ($jenisTags as $t): ?>
                                        <span class="tag"><?= esc($t) ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            
            <?php if ($totalPages > 1): ?>
                <div class="pagination">
                    <a class="page-btn <?= ($page <= 1 ? 'disabled' : '') ?>"
                        href="<?= ($page <= 1 ? '#' : esc(build_url(['page' => $page - 1]))) ?>">&lt;</a>

                    <div class="page-info">Halaman <?= (int)$page ?> dari <?= (int)$totalPages ?></div>

                    <a class="page-btn <?= ($page >= $totalPages ? 'disabled' : '') ?>"
                        href="<?= ($page >= $totalPages ? '#' : esc(build_url(['page' => $page + 1]))) ?>">&gt;</a>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <script src="../Assets/Javascript/HeaderFooter.js"></script>
    <script src="../Assets/Javascript/Publikasi.js"></script>
</body>

</html>