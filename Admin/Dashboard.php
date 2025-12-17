<?php
require_once __DIR__ . '/Cek_Autentikasi.php';
require_once __DIR__ . '/Koneksi/KoneksiSasa.php';

function fetch_one_assoc(string $sql): ?array {
    $res = q($sql);
    $row = $res ? pg_fetch_assoc($res) : false;
    return $row ?: null;
}

function parse_keahlian(?string $raw): array {
    if (!$raw) return [];

    $raw = trim($raw);
    if ($raw === '') return [];

    if ($raw[0] === '{') {
        $raw = trim($raw, '{}');
        $items = explode(',', $raw);
    } else {
        $items = explode(',', $raw);
    }

    return array_values(array_filter(array_map(function ($v) {
        return trim($v, " \t\n\r\0\x0B\""); 
    }, $items)));
}

$totalArtikel = 0;
$totalAnggotaAktif = 0;
$totalAjuanPeminjaman = 0;

$labelsBulan = [];
$dataBulan   = [];

try {
    $row = fetch_one_assoc("SELECT COUNT(*) AS total FROM berita");
    $totalArtikel = (int)($row['total'] ?? 0);
} catch (Throwable $e) {}

try {
    $row = fetch_one_assoc("SELECT COUNT(*) AS total FROM peminjaman_lab");
    $totalAjuanPeminjaman = (int)($row['total'] ?? 0);
} catch (Throwable $e) {}

try {
    $row = fetch_one_assoc("SELECT COUNT(*) AS total FROM anggotalab WHERE status = TRUE");
    $totalAnggotaAktif = (int)($row['total'] ?? 0);
} catch (Throwable $e) {}

try {
    $sql = "
        SELECT 
            EXTRACT(MONTH FROM tanggal_pengajuan)::int AS bulan,
            COUNT(*) AS total
        FROM peminjaman_lab
        WHERE tanggal_pengajuan IS NOT NULL
        GROUP BY bulan
        ORDER BY bulan;
    ";
    $res = q($sql);
    while ($row = pg_fetch_assoc($res)) {
        $bulan = (int)($row['bulan'] ?? 0);
        if ($bulan >= 1 && $bulan <= 12) {
            $labelsBulan[] = date("M", mktime(0, 0, 0, $bulan, 1));
            $dataBulan[]   = (int)($row['total'] ?? 0);
        }
    }
} catch (Throwable $e) {}

$anggotaTerbaru = null;
try {

    $sql = "
        SELECT id_anggota, nama, jabatan, foto, keahlian, status, created_at
        FROM mv_anggota_keahlian
        WHERE status = TRUE
        ORDER BY created_at DESC NULLS LAST, id_anggota DESC
        LIMIT 1;
    ";
    $anggotaTerbaru = fetch_one_assoc($sql);

    if (!$anggotaTerbaru) {
        $sql2 = "
            SELECT id_anggota, nama, jabatan, foto, keahlian, status
            FROM mv_anggota_keahlian
            WHERE status = TRUE
            ORDER BY id_anggota DESC
            LIMIT 1;
        ";
        $anggotaTerbaru = fetch_one_assoc($sql2);
    }
} catch (Throwable $e) {

    try {
        $sql2 = "
            SELECT id_anggota, nama, jabatan, foto, keahlian, status
            FROM mv_anggota_keahlian
            WHERE status = TRUE
            ORDER BY id_anggota DESC
            LIMIT 1;
        ";
        $anggotaTerbaru = fetch_one_assoc($sql2);
    } catch (Throwable $e2) {}
}

$notifAjuan = [];
$jumlahNotifBaru = 0;

try {
    $row = fetch_one_assoc("SELECT COUNT(*) AS total FROM peminjaman_lab WHERE status = 'pending'");
    $jumlahNotifBaru = (int)($row['total'] ?? 0);

    $sql = "
        SELECT id_peminjaman, nama_peminjam, keperluan, tanggal_pengajuan, status
        FROM peminjaman_lab
        WHERE status = 'pending'
        ORDER BY tanggal_pengajuan DESC, id_peminjaman DESC
        LIMIT 5;
    ";
    $res = q($sql);
    while ($r = pg_fetch_assoc($res)) {
        $notifAjuan[] = $r;
    }
} catch (Throwable $e) {}

$pengumumanTerbaru = [];
try {
    $sql = "
        SELECT id_pengumuman, isi, tanggal_terbit, uploader
        FROM pengumuman
        WHERE status = 'Aktif'
        ORDER BY tanggal_terbit DESC, created_at DESC
        LIMIT 3;
    ";
    $res = q($sql);
    while ($row = pg_fetch_assoc($res)) {
        $pengumumanTerbaru[] = $row;
    }
} catch (Throwable $e) {}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="../Assets/Css/Admin/Dashboard.css">
    <link rel="stylesheet" href="../Assets/Css/Admin/Sidebar.css">
    <link rel="stylesheet" href="../Assets/Css/Admin/Header.css">

    <link rel="icon" type="images/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        window.peminjamanLabels = <?= json_encode($labelsBulan) ?>;
        window.peminjamanData   = <?= json_encode($dataBulan) ?>;
    </script>
