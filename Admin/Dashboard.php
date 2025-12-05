<?php
// Dashboard.php (di folder Admin)

// kalau nanti pakai session login admin:
// session_start();

// panggil koneksi & helper q(), qparams()
require __DIR__ . '/CRUD/Koneksi.php';

// ambil data dari tabel bidangriset (sama seperti halaman Riset)
$res  = q('SELECT id_riset, nama_bidang_riset FROM bidangriset ORDER BY id_riset ASC');
$rows = pg_fetch_all($res) ?: [];

// sementara angka statistik masih statis (boleh nanti disambung ke query)
$totalArtikel         = 20;
$totalAnggotaAktif    = 8;
$totalAjuanPeminjaman = 20;

$totalBidangRiset     = count($rows);
$risetBerjalan        = 12;
$risetSelesai         = 8;

// ===============================
// DATA UNTUK HISTOGRAM (DARI DB)
// ===============================
$chartLabels = [];
$chartData   = [];

// setiap bidang riset jadi satu bar dengan nilai 1
foreach ($rows as $row) {
    $chartLabels[] = $row['nama_bidang_riset']; // label di sumbu X
    $chartData[]   = 1;                         // tinggi bar (sementara 1 per bidang)
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Admin</title>

    <!-- FONT & ICON -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- CSS -->
    <link rel="stylesheet" href="../Assets/Css/Admin/Dashboard.css">

    <!-- CHART.JS UNTUK GRAFIK -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="page-top-line"></div>

<div class="layout">
    <!-- KALAU NANTI PAKAI SIDEBAR, BISA DITAMBAH DI SINI -->

    <main class="main">

        <!-- HEADER -->
        <header class="topbar">
            <div class="topbar-left">
                <div class="greeting">
                    <h1>Halo, Maria Savira</h1>
                    <p>Ini adalah rekapan dari lab business analytics</p>
                </div>

                <!-- maskot di kanan teks -->
                <img src="../Assets/Image/Logo/Maskot.png" alt="Maskot" class="header-logo">
            </div>

            <div class="topbar-right">
                <!-- baris atas: lonceng + avatar -->
                <div class="topbar-icons">
                    <button class="icon-circle">
                        <i class="fa-regular fa-bell"></i>
                    </button>
                    <div class="user-avatar"></div>
                </div>

                <!-- baris bawah: Bulan Ini + kalender -->
                <div class="topbar-filter">
                    <button class="btn-filter">
                        Bulan Ini <i class="fa-solid fa-chevron-down"></i>
                    </button>

                    <button class="icon-circle small-icon">
                        <i class="fa-regular fa-calendar-days"></i>
                    </button>
                </div>
            </div>
        </header>

        <!-- STATISTIK -->
        <section class="stats-row">
            <article class="stat-card">
                <h4>Total Artikel</h4>
                <p class="value"><?php echo $totalArtikel; ?></p>
            </article>

            <article class="stat-card">
                <h4>Total Anggota Aktif</h4>
                <p class="value"><?php echo $totalAnggotaAktif; ?></p>
            </article>

            <article class="stat-card">
                <h4>Total Ajuan Peminjaman Lab</h4>
                <p class="value"><?php echo $totalAjuanPeminjaman; ?></p>
            </article>
        </section>

        <!-- ANGGOTA TERBARU + AJUAN TERBARU + PENGUMUMAN (1 GRID BESAR) -->
        <section class="content-grid">

            <!-- ANGGOTA TERBARU (kolom 1-2, baris 1) -->
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
                            <button class="btn-icon btn-delete">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                            <button class="btn-icon btn-edit">
                                <i class="fa-solid fa-pen"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AJUAN TERBARU (kolom 3, baris 1–2, tinggi 411) -->
            <div class="ajuan-section">
                <h3 class="section-title text-center">Ajuan Terbaru</h3>
                <div class="card card-ajuan">
                    <!-- nanti isi list ajuan di sini (loop dari DB kalau perlu) -->
                </div>
            </div>

            <!-- PENGUMUMAN TERBARU (kolom 1-2, baris 2) -->
            <div class="pengumuman-section">
                <h3 class="section-title">Pengumuman Terbaru</h3>
                <div class="row-1-cols">

                    <!-- KOTAK KIRI: pengumuman -->
                    <div class="card pengumuman-card">
                        <p class="empty-text" style="color:#6b7280; font-size:14px;">
                            Belum ada pengumuman terbaru.
                        </p>
                    </div>

                    <!-- KOTAK KANAN: HISTOGRAM dari tabel bidangriset -->
                    <div class="card chart-card">
                        <h4 class="chart-title" style="margin-bottom: 12px; font-size:15px;">
                            Bidang Riset Terdaftar
                        </h4>
                        <div class="chart-wrapper" style="width:100%; height:260px;">
                            <canvas id="chartBidangRiset"></canvas>
                        </div>
                    </div>

                </div>
            </div>

        </section>

        <!-- contoh kalau mau cek data bidangriset (debug sementara) -->
        <!--
        <pre>
        <?php // print_r($rows); ?>
        </pre>
        -->

    </main>
</div>

<!-- SCRIPT GRAFIK HISTOGRAM -->
<script>
    // data dari PHP (tabel bidangriset)
    const labelsBidang = <?php echo json_encode($chartLabels); ?>;
    const dataBidang   = <?php echo json_encode($chartData); ?>;

    const canvasBidang = document.getElementById('chartBidangRiset');
    if (canvasBidang) {
        const ctx = canvasBidang.getContext('2d');

        new Chart(ctx, {
            type: 'bar',   // bentuk histogram
            data: {
                labels: labelsBidang,
                datasets: [{
                    label: 'Jumlah',
                    data: dataBidang,
                    borderWidth: 1,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    }
</script>

</body>
</html>
