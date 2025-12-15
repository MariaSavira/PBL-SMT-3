<?php
require_once __DIR__ . '../../Admin/Cek_Autentikasi.php';
require_once __DIR__ . '../../Admin/Koneksi/KoneksiSasa.php';
// default foto kalau kosong
function foto_url(?string $foto): string {
    $foto = trim((string)$foto);
    if ($foto === '') {
        return '../Assets/Image/AnggotaLab/profile1.png';
    }
    // kalau kamu simpan nama file saja di DB, arahkan ke folder ini:
    // return "../Assets/Image/AnggotaLab/" . $foto;

    // kalau di DB sudah path relatif/URL, langsung pakai:
    return $foto;
}

// ============ AMBIL DATA DARI MATERIALIZED VIEW ============
// hanya yang aktif
$sql = "
    SELECT id_anggota, nama, keahlian, jabatan, foto, status
    FROM public.mv_anggota_keahlian
    WHERE status = TRUE
    ORDER BY
      CASE WHEN lower(jabatan) LIKE '%kepala%' THEN 0 ELSE 1 END,
      nama ASC
";

$res = q($sql); // kalau kamu gak punya q(), bilang ya, aku sesuaikan
$anggota = [];

// grouping per id_anggota
while ($row = pg_fetch_assoc($res)) {
    $id = (int)$row['id_anggota'];

    if (!isset($anggota[$id])) {
        $anggota[$id] = [
            'id_anggota' => $id,
            'nama'       => $row['nama'] ?? '',
            'jabatan'    => $row['jabatan'] ?? '',
            'foto'       => $row['foto'] ?? '',
            'status'     => (bool)$row['status'],
            'keahlian'   => [],
        ];
    }

    $k = trim((string)($row['keahlian'] ?? ''));
    if ($k !== '' && !in_array($k, $anggota[$id]['keahlian'], true)) {
        $anggota[$id]['keahlian'][] = $k;
    }
}

// pisahkan Kepala Lab vs peneliti
$kepalaLab = null;
$peneliti  = [];

foreach ($anggota as $a) {
    if ($kepalaLab === null && stripos($a['jabatan'], 'kepala') !== false) {
        $kepalaLab = $a;
    } else {
        $peneliti[] = $a;
    }
}

// fallback kalau gak ada yang “kepala”
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

        <!-- ===== KEPALA LAB (DINAMIS) ===== -->
        <?php if ($kepalaLab): ?>
            <div class="kepala-lab-card">
                <div class="kepala-lab-photo">
                    <img src="<?= htmlspecialchars(foto_url($kepalaLab['foto'])) ?>" alt="Kepala Laboratorium">
                    <div class="kepala-lab-badge">
                        <?= htmlspecialchars($kepalaLab['jabatan'] ?: 'Kepala Laboratorium') ?>
                    </div>
                </div>

                <a href="DetailAnggota.php?id=<?= (int)$kepalaLab['id_anggota'] ?>" style="text-decoration:none;">
                    <div class="kepala-lab-info">
                        <h2 class="kepala-lab-name"><?= htmlspecialchars($kepalaLab['nama']) ?></h2>

                        <!-- kalau kamu punya deskripsi di tabel lain, nanti kita sambungkan -->
                        <p class="kepala-lab-desc">
                            Anggota Laboratorium dengan fokus pada pengembangan riset dan kegiatan akademik.
                        </p>

                        <hr>

                        <div class="kepala-lab-socmed">
                            <a href="#" aria-label="Google Scholar"><i class="fa-solid fa-graduation-cap"></i></a>
                            <a href="#" aria-label="LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
                            <a href="#" aria-label="ResearchGate"><i class="fa-brands fa-researchgate"></i></a>
                            <a href="#" aria-label="Facebook"><i class="fa-brands fa-facebook-f"></i></a>
                        </div>

                        <h3 class="kepala-lab-subtitle">Bidang Keahlian</h3>
                        <div class="kepala-lab-tags">
                            <?php if (!empty($kepalaLab['keahlian'])): ?>
                                <?php foreach ($kepalaLab['keahlian'] as $tag): ?>
                                    <span class="anggota-tag"><?= htmlspecialchars($tag) ?></span>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <span class="anggota-tag">-</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            </div>
        <?php endif; ?>

        <!-- ===== PARA PENELITI (DINAMIS) ===== -->
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
    <script src="../Assets/Javascript/HeaderFooter.js"></script>
</body>
</html>