<?php
require_once __DIR__ . '/Cek_Autentikasi.php';
require __DIR__ . '../Koneksi/KoneksiSasa.php';

$totalArtikel = 0;
$totalAnggotaAktif = 0;
$totalAjuanPeminjaman = 0;

$labelsBulan = [];
$dataBulan   = [];

// TOTAL ARTIKEL DARI TABEL berita

try {
    $res = q("SELECT COUNT(*) AS total FROM berita");
    $totalArtikel = (int)(pg_fetch_result($res, 0, 'total') ?? 0);
} catch (Throwable $e) {
}

<<<<<<< HEAD
// TOTAL AJUAN PEMINJAMAN LAB

=======
/* ------------------- TOTAL AJUAN ------------------- */
>>>>>>> b977c2c2f3cf3c8b386b1803e0270941eade35ab
try {
    $res = q("SELECT COUNT(*) AS total FROM peminjaman_lab");
    $totalAjuanPeminjaman = (int)(pg_fetch_result($res, 0, 'total') ?? 0);
} catch (Throwable $e) {
}

<<<<<<< HEAD

// TOTAL ANGGOTA AKTIF DARI TABEL anggotalab

=======
/* ------------------- TOTAL ANGGOTA AKTIF ------------------- */
>>>>>>> b977c2c2f3cf3c8b386b1803e0270941eade35ab
try {
    $res = q("SELECT COUNT(*) AS total FROM anggotalab WHERE status = TRUE");
    $totalAnggotaAktif = (int)(pg_fetch_result($res, 0, 'total') ?? 0);
} catch (Throwable $e) {
}

<<<<<<< HEAD
// DATA GRAFIK PEMINJAMAN PER BULAN

=======
/* ------------------- DATA PEMINJAMAN PER BULAN ------------------- */
>>>>>>> b977c2c2f3cf3c8b386b1803e0270941eade35ab
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
        $bulan = (int)$row['bulan'];
        if ($bulan >= 1 && $bulan <= 12) {
            $labelsBulan[] = date("M", mktime(0, 0, 0, $bulan, 1));
            $dataBulan[]   = (int)$row['total'];
        }
    }
} catch (Throwable $e) {
}

<<<<<<< HEAD
// ANGGOTA TERBARU DARI TABEL anggotalab

=======
/* ------------------- ANGGOTA TERBARU ------------------- */
>>>>>>> b977c2c2f3cf3c8b386b1803e0270941eade35ab
$anggotaTerbaru = null;
try {
    $sql = "
        SELECT id_anggota, nama, jabatan, foto, deskripsi, created_at
        FROM anggotalab
        WHERE status = TRUE
        ORDER BY created_at DESC, id_anggota DESC
        LIMIT 1;
    ";
    $res = q($sql);
    $anggotaTerbaru = pg_fetch_assoc($res) ?: null;
} catch (Throwable $e) {
}

<<<<<<< HEAD
// NOTIF AJUAN TERBARU (PENDING SAJA, TANPA dibaca_admin)

