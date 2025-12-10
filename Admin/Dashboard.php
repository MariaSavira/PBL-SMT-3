<?php
// panggil koneksi (file koneksi.php yang berisi fungsi q() dan qparams())
require __DIR__ . '/CRUD/koneksi.php';

// ----------------------------
// INISIALISASI NILAI STATISTIK
// ----------------------------
$totalArtikel         = 0;  // akan diisi dari tabel berita
$totalAnggotaAktif    = 0;  // akan diisi dari tabel anggotalab
$totalAjuanPeminjaman = 0;  // akan diisi dari tabel peminjaman_lab

// inisialisasi array grafik
$labelsBulan = [];
$dataBulan   = [];

// ----------------------------
// TOTAL ARTIKEL DARI TABEL berita
// ----------------------------
// kalau mau hanya yang published, bisa ubah jadi: WHERE status = 'published'
try {
    $resultArtikel = q("SELECT COUNT(*) AS total FROM berita");
    $rowArtikel    = pg_fetch_assoc($resultArtikel);
    $totalArtikel  = (int)($rowArtikel['total'] ?? 0);
} catch (Throwable $e) {
    $totalArtikel = 0;
}

// ----------------------------
// TOTAL AJUAN PEMINJAMAN LAB
// ----------------------------
try {
    $resultAjuan = q("SELECT COUNT(*) AS total FROM peminjaman_lab");
    $rowAjuan    = pg_fetch_assoc($resultAjuan);
    $totalAjuanPeminjaman = (int)($rowAjuan['total'] ?? 0);
} catch (Throwable $e) {
    $totalAjuanPeminjaman = 0;
}

