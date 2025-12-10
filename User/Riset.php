<?php
require __DIR__ . '/../Admin/Koneksi/KoneksiSasa.php';

// Ambil data bidang riset dari database
$res  = q('SELECT id_riset, nama_bidang_riset FROM bidangriset ORDER BY id_riset ASC');
$rows = pg_fetch_all($res) ?: [];
$totalRisetAktif = is_array($rows) ? count($rows) : 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Riset Penelitian</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="../Assets/Css/HeaderFooter.css">
    <link rel="stylesheet" href="../Assets/Css/Riset.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">

    <link rel="icon" type="images/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />
</head>

<body>
    <div id="header"></div>

    <div class="heading">
        <h1>Riset Penelitian</h1>
        <p>Jelajahi berbagai fokus riset yang sedang kami kembangkan melalui
            mind map dan<br>bidang-bidang utama di bawah ini.</p>
    </div>

    <div class="diagram-container">
        <img class="diagram-img" src="../Assets/Image/Riset/diagram.png" alt="Diagram Riset" />
    </div>

    <div class="section-title">Bidang Riset Utama</div>

    <div class="tag-list">
        <?php if (!$rows): ?>
            <p style="text-align:center; color:#666; margin-top:10px;">
                Belum ada data riset.
            </p>
        <?php else: ?>
            <?php foreach ($rows as $row): ?>
                <div class="tag">
                    <?= htmlspecialchars($row['nama_bidang_riset']) ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- ================= STAT BOX TETAP SAMA ================= -->

    <div class="stats">
        <div class="stat-box">
            <div class="stat-number">
                <?= $totalRisetAktif ?>
            </div>
            <div class="stat-label">Jumlah Riset Aktif</div>
        </div>

    <a href="Publikasi.html" class="cta-btn">Lihat Publikasi</a>

    <div id="footer"></div>

        <div class="section-title">Bidang Riset Utama</div>

        <div class="tag-list">
            <div class="tag">Anomaly Detection</div>
            <div class="tag">Identity Theft</div>
            <div class="tag">Fraud Detection</div>
            <div class="tag">Brand Image Analysis</div>
            <div class="tag">Customer Analytics</div>
            <div class="tag">Competitive Monitoring</div>
            <div class="tag">Digital Marketing Analysis</div>
            <div class="tag">New Product Development</div>
        </div>

        <div class="stats">
            <div class="stat-box">
                <div class="stat-number">25</div>
                <div class="stat-label">Jumlah Riset Aktif</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">8</div>
                <div class="stat-label">Kolaborator</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">7</div>
                <div class="stat-label">Bidang Fokus</div>
            </div>
            <div class="stat-box">
                <div class="stat-number">42</div>
                <div class="stat-label">Publikasi</div>
            </div>
        </div>

        <a href="Publikasi.html" class="cta-btn">Lihat Publikasi</a>
        <div id="footer"></div>

        <script src="../Assets/Javascript/HeaderFooter.js"></script>
    </body>
</html>