$notifAjuan      = [];
=======
/* ------------------- NOTIFIKASI ------------------- */
$notifAjuan = [];
>>>>>>> b977c2c2f3cf3c8b386b1803e0270941eade35ab
$jumlahNotifBaru = 0;
try {
    $res = q("SELECT COUNT(*) AS total FROM peminjaman_lab WHERE status = 'pending'");
    $jumlahNotifBaru = (int)(pg_fetch_result($res, 0, 'total') ?? 0);

    $sql = "
        SELECT id_peminjaman, nama_peminjam, keperluan, tanggal_pengajuan, status
        FROM peminjaman_lab
        WHERE status = 'pending'
<<<<<<< HEAD
        ORDER BY tanggal_pengajuan DESC, id_peminjaman DESC");

    while ($row = pg_fetch_assoc($qNotifList)) {
=======
        ORDER BY tanggal_pengajuan DESC, id_peminjaman DESC
        LIMIT 5;
    ";
    $res = q($sql);
    while ($row = pg_fetch_assoc($res)) {
>>>>>>> b977c2c2f3cf3c8b386b1803e0270941eade35ab
        $notifAjuan[] = $row;
    }
} catch (Throwable $e) {
}

<<<<<<< HEAD

// PENGUMUMAN TERBARU DARI TABEL pengumuman

=======
/* ------------------- PENGUMUMAN ------------------- */
>>>>>>> b977c2c2f3cf3c8b386b1803e0270941eade35ab
$pengumumanTerbaru = [];
try {
    $sql = "
        SELECT id_pengumuman, isi, tanggal_terbit, uploader
        FROM pengumuman
        WHERE status = 'Aktif'
<<<<<<< HEAD
        ORDER BY tanggal_terbit DESC, created_at DESC, id_pengumuman DESC";

    $resultPengumuman = q($sqlPengumuman);
    while ($row = pg_fetch_assoc($resultPengumuman)) {
=======
        ORDER BY tanggal_terbit DESC, created_at DESC
        LIMIT 3;
    ";
    $res = q($sql);
    while ($row = pg_fetch_assoc($res)) {
>>>>>>> b977c2c2f3cf3c8b386b1803e0270941eade35ab
        $pengumumanTerbaru[] = $row;
    }
} catch (Throwable $e) {
}
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        window.peminjamanLabels = <?= json_encode($labelsBulan) ?>;
        window.peminjamanData = <?= json_encode($dataBulan) ?>;
    </script>
</head>

<body>
    <div id="sidebar"></div>

    <div class="layout">
        <main class="content" id="content">

            <header class="topbar">
                <div class="topbar-left">
                    <div class="greeting">
                        <h1>Halo, Maria Savira</h1>
                        <p>Ini adalah rekapan dari lab business analytics</p>
                    </div>
                    <img src="../Assets/Image/Logo/Maskot.png" class="header-logo">
                </div>

                <div class="topbar-right">
                    <div class="topbar-icons">

                        <!-- NOTIF -->
                        <div class="notif-wrapper">
                            <button class="icon-circle" id="notifToggle">
                                <i class="fa-regular fa-bell"></i>
                                <?php if ($jumlahNotifBaru > 0): ?>
                                    <span class="notif-badge"><?= $jumlahNotifBaru ?></span>
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
                                                        <?= htmlspecialchars($n['nama_peminjam']) ?>
                                                        <span class="notif-link"><?= htmlspecialchars($n['keperluan']) ?></span>
                                                    </p>
                                                    <p class="notif-time">
                                                        <?= date('d M Y', strtotime($n['tanggal_pengajuan'])) ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- KALENDER -->
                        <div class="calendar-wrapper">
                            <button class="icon-circle small-icon">
                                <i class="fa-regular fa-calendar-days"></i>
                            </button>
                            <input type="date" id="calendarInput" class="calendar-input-hidden">
                        </div>

                    </div>
                </div>
            </header>

            <section class="stats-row">
                <article class="stat-card">
                    <h4>Total Artikel</h4>
                    <p class="value"><?= $totalArtikel ?></p>
                </article>

                <article class="stat-card">
                    <h4>Total Anggota Aktif</h4>
                    <p class="value"><?= $totalAnggotaAktif ?></p>
                </article>

                <article class="stat-card">
                    <h4>Total Ajuan Peminjaman Lab</h4>
                    <p class="value"><?= $totalAjuanPeminjaman ?></p>
                </article>
            </section>

            <section class="content-grid">
                <!-- ============================= -->
                <!-- ANGGOTA TERBARU -->
                <!-- ============================= -->
                <div class="anggota-section">
                    <h3 class="section-title">Anggota Terbaru</h3>

                    <div class="card">
                        <?php if (!$anggotaTerbaru): ?>

                            <p class="empty-text" style="font-size:14px;color:#6b7280;">
                                Belum ada data anggota terbaru.
                            </p>

                        <?php else: ?>
                            <?php
                            $tags = [];
                            if (!empty($anggotaTerbaru['deskripsi'])) {
                                $tags = array_filter(array_map('trim', explode(',', $anggotaTerbaru['deskripsi'])));
                            }

                            $fotoUrl = '';
                            if (!empty($anggotaTerbaru['foto'])) {
                                $fotoUrl = '../Assets/Image/Anggota/' . $anggotaTerbaru['foto'];
                            }
                            ?>

                            <div class="member-card" data-id="<?= htmlspecialchars($anggotaTerbaru['id_anggota']) ?>">
                                <div class="member-info">

                                    <div class="member-avatar">
                                        <?php if ($fotoUrl): ?>
                                            <img src="<?= htmlspecialchars($fotoUrl) ?>"
                                                alt="Foto <?= htmlspecialchars($anggotaTerbaru['nama']) ?>">
                                        <?php endif; ?>
                                    </div>

                                    <div class="member-text">
                                        <h5><?= htmlspecialchars($anggotaTerbaru['nama']) ?></h5>
                                        <p><?= htmlspecialchars($anggotaTerbaru['jabatan']) ?></p>

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
                                    <a class="btn-icon btn-delete"
                                        href="HapusAnggota.php?id=<?= urlencode($anggotaTerbaru['id_anggota']) ?>"
                                        onclick="return confirm('Yakin ingin menghapus anggota ini?');"
                                        title="Hapus anggota">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>

                                    <a class="btn-icon btn-edit"
                                        href="EditAnggota.php?id=<?= urlencode($anggotaTerbaru['id_anggota']) ?>"
                                        title="Edit anggota">
                                        <i class="fa-solid fa-pen"></i>
                                    </a>
                                </div>
                            </div>

                        <?php endif; ?>
                    </div>
                </div>


                <!-- ============================= -->
                <!-- AJUAN TERBARU - KANAN -->
                <!-- ============================= -->

                <div class="ajuan-section">
                    <div class="card card-ajuan">
                        <h3 class="card-title-center">Ajuan Terbaru</h3>

                        <?php
                        $ajuanPending = [];
                        try {
                            $sql = "
                SELECT id_peminjaman, nama_peminjam, keperluan AS nama_kegiatan,
                       tanggal_pengajuan, status
                FROM peminjaman_lab
                WHERE status = 'pending'
                ORDER BY tanggal_pengajuan DESC, id_peminjaman DESC
            ";
                            $res = q($sql);

                            while ($row = pg_fetch_assoc($res)) {
                                $ajuanPending[] = $row;
                            }
                        } catch (Throwable $e) {
                            $ajuanPending = [];
                        }
                        ?>

                        <?php if (empty($ajuanPending)): ?>
                            <p class="empty-text">Belum ada ajuan pending.</p>

                        <?php else: ?>
                            <div style="max-height:350px; overflow-y:auto; padding-right:8px;">
                                <?php foreach ($ajuanPending as $a): ?>
                                    <div class="ajuan-item">
                                        <h4><?= htmlspecialchars($a['nama_peminjam']) ?></h4>
                                        <p><?= htmlspecialchars($a['nama_kegiatan']) ?></p>
                                        <small><?= date('d M Y', strtotime($a['tanggal_pengajuan'])) ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

                <!-- ============================= -->
                <!-- PENGUMUMAN & GRAFIK -->
                <!-- ============================= -->

                <div class="pengumuman-section">

                    <div class="row-1-cols">

                        <!-- PENGUMUMAN -->
                        <div class="col-block">
                            <h3 class="section-title">Pengumuman Terbaru</h3>

                            <div class="card pengumuman-card">
                                <?php if (empty($pengumumanTerbaru)): ?>
                                    <p class="empty-text">Belum ada pengumuman terbaru.</p>

                                <?php else: ?>
                                    <?php foreach ($pengumumanTerbaru as $p): ?>
                                        <div style="margin-bottom:12px; padding-bottom:10px; border-bottom:1px solid #eef2ff;">
                                            <p class="pengumuman-text">
                                                <?= nl2br(htmlspecialchars($p['isi'])) ?>
                                            </p>

                                            <div class="pengumuman-meta">
                                                <span><?= date('d M Y', strtotime($p['tanggal_terbit'])) ?></span>
                                                <?php if (!empty($p['uploader'])): ?>
                                                    <span>• Oleh <?= htmlspecialchars($p['uploader']) ?></span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>


                        <!-- GRAFIK -->
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
        </main>
    </div>
    <script src="../Assets/Javascript/Admin/Header.js"></script>
    <script src="../Assets/Javascript/Admin/Sidebar.js"></script>
    <script src="../Assets/Javascript/Admin/Dashboard.js"></script>

</body>

</html>