// ----------------------------
// TOTAL ANGGOTA AKTIF DARI TABEL anggotalab
// ----------------------------
try {
    $resultAnggota = q("
        SELECT COUNT(*) AS total
        FROM anggotalab
        WHERE status = TRUE
    ");
    $rowAnggota        = pg_fetch_assoc($resultAnggota);
    $totalAnggotaAktif = (int)($rowAnggota['total'] ?? 0);
} catch (Throwable $e) {
    $totalAnggotaAktif = 0;
}

// ----------------------------
// DATA GRAFIK PEMINJAMAN PER BULAN
// ----------------------------
try {
    $sqlPerBulan = "
        SELECT 
            EXTRACT(MONTH FROM tanggal_pengajuan)::int AS bulan,
            COUNT(*) AS total
        FROM peminjaman_lab
        WHERE tanggal_pengajuan IS NOT NULL
        GROUP BY bulan
        ORDER BY bulan;
    ";

    $resultPerBulan = q($sqlPerBulan);

    while ($row = pg_fetch_assoc($resultPerBulan)) {
        $bulanInt = (int)$row['bulan'];   // 1–12

        if ($bulanInt < 1 || $bulanInt > 12) {
            continue;
        }

        // label bulan: Jan, Feb, Mar, ...
        $labelsBulan[] = date("M", mktime(0, 0, 0, $bulanInt, 1));
        $dataBulan[]   = (int)$row['total'];
    }
} catch (Throwable $e) {
    $labelsBulan = [];
    $dataBulan   = [];
}

// ----------------------------
// ANGGOTA TERBARU DARI TABEL anggotalab
// ----------------------------
$anggotaTerbaru = null;

try {
    // kita ambil 1 anggota dengan status = TRUE, urut dari created_at paling baru
    $sqlAnggota = "
        SELECT 
            id_anggota,
            nama,
            jabatan,
            foto,
            deskripsi,
            status,
            created_at
        FROM anggotalab
        WHERE status = TRUE
        ORDER BY created_at DESC, id_anggota DESC
        LIMIT 1;
    ";

    $resultAnggota  = q($sqlAnggota);
    $anggotaTerbaru = pg_fetch_assoc($resultAnggota) ?: null;
} catch (Throwable $e) {
    $anggotaTerbaru = null;
}

// ----------------------------
// NOTIF AJUAN TERBARU (PENDING SAJA, TANPA dibaca_admin)
// ----------------------------
$notifAjuan      = [];
$jumlahNotifBaru = 0;

try {
    // hitung jumlah ajuan pending (untuk badge merah)
    $qNotifCount = q("
        SELECT COUNT(*) AS total 
        FROM peminjaman_lab 
        WHERE status = 'pending'
    ");
    $rowNotif        = pg_fetch_assoc($qNotifCount);
    $jumlahNotifBaru = (int)($rowNotif['total'] ?? 0);

    // ambil maksimal 5 ajuan pending terbaru (untuk dropdown)
    $qNotifList = q("
        SELECT 
            id_peminjaman,
            nama_peminjam,
            keperluan,
            tanggal_pengajuan,
            status
        FROM peminjaman_lab
        WHERE status = 'pending'
        ORDER BY tanggal_pengajuan DESC, id_peminjaman DESC
        LIMIT 5;
    ");

    while ($row = pg_fetch_assoc($qNotifList)) {
        $notifAjuan[] = $row;
    }
} catch (Throwable $e) {
    $jumlahNotifBaru = 0;
    $notifAjuan      = [];
}

// ----------------------------
// PENGUMUMAN TERBARU DARI TABEL pengumuman
// ----------------------------
$pengumumanTerbaru = [];

try {
    // ambil maks 3 pengumuman aktif, terbaru dari tanggal_terbit
    $sqlPengumuman = "
        SELECT 
            id_pengumuman,
            isi,
            tanggal_terbit,
            uploader,
            status
        FROM pengumuman
        WHERE status = 'Aktif'
        ORDER BY tanggal_terbit DESC, created_at DESC, id_pengumuman DESC
        LIMIT 3;
    ";

    $resultPengumuman = q($sqlPengumuman);
    while ($row = pg_fetch_assoc($resultPengumuman)) {
        $pengumumanTerbaru[] = $row;
    }
} catch (Throwable $e) {
    $pengumumanTerbaru = [];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <link rel="stylesheet" href="../Assets/Css/Admin/Dashboard.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- kirim data PHP ke JavaScript -->
    <script>
        window.peminjamanLabels = <?= json_encode($labelsBulan) ?>;
        window.peminjamanData   = <?= json_encode($dataBulan) ?>;
    </script>
</head>
<body>

<div class="layout">
    <main class="main">

        <!-- HEADER -->
        <header class="topbar">
            <div class="topbar-left">
                <div class="greeting">
                    <h1>Halo, Maria Savira</h1>
                    <p>Ini adalah rekapan dari lab business analytics</p>
                </div>
                <img src="../Assets/Image/Logo/Maskot.png" alt="Maskot" class="header-logo">
            </div>

            <div class="topbar-right">
                <!-- SATU BARIS: NOTIF + KALENDER + AVATAR -->
                <div class="topbar-icons">
                    <!-- NOTIF WRAPPER -->
                    <div class="notif-wrapper">
                        <button class="icon-circle" id="notifToggle">
                            <i class="fa-regular fa-bell"></i>

                            <?php if ($jumlahNotifBaru > 0): ?>
                                <span class="notif-badge">
                                    <?= $jumlahNotifBaru ?>
                                </span>
                            <?php endif; ?>
                        </button>

                        <!-- DROPDOWN NOTIFIKASI -->
                        <div class="notif-dropdown" id="notifMenu">
                            <div class="notif-header">
                                <span>Notifications</span>
                                <span class="notif-total">
                                    <?= count($notifAjuan) ?>
                                </span>
                            </div>

                            <div class="notif-list">
                                <?php if (empty($notifAjuan)): ?>
                                    <p style="padding:10px 18px;font-size:12px;color:#6b7280;">
                                        Belum ada ajuan pending.
                                    </p>
                                <?php else: ?>
                                    <?php foreach ($notifAjuan as $n): ?>
                                        <!-- TANPA kolom dibaca_admin, default kita kasih class unread -->
                                        <div class="notif-item unread">
                                            <div class="notif-icon">
                                                <i class="fa-regular fa-envelope"></i>
                                            </div>
                                            <div class="notif-content">
                                                <p class="notif-title">
                                                    <?= htmlspecialchars($n['nama_peminjam']) ?>
                                                    <span class="notif-link">
                                                        <?= htmlspecialchars($n['keperluan']) ?>
                                                    </span>
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

                    <!-- KALENDER DI SAMPING LONCENG -->
                    <div class="calendar-wrapper">
                        <button class="icon-circle small-icon" id="calendarButton">
                            <i class="fa-regular fa-calendar-days"></i>
                        </button>
                        <input type="date" id="calendarInput" class="calendar-input-hidden">
                    </div>
            </div>
        </header>

        <!-- STATISTIK -->
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

        <!-- GRID BESAR -->
        <section class="content-grid">

            <!-- ANGGOTA TERBARU -->
            <div class="anggota-section">
                <h3 class="section-title">Anggota Terbaru</h3>

                <div class="card">
                    <?php if (!$anggotaTerbaru): ?>

                        <p class="empty-text" style="font-size:14px;color:#6b7280;">
                            Belum ada data anggota terbaru.
                        </p>

                    <?php else: ?>
                        <?php
                        // kita pakai kolom deskripsi sebagai list keahlian, dipisah koma
                        $tags = [];
                        if (!empty($anggotaTerbaru['deskripsi'])) {
                            $tags = array_filter(array_map('trim', explode(',', $anggotaTerbaru['deskripsi'])));
                        }

                        // path foto, kalau di DB cuma nama file, bisa tambahkan folder di depan
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
                                <!-- HAPUS -->
                                <a class="btn-icon btn-delete"
                                   href="HapusAnggota.php?id=<?= urlencode($anggotaTerbaru['id_anggota']) ?>"
                                   onclick="return confirm('Yakin ingin menghapus anggota ini?');"
                                   title="Hapus anggota">
                                    <i class="fa-solid fa-trash"></i>
                                </a>

                                <!-- EDIT -->
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

            <!-- AJUAN TERBARU (CARD BESAR DI KANAN) -->
            <div class="ajuan-section">
                <div class="card card-ajuan">
                    <h3 class="card-title-center">Ajuan Terbaru</h3>

                    <?php
                    // Ambil SEMUA ajuan berstatus pending (tanpa LIMIT) untuk card kanan
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

                        <!-- AREA SCROLL -->
                        <div style="overflow-y:auto; padding-right:6px; max-height:340px;">

                            <?php foreach ($ajuanPending as $ajuan): ?>
                                <div class="ajuan-latest"
                                     style="margin-top:8px; padding-top:6px; border-top:1px solid #eef2ff;">

                                    <p style="font-size:15px;font-weight:600;margin-bottom:2px;">
                                        <?= htmlspecialchars($ajuan['nama_peminjam']) ?>
                                    </p>

                                    <p style="font-size:13px;color:#4b5563;margin-bottom:6px;">
                                        <?= htmlspecialchars($ajuan['nama_kegiatan'] ?? '-') ?>
                                    </p>

                                    <div style="display:flex;align-items:center;gap:8px;font-size:12px;color:#6b7280;">
                                        <span><?= date('d M Y', strtotime($ajuan['tanggal_pengajuan'])) ?></span>

                                        <span style="
                                            padding:2px 8px;
                                            border-radius:999px;
                                            background:#fef3c7;
                                            color:#92400e;
                                            font-weight:500;
                                        ">
                                            <?= htmlspecialchars($ajuan['status']) ?>
                                        </span>
                                    </div>

                                </div>
                            <?php endforeach; ?>

                        </div>
                    <?php endif; ?>

                </div>
            </div>

            <!-- PENGUMUMAN DAN GRAFIK -->
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
                                            <?= nl2br(htmlspecialchars($p['isi'])) ?>
                                        </p>
                                        <div style="font-size:11px;color:#6b7280;display:flex;gap:8px;flex-wrap:wrap;">
                                            <span>
                                                <?= date('d M Y', strtotime($p['tanggal_terbit'])) ?>
                                            </span>
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

<!-- ?v=4 supaya browser ambil file JS terbaru (anti cache) -->
<script src="../Assets/Javascript/Admin/Dashboard.js?v=4"></script>

</body>
</html>
