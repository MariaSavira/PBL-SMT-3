<?php
require_once __DIR__ . '../../Admin/Koneksi/KoneksiSasa.php'; 

function esc($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function foto_url(?string $foto): string
{
    $foto = trim((string)$foto);
    if ($foto === '') return "../Assets/Image/AnggotaLab/default.png";

    //bisa file kosong, url eksternal, maupun file lokal
    if (preg_match('~^https?://~i', $foto) || str_contains($foto, '/') || str_contains($foto, '\\')) {
        return $foto;
    }

    return "../Assets/Image/AnggotaLab/" . $foto;
}

function decode_links($raw): array
{
    if ($raw === null || $raw === '') return [];

    $arr = is_array($raw) ? $raw : json_decode((string)$raw, true);
    if (!is_array($arr)) return [];

    $out = [];
    foreach ($arr as $k => $v) {
        if (is_string($v)) {
            $v = trim($v);
            if ($v !== '') $out[$k] = $v;
            continue;
        }

        if (is_array($v)) {
            if (isset($v['url']) && is_string($v['url'])) {
                $u = trim($v['url']);
                if ($u !== '') $out[$k] = $u;
                continue;
            }

            foreach ($v as $vv) {
                if (is_string($vv) && trim($vv) !== '') {
                    $out[$k] = trim($vv);
                    break;
                }
            }
        }
    }

    return $out;
}

function social_label(string $key): string
{
    $k = strtolower($key);
    $labels = [
        'scholar' => 'Google Scholar',
        'google_scholar' => 'Google Scholar',
        'googlescholar' => 'Google Scholar',
        'linkedin' => 'LinkedIn',
        'researchgate' => 'ResearchGate',
        'facebook' => 'Facebook',
        'instagram' => 'Instagram',
        'youtube' => 'YouTube',
        'github' => 'GitHub',
        'twitter' => 'X',
        'x' => 'X',
        'email' => 'Email',
        'website' => 'Website',
        'web' => 'Website',
    ];
    return $labels[$k] ?? ucfirst(str_replace('_', ' ', $key));
}

function detect_platform(string $key, string $url): string
{
    $k = strtolower(trim($key));
    $u = strtolower(trim($url));

    $known = ['linkedin', 'facebook', 'researchgate', 'instagram', 'youtube', 'github', 'scholar', 'google_scholar', 'googlescholar', 'x', 'twitter', 'email', 'mailto', 'website', 'web'];
    if (in_array($k, $known, true)) return $k;

    if (str_contains($u, 'scholar.google')) return 'scholar';
    if (str_contains($u, 'linkedin.com')) return 'linkedin';
    if (str_contains($u, 'researchgate.net')) return 'researchgate';
    if (str_contains($u, 'facebook.com') || str_contains($u, 'fb.com')) return 'facebook';
    if (str_contains($u, 'instagram.com')) return 'instagram';
    if (str_contains($u, 'youtube.com') || str_contains($u, 'youtu.be')) return 'youtube';
    if (str_contains($u, 'github.com')) return 'github';
    if (str_contains($u, 'twitter.com') || str_contains($u, 'x.com')) return 'x';
    if (str_contains($u, 'mailto:')) return 'mailto';

    return 'website';
}

function render_social_icon(string $key): string
{
    $k = strtolower($key);

    $map = [
        'scholar' => 'fa-brands fa-google-scholar',
        'linkedin'     => 'fa-brands fa-linkedin-in',
        'researchgate' => 'fa-brands fa-researchgate',
        'facebook'     => 'fa-brands fa-facebook-f',
        'instagram'    => 'fa-brands fa-instagram',
        'youtube'      => 'fa-brands fa-youtube',
        'github'       => 'fa-brands fa-github',
        'twitter'      => 'fa-brands fa-x-twitter',
        'x'            => 'fa-brands fa-x-twitter',
        'email'        => 'fa-solid fa-envelope',
        'mailto'       => 'fa-solid fa-envelope',
        'website'      => 'fa-solid fa-globe',
        'web'          => 'fa-solid fa-globe',
    ];
    $cls = $map[$k] ?? 'fa-solid fa-link';
    return '<i class="' . esc($cls) . '"></i>';
}

/** MV kamu kadang ngasih {"A","B"} */
function parse_keahlian($raw): array
{
    $raw = trim((string)$raw);
    if ($raw === '') return [];

    if ($raw[0] === '{' && substr($raw, -1) === '}') {
        $inside = trim($raw, "{} \t\n\r\0\x0B");
        if ($inside === '') return [];
        $parts = explode(',', $inside);
        $out = [];
        foreach ($parts as $p) {
            $p = trim($p);
            $p = trim($p, "\"' ");
            if ($p !== '') $out[] = $p;
        }
        return $out;
    }
    return [$raw];
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    exit('ID anggota tidak valid.');
}

$sqlA = "
  SELECT id_anggota, nama, jabatan, deskripsi, foto, link, status
  FROM anggotalab
  WHERE id_anggota = $1
  LIMIT 1
";
$resA = qparams($sqlA, [$id]);
$anggota = pg_fetch_assoc($resA);

if (!$anggota) {
    http_response_code(404);
    exit('Anggota tidak ditemukan.');
}

$nama = $anggota['nama'] ?? '';
$jabatan = $anggota['jabatan'] ?? '';
$deskripsi = $anggota['deskripsi'] ?? '';
$foto = foto_url($anggota['foto'] ?? '');
$links = decode_links($anggota['link'] ?? null);

$sqlK = "
  SELECT keahlian
  FROM mv_anggota_keahlian
  WHERE id_anggota = $1
";
$resK = qparams($sqlK, [$id]);
$keahlian = [];
while ($r = pg_fetch_assoc($resK)) {
    $raw = $r['keahlian'] ?? '';
    foreach (parse_keahlian($raw) as $tag) {
        if ($tag !== '' && !in_array($tag, $keahlian, true)) $keahlian[] = $tag;
    }
}

$isKepala = (stripos($jabatan, 'kepala') !== false);
$badgeText = $isKepala ? 'Kepala Laboratorium' : 'Anggota Laboratorium';
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= esc($nama ?: 'Detail Anggota') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="icon" type="images/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />    
    <link rel="stylesheet" href="../Assets/Css/AnggotaIni.css">
    <link rel="stylesheet" href="../Assets/Css/Index.css">
    <link rel="stylesheet" href="../Assets/Css/DetailAnggota.css">
</head>

<body>
    <div id="header"></div>

    <div class="heading">
        <h1>Detail Anggota</h1>
        <p>Informasi lengkap anggota Laboratorium Business Analytics</p>
    </div>

    <div class="anggota-wrapper">

        <div class="kepala-lab-card detail-card">
            <div class="kepala-lab-photo">
                <img src="<?= esc($foto) ?>" alt="<?= esc($nama) ?>"
                    onerror="this.src='../Assets/Image/AnggotaLab/default.png'">

                <div class="kepala-lab-badge"><?= esc($badgeText) ?></div>
            </div>

            <div class="kepala-lab-info">
                <h2 class="kepala-lab-name"><?= esc($nama) ?></h2>

                <?php if ($jabatan !== ''): ?>
                    <div class="detail-role"><?= esc($jabatan) ?></div>
                <?php endif; ?>

                <p class="kepala-lab-desc">
                    <?= $deskripsi !== '' ? nl2br(esc($deskripsi)) : 'Belum ada deskripsi untuk anggota ini.' ?>
                </p>

                <hr>

                
                <div class="kepala-lab-socmed">
                    <?php
                    foreach ($links as $key => $url):
                        $url = trim((string)$url);
                        if ($url === '') continue;

                        if (in_array(strtolower($key), ['email', 'mailto'], true) && stripos($url, 'mailto:') !== 0) {
                            $url = 'mailto:' . $url;
                        }
                        if (!preg_match('~^(https?://|mailto:)~i', $url)) {
                            $url = 'https://' . $url;
                        }
                    ?>
                        <?php
                        $platform = detect_platform($key, $url);
                        ?>
                        <a href="<?= esc($url) ?>" target="_blank" rel="noopener" aria-label="<?= esc(social_label($platform)) ?>">
                            <?= render_social_icon($platform) ?>
                        </a>

                    <?php endforeach; ?>

                    <?php if (empty($links)): ?>
                        <span class="socmed-empty">Belum ada tautan sosial.</span>
                    <?php endif; ?>
                </div>

                <h3 class="kepala-lab-subtitle">Bidang Keahlian</h3>
                <div class="kepala-lab-tags">
                    <?php if (!empty($keahlian)): ?>
                        <?php foreach ($keahlian as $t): ?>
                            <span class="anggota-tag"><?= esc($t) ?></span>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <span class="tag-empty">Belum ada bidang keahlian.</span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="footer"></div>
    <script src="../Assets/Javascript/HeaderFooter.js"></script>
</body>

</html>