</head>

<body>
<div id="sidebar"></div>

<div class="layout">
    <main class="content" id="content">

        <div class="content-header">
            <div class="topbar-left">
                <div class="greeting">
                    <h1>Halo, Maria Savira</h1>
                    <p>Ini adalah rekapan dari lab business analytics</p>
                </div>
                <img src="../Assets/Image/Logo/Maskot.png" class="header-logo">
            </div>

            <div class="header-right">
                <div class="notif-wrapper">
                    <button class="icon-circle" id="notifToggle">
                        <i class="fa-regular fa-bell"></i>
                        <?php if ($jumlahNotifBaru > 0): ?>
                            <span class="notif-badge"><?= (int)$jumlahNotifBaru ?></span>
                        <?php endif; ?>
                    </button>

                    <div class="notif-dropdown" id="notifMenu">
                        <div class="notif-header">
                            <span>Notifications</span>
                            <span class="notif-total"><?= count($notifAjuan) ?></span>
                        </div>

                        <div class="notif-list">
                            <?php if (empty($notifAjuan)): ?>
                                <p class="empty-text">Belum ada ajuan pending.</p>
                            <?php else: ?>
                                <?php foreach ($notifAjuan as $n): ?>
                                    <div class="notif-item unread">
                                        <div class="notif-icon">
                                            <i class="fa-regular fa-envelope"></i>
                                        </div>
                                        <div class="notif-content">
                                            <p class="notif-title">
                                                <?= htmlspecialchars($n['nama_peminjam'] ?? '-') ?>
                                                <span class="notif-link"><?= htmlspecialchars($n['keperluan'] ?? '-') ?></span>
                                            </p>
                                            <p class="notif-time">
                                                <?= !empty($n['tanggal_pengajuan']) ? date('d M Y', strtotime($n['tanggal_pengajuan'])) : '-' ?>
                                            </p>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="profile-dropdown" id="profileDropdown">
                    <button class="profile-toggle" type="button" id="profileToggle">
                        <span class="profile-name"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></span>

                        <?php
                        $folderUrl = '/PBL-SMT-3/Assets/Image/AnggotaLab/';
                        $folderFs  = $_SERVER['DOCUMENT_ROOT'] . $folderUrl;
                        $foto      = $_SESSION['foto'] ?? '';

                        if (!empty($foto) && file_exists($folderFs . $foto)) {
                            $src = $folderUrl . $foto;
                        } else {
                            $src = $folderUrl . 'No-Profile.png';
                        }
                        ?>

                        <img src="<?= htmlspecialchars($src) ?>" alt="Foto User" class="user-foto header-foto">
                        <i class="fa-solid fa-chevron-down profile-arrow"></i>
                    </button>

                    <div class="profile-menu" id="profileMenu">
                        <a href="/PBL-SMT-3/Admin/CRUD/ProfilAdmin/EditProfil.php" class="profile-menu-item">
                            <i class="fa-regular fa-user"></i>
                            <span>Lihat Profil</span>
                        </a>
                        <a href="/PBL-SMT-3/Admin/Logout.php" class="profile-menu-item">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <section class="stats-row">
            <article class="stat-card">
                <h4>Total Artikel</h4>
                <p class="value"><?= (int)$totalArtikel ?></p>
            </article>

            <article class="stat-card">
                <h4>Total Anggota Aktif</h4>
                <p class="value"><?= (int)$totalAnggotaAktif ?></p>
            </article>

            <article class="stat-card">
                <h4>Total Ajuan Peminjaman Lab</h4>
                <p class="value"><?= (int)$totalAjuanPeminjaman ?></p>
            </article>
        </section>

        <section class="content-grid">

            <div class="anggota-section">
                <h3 class="section-title">Anggota Terbaru</h3>

                <div class="card">
                    <?php if (!$anggotaTerbaru): ?>
                        <p class="empty-text" style="font-size:14px;color:#6b7280;">
                            Belum ada data anggota terbaru.
                        </p>
                    <?php else: ?>
                        <?php
                        $tags = parse_keahlian($anggotaTerbaru['keahlian'] ?? '');

                        $fotoUrl = '';
                        if (!empty($anggotaTerbaru['foto'])) {
                            $fotoUrl = '/PBL-SMT-3/Assets/Image/AnggotaLab/' . $anggotaTerbaru['foto'];
                        } else {
                            $fotoUrl = '/PBL-SMT-3/Assets/Image/AnggotaLab/No-Profile.png';
                        }
                        ?>
                        <div class="member-card" data-id="<?= htmlspecialchars($anggotaTerbaru['id_anggota'] ?? '') ?>">
                            <div class="member-info">
                                <div class="member-avatar">
                                    <img src="<?= htmlspecialchars($fotoUrl) ?>"
                                         alt="Foto <?= htmlspecialchars($anggotaTerbaru['nama'] ?? 'Anggota') ?>">
                                </div>

                                <div class="member-text">
                                    <h5><?= htmlspecialchars($anggotaTerbaru['nama'] ?? '-') ?></h5>
                                    <p><?= htmlspecialchars($anggotaTerbaru['jabatan'] ?? '-') ?></p>

                                    <div class="member-tags">
                                        <?php if (!empty($tags)): ?>
                                            <?php foreach ($tags as $tag): ?>
                                                <span class="tag-pill"><?= htmlspecialchars($tag) ?></span>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="tag-pill">Belum ada bidang</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="member-actions">
                                <form action="CRUD/AnggotaLab/DeleteAnggota.php" method="POST" style="display:inline-flex;">
                                    <input type="hidden" name="id_anggota" value="<?= htmlspecialchars($anggotaTerbaru['id_anggota'] ?? '') ?>">
                                    <button type="submit" class="btn-icon btn-delete" title="Hapus anggota" style="padding:0;margin:0;line-height:1;">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>

                                <a class="btn-icon btn-edit"
                                   href="CRUD/AnggotaLab/EditAnggota.php?id=<?= urlencode($anggotaTerbaru['id_anggota'] ?? '') ?>"
                                   title="Edit anggota">
                                    <i class="fa-solid fa-pen"></i>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="ajuan-section">
                <div class="card card-ajuan">
                    <h3 class="card-title-center">Ajuan Terbaru</h3>

                    <?php
                    $ajuanPending = [];
                    try {
                        $sqlPending = "
                            SELECT 
                                id_peminjaman,
                                nama_peminjam,
                                keperluan AS nama_kegiatan,
                                tanggal_pengajuan,
                                status
                            FROM peminjaman_lab
                            WHERE status = 'pending'
                            ORDER BY tanggal_pengajuan DESC, id_peminjaman DESC;
                        ";
                        $resultPending = q($sqlPending);
                        while ($row = pg_fetch_assoc($resultPending)) {
                            $ajuanPending[] = $row;
                        }
                    } catch (Throwable $e) {
                        $ajuanPending = [];
                    }
                    ?>

                    <?php if (empty($ajuanPending)): ?>
                        <p class="empty-text" style="font-size:14px;color:#6b7280;margin-top:8px;">
                            Belum ada ajuan peminjaman berstatus pending.
                        </p>
                    <?php else: ?>
                        <p style="font-size:13px;color:#6b7280;margin:8px 0 6px 0;">
                            Ajuan peminjaman terbaru yang perlu ditinjau:
                        </p>

                        <div style="overflow-y:auto; padding-right:6px; max-height:340px;">
                            <?php foreach ($ajuanPending as $ajuan): ?>
                                <div class="ajuan-latest" style="margin-top:8px; padding-top:6px; border-top:1px solid #eef2ff;">
                                    <p style="font-size:15px;font-weight:600;margin-bottom:2px;">
                                        <?= htmlspecialchars($ajuan['nama_peminjam'] ?? '-') ?>
                                    </p>

                                    <p style="font-size:13px;color:#4b5563;margin-bottom:6px;">
                                        <?= htmlspecialchars($ajuan['nama_kegiatan'] ?? '-') ?>
                                    </p>

                                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#6b7280;">
                                        <span><?= !empty($ajuan['tanggal_pengajuan']) ? date('d M Y', strtotime($ajuan['tanggal_pengajuan'])) : '-' ?></span>
                                        <span style="padding:2px 8px;border-radius:999px;background:#fef3c7;color:#92400e;font-weight:500;">
                                            <?= htmlspecialchars($ajuan['status'] ?? '-') ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <div class="pengumuman-section">
                <div class="row-1-cols">

                    <div class="col-block">
                        <h3 class="section-title">Pengumuman Terbaru</h3>

                        <div class="card pengumuman-card">
                            <?php if (empty($pengumumanTerbaru)): ?>
                                <p class="empty-text" style="font-size:14px;color:#6b7280;">
                                    Belum ada pengumuman terbaru.
                                </p>
                            <?php else: ?>
                                <?php foreach ($pengumumanTerbaru as $p): ?>
                                    <div style="margin-bottom:12px; padding-bottom:10px; border-bottom:1px solid #eef2ff;">
                                        <p style="font-size:13px;color:#111827;margin-bottom:4px;">
                                            <?= nl2br(htmlspecialchars($p['isi'] ?? '')) ?>
                                        </p>
                                        <div style="font-size:11px;color:#6b7280;display:flex;gap:8px;flex-wrap:wrap;">
                                            <span><?= !empty($p['tanggal_terbit']) ? date('d M Y', strtotime($p['tanggal_terbit'])) : '-' ?></span>
                                            <?php if (!empty($p['uploader'])): ?>
                                                <span>• Oleh <?= htmlspecialchars($p['uploader']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="col-block">
                        <h3 class="section-title">Ajuan Peminjaman per Bulan</h3>
                        <div class="card chart-card">
                            <div class="chart-wrapper">
                                <canvas id="chartPeminjaman"></canvas>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </section>

    </main>
</div>

<script src="../Assets/Javascript/Admin/HeaderDashboard.js"></script>
<script src="../Assets/Javascript/Admin/Sidebar.js"></script>
<script src="../Assets/Javascript/Admin/Dashboard.js"></script>
</body>
</html>