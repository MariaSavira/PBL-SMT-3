<?php
require_once __DIR__ . '/../Admin/Koneksi/KoneksiSasa.php'; 

function esc($s)
{
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    http_response_code(400);
    exit('ID karya tidak valid.');
}

$sql = "
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
  WHERE k.id_karya = $1
  LIMIT 1
";
$res = qparams($sql, [$id]);
$row = pg_fetch_assoc($res);

if (!$row) {
    http_response_code(404);
    exit('Karya tidak ditemukan.');
}

$judul = $row['judul'] ?? '';
$desc  = $row['deskripsi'] ?? '';
$link  = trim((string)($row['link'] ?? ''));
$uploader = $row['uploader_nama'] ?: ('ID: ' . ($row['uploaded_by'] ?? '-'));
$tgl = !empty($row['uploaded_at']) ? date('d M Y', strtotime($row['uploaded_at'])) : '-';

if ($link !== '' && !preg_match('~^https?://~i', $link)) {
    $link = 'https://' . $link;
}

$previewUrl = $link;


// Google Drive
if (str_contains($link, 'drive.google.com')) {
    $previewUrl = preg_replace('~/view(\?.*)?$~', '/preview', $link);
}

// YouTube
elseif (
    preg_match('~youtu\.be/([A-Za-z0-9_-]{6,})~', $link, $m) ||
    preg_match('~v=([A-Za-z0-9_-]{6,})~', $link, $m)
) {
    $previewUrl = 'https://www.youtube.com/embed/' . $m[1];
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?= esc($judul ?: 'Detail Karya') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="icon" type="images/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />

    <link rel="stylesheet" href="../Assets/Css/DetailKarya.css">
</head>

<body>
    <div id="header"></div>

    <div class="heading">
        <h1>Detail Karya</h1>
        <p>Informasi lengkap karya dan akses cepat ke tautan sumbernya</p>
    </div>

    <div class="content-container">
        <div class="content-card">

            <div class="topbar">
                <a class="back" href="karya.php"><i class="fa-solid fa-arrow-left"></i> Kembali</a>
                <?php if ($link !== ''): ?>
                    <a class="open" href="<?= esc($link) ?>" target="_blank" rel="noopener">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i> Buka Link
                    </a>
                <?php endif; ?>
            </div>

            <div class="detail-grid">
                
                <section class="info">
                    <h2 class="title"><?= esc($judul) ?></h2>

                    <div class="meta">
                        <div class="meta-item">
                            <span class="label">Uploaded</span>
                            <span class="value"><?= esc($tgl) ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="label">Uploader</span>
                            <span class="value"><?= esc($uploader) ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="label">ID Karya</span>
                            <span class="value">#<?= (int)$row['id_karya'] ?></span>
                        </div>
                    </div>

                    <?php if ($desc !== ''): ?>
                        <div class="desc">
                            <?= nl2br(esc($desc)) ?>
                        </div>
                    <?php else: ?>
                        <div class="desc empty">
                            Belum ada deskripsi untuk karya ini.
                        </div>
                    <?php endif; ?>

                    <?php if ($link !== ''): ?>
                        <div class="linkbox">
                            <div class="linkrow">
                                <i class="fa-solid fa-link"></i>
                                <div class="linktext">
                                    <div class="linktitle">Tautan Karya</div>
                                    <div class="linkurl" id="linkText"><?= esc($link) ?></div>
                                </div>
                            </div>
                            <button class="copy" id="copyBtn" type="button">
                                <i class="fa-regular fa-copy"></i> Salin
                            </button>
                        </div>
                    <?php endif; ?>
                </section>

                
                <section class="preview">
                    <div class="preview-head">
                        <h3>Preview</h3>
                        <span class="badge">Auto</span>
                    </div>

                    <?php if ($link === ''): ?>
                        <div class="preview-empty">
                            Tidak ada link untuk dipreview.
                        </div>
                    <?php else: ?>
                        <div class="preview-frame">
                            <iframe
                                id="previewFrame"
                                src="<?= esc($previewUrl) ?>"
                                title="Preview Karya"
                                loading="lazy"
                                referrerpolicy="no-referrer"></iframe>
                        </div>

                        <div class="preview-note" id="previewNote">
                            Kalau preview kosong/blocked, klik <strong>Buka Link</strong> untuk melihat langsung.
                        </div>
                    <?php endif; ?>
                </section>
            </div>

        </div>
    </div>

    <div id="footer"></div>

    <script src="../Assets/Javascript/HeaderFooter.js"></script>
    <script>
        // copy link
        const copyBtn = document.getElementById('copyBtn');
        const linkText = document.getElementById('linkText');
        if (copyBtn && linkText) {
            copyBtn.addEventListener('click', async () => {
                try {
                    await navigator.clipboard.writeText(linkText.textContent.trim());
                    copyBtn.innerHTML = '<i class="fa-solid fa-check"></i> Tersalin';
                    setTimeout(() => copyBtn.innerHTML = '<i class="fa-regular fa-copy"></i> Salin', 1400);
                } catch (e) {
                    alert('Gagal menyalin link.');
                }
            });
        }

        const frame = document.getElementById('previewFrame');
        const note = document.getElementById('previewNote');
        if (frame && note) {
            let loaded = false;
            frame.addEventListener('load', () => {
                loaded = true;
            });
            setTimeout(() => {
                
                if (!loaded) note.innerHTML = 'Preview mungkin diblokir oleh website. Klik <strong>Buka Link</strong> untuk melihat langsung.';
            }, 2500);
        }
    </script>
</body>

</html>