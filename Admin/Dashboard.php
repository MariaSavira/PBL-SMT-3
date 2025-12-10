<?php
    // panggil koneksi (file koneksi.php yang berisi fungsi q() dan qparams())
    require_once __DIR__ . '/Cek_Autentikasi.php';
    require __DIR__ . '../Koneksi/KoneksiSasa.php';

    // ----------------------------
    // INISIALISASI NILAI STATISTIK
    // ----------------------------
    $totalArtikel         = 20; // sementara masih dummy
    $totalAnggotaAktif    = 8;  // sementara masih dummy
    $totalAjuanPeminjaman = 0;  // ini dari database

    // inisialisasi array grafik
    $labelsBulan = [];
    $dataBulan   = [];

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
    <link rel="icon" type="images/x-icon"
        href="../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="../Assets/Css/Admin/Dashboard.css">
    <link rel="stylesheet" href="../Assets/Css/Admin/Sidebar.css">
    <link rel="stylesheet" href="../Assets/Css/Admin/Header.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- kirim data PHP ke JavaScript -->
    <script>
        window.peminjamanLabels = <?= json_encode($labelsBulan) ?>;
        window.peminjamanData   = <?= json_encode($dataBulan) ?>;

        console.log("Data dari PHP:", window.peminjamanLabels, window.peminjamanData);
    </script>
</head>
<body>
    <div id="sidebar"></div>
        <div class="layout">
            <main class="content" id="content">

                <!-- HEADER -->
                 <!-- <header class="topbar">
                    <div class="topbar-left">
                        <div class="greeting">
                            <h1>Halo, Maria Savira</h1>
                            <p>Ini adalah rekapan dari lab business analytics</p>
                        </div>
                        <img src="../Assets/Image/Logo/Maskot.png" alt="Maskot" class="header-logo">
                    </div>

                    <div class="topbar-right">
                        <div class="topbar-icons">
                            <button class="icon-circle"><i class="fa-regular fa-bell"></i></button>
                            <div class="user-avatar"></div>
                        </div>

                        <div class="topbar-filter">
                            
                            <div class="filter-wrapper">
                                <button class="btn-filter" id="filterToggle">
                                    <span id="filterLabel">Bulan Ini</span>
                                    <i class="fa-solid fa-chevron-down"></i>
                                </button>

                                <div class="filter-menu" id="filterMenu">
                                    <button type="button" data-range="week">Minggu Ini</button>
                                    <button type="button" data-range="month" class="active">Bulan Ini</button>
                                    <button type="button" data-range="year">Tahun Ini</button>
                                </div>
                            </div>

                            <button class="icon-circle small-icon" id="calendarButton">
                                <i class="fa-regular fa-calendar-days"></i>
                            </button>

                            <input type="date" id="calendarInput"
                                style="opacity:0;pointer-events:none;position:absolute;top:100%;right:0;width:0;height:0;">
                        </div>
                    </div>
                </header> -->
                <div id="header"></div>

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
                            <div class="member-card">
                                <div class="member-info">
                                    <div class="member-avatar"></div>

                                    <div class="member-text">
                                        <h5>Maria Savira</h5>
                                        <p>Asisten Lab</p>

                                        <div class="member-tags">
                                            <span class="tag-pill">IT Governance</span>
                                            <span class="tag-pill">Data Analytics</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="member-actions">
                                    <button class="btn-icon btn-delete"><i class="fa-solid fa-trash"></i></button>
                                    <button class="btn-icon btn-edit"><i class="fa-solid fa-pen"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- AJUAN TERBARU -->
                    <div class="ajuan-section">
                        <div class="card card-ajuan">
                            <h3 class="card-title-center">Ajuan Terbaru</h3>

                            <?php
                            // Ambil SEMUA ajuan berstatus pending (tanpa LIMIT)
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

                                <!-- AREA SCROLL
                                    Tinggi kira-kira cukup untuk ±5 item,
                                    sisanya bisa di-scroll -->
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
                                    <p class="empty-text" style="font-size:14px;color:#6b7280;">
                                        Belum ada pengumuman terbaru.
                                    </p>
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
    <script src="../Assets/Javascript/Admin/Sidebar.js"></script>
    <script src="../Assets/Javascript/Admin/Header.js"></script>
    <script src="../Assets/Javascript/Admin/Dashboard.js"></script>

</body>
</html>