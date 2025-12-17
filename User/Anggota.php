<?php
require_once __DIR__ . '../../Admin/Cek_Autentikasi.php';
require_once __DIR__ . '../../Admin/Koneksi/KoneksiSasa.php';

function foto_url(?string $foto): string
{
    $foto = trim((string)$foto);

    if ($foto === '') {
        return "../Assets/Image/AnggotaLab/No-Picture.jpg";
    }

    return "../Assets/Image/AnggotaLab/" . $foto;
}

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

$sql = "
    SELECT 
        mv.id_anggota,
        mv.nama,
        mv.keahlian,
        mv.jabatan,
        mv.foto,
        mv.status,
        a.deskripsi,
        a.link
    FROM public.mv_anggota_keahlian mv
    JOIN public.anggotalab a ON a.id_anggota = mv.id_anggota
    WHERE mv.status = TRUE
    ORDER BY
      CASE WHEN lower(mv.jabatan) LIKE '%kepala%' THEN 0 ELSE 1 END,
      mv.nama ASC
";


$res = q($sql);
$anggota = [];

while ($row = pg_fetch_assoc($res)) {
    $id = (int)$row['id_anggota'];

    if (!isset($anggota[$id])) {
        $anggota[$id] = [
            'id_anggota' => $id,
            'nama'       => $row['nama'] ?? '',
            'jabatan'    => $row['jabatan'] ?? '',
            'foto'       => $row['foto'] ?? '',
            'status'     => (bool)$row['status'],
            'deskripsi'  => $row['deskripsi'] ?? '',
            'link'       => $row['link'] ?? null, // jsonb
            'keahlian'   => [],
        ];
    }

    $raw = $row['keahlian'] ?? '';
    foreach (parse_keahlian($raw) as $tag) {
        if (!in_array($tag, $anggota[$id]['keahlian'], true)) {
            $anggota[$id]['keahlian'][] = $tag;
        }
    }
}

$kepalaLab = null;
$peneliti  = [];

foreach ($anggota as $a) {
    if ($kepalaLab === null && stripos($a['jabatan'], 'kepala') !== false) {
        $kepalaLab = $a;
    } else {
        $peneliti[] = $a;
    }
}

if ($kepalaLab === null && count($peneliti) > 0) {
    $kepalaLab = array_shift($peneliti);
}
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anggota Laboratorium</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="icon" type="images/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />

    <link rel="stylesheet" href="../Assets/Css/AnggotaIni.css">
    <link rel="stylesheet" href="../Assets/Css/Index.css">
</head>

<body>
    <div id="header"></div>

    <div class="heading">
        <h1>Anggota Laboratorium</h1>
        <p>Anggota Laboratorium Business Analytics merupakan dosen dan peneliti
            yang memiliki keahlian di bidang analisis bisnis<br> berbasis data
            dan berperan aktif dalam kegiatan penelitian, pengabdian, serta
            pengembangan ilmu pengetahuan<br> di lingkungan akademik.</p>
    </div>

    <div class="anggota-wrapper">

        
        <?php if ($kepalaLab): ?>
            <div class="kepala-lab-card kepala-card-clickable" data-href="DetailAnggota.php?id=<?= (int)$kepalaLab['id_anggota'] ?>">

                <div class="kepala-lab-photo">
                    <img src="<?= htmlspecialchars(foto_url($kepalaLab['foto'])) ?>" alt="Kepala Laboratorium">
                    <div class="kepala-lab-badge">Kepala Laboratorium</div>
                </div>

                <div class="kepala-lab-info">
                    <h2 class="kepala-lab-name"><?= htmlspecialchars($kepalaLab['nama']) ?></h2>

                    <p class="kepala-lab-desc">
                        <?= htmlspecialchars(trim($kepalaLab['deskripsi'] ?? '') ?: 'Anggota Laboratorium dengan fokus pada pengembangan riset dan kegiatan akademik.') ?>
                    </p>

                    <hr>

                    <div class="kepala-lab-socmed">
                        <a href="#" target="_blank" rel="noopener" aria-label="Google Scholar">
                            <i class="fa-brands fa-google-scholar"></i> </a>

                        <a href="#" target="_blank" rel="noopener" aria-label="LinkedIn">
                            <i class="fa-brands fa-linkedin-in"></i> </a>

                        <a href="#" target="_blank" rel="noopener" aria-label="ResearchGate">
                            <i class="fa-brands fa-researchgate"></i> </a>

                        <a href="#" target="_blank" rel="noopener" aria-label="Website">
                            <i class="fa-solid fa-globe"></i> </a>
                    </div>

                    <h3 class="kepala-lab-subtitle">Bidang Keahlian</h3>
                    <div class="kepala-lab-tags">
                        <?php foreach (($kepalaLab['keahlian'] ?? []) as $tag): ?>
                            <span class="anggota-tag"><?= htmlspecialchars($tag) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                
                <a class="card-overlay-link"
                    href="DetailAnggota.php?id=<?= (int)$kepalaLab['id_anggota'] ?>"
                    aria-label="Buka detail kepala laboratorium"></a>

            </div>
        <?php endif; ?>


        
        <div class="section-title anggota-section-title">Para Peneliti</div>

        <div class="peneliti-grid">
            <?php foreach ($peneliti as $p): ?>
                <div class="peneliti-card">
                    <a href="DetailAnggota.php?id=<?= (int)$p['id_anggota'] ?>">
                        <div class="peneliti-photo">
                            <img src="<?= htmlspecialchars(foto_url($p['foto'])) ?>" alt="<?= htmlspecialchars($p['nama']) ?>">
                        </div>
                        <div class="peneliti-body">
                            <h4 class="peneliti-name"><?= htmlspecialchars($p['nama']) ?></h4>
                            <div class="peneliti-tags">
                                <?php if (!empty($p['keahlian'])): ?>
                                    <?php foreach ($p['keahlian'] as $tag): ?>
                                        <span class="anggota-tag"><?= htmlspecialchars($tag) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <span class="anggota-tag">-</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <div id="footer"></div>
    <script>
        document.addEventListener("click", (e) => {
            const card = e.target.closest(".kepala-card-clickable");
            if (!card) return;

            if (e.target.closest(".kepala-lab-socmed a")) return;

            const href = card.getAttribute("data-href");
            if (href) window.location.href = href;
        });
    </script>

    <script src="../Assets/Javascript/HeaderFooter.js"></script>
</body>

</html>