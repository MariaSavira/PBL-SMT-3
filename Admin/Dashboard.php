<?php
require __DIR__ . '/CRUD/Koneksi.php';

// contoh data statis
$totalArtikel         = 20;
$totalAnggotaAktif    = 8;
$totalAjuanPeminjaman = 20;
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
                <div class="topbar-icons">
                    <button class="icon-circle"><i class="fa-regular fa-bell"></i></button>
                    <div class="user-avatar"></div>
                </div>

                <div class="topbar-filter">
                    <!-- FILTER -->
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

                    <!-- KALENDER -->
                    <button class="icon-circle small-icon" id="calendarButton">
                        <i class="fa-regular fa-calendar-days"></i>
                    </button>

                    <input type="date" id="calendarInput"
                        style="opacity:0;pointer-events:none;position:absolute;top:100%;right:0;width:0;height:0;">
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
                </div>
            </div>

            <!-- PENGUMUMAN DAN GRAFIK -->
            <div class="pengumuman-section">

                <div class="row-1-cols">

                    <div class="col-block">
                        <h3 class="section-title">Pengumuman Terbaru</h3>

                        <div class="card pengumuman-card">
                            <p class="empty-text" style="font-size:14px;color:#6b7280;">Belum ada pengumuman terbaru.</p>
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

<script src="../Assets/Javascript/Admin/Dashboard.js"></script>

</body>
</html>
