<!DOCTYPE html>
<html lang="id">

<?php
require __DIR__ . '/../Admin/Koneksi/KoneksiValia.php';
require_once __DIR__ . '/../Admin/Cek_Autentikasi.php';


$stmt = $db->prepare("
  SELECT
    b.id_berita,
    b.judul,
    b.isi,
    b.gambar,
    b.tanggal,
    b.status,
    a.username AS author_username
  FROM berita b
  LEFT JOIN anggotalab a
    ON a.id_anggota = b.uploaded_by
  WHERE b.status = 'publish'
  ORDER BY b.tanggal DESC, b.id_berita DESC
  LIMIT 4
");
$stmt->execute();
$berita = $stmt->fetchAll();

$beritaUtama = $berita[0] ?? null;
$beritaKecil = array_slice($berita, 1);


$stmtGaleri = $db->prepare("
    SELECT id_galeri, judul, file_path
    FROM galeri
    ORDER BY tanggal_upload DESC NULLS LAST, id_galeri DESC
    LIMIT 3
");
$stmtGaleri->execute();
$galeriList = $stmtGaleri->fetchAll();

$stmtRiset = $db->prepare("
    SELECT nama_bidang_riset
    FROM bidangriset
    ORDER BY id_riset ASC
");
$stmtRiset->execute();
$fokusRisetRows = $stmtRiset->fetchAll();

?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Business Analytics</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="icon" type="images/x-icon"
        href="../Assets/Image/Logo/Logo Without Text.png" />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <link rel="stylesheet" href="../Assets/Css/Index.css">
</head>

<body>
    <div id="header"></div>
    <section class="hero" id="hero">
        <video autoplay muted loop playsinline class="hero-video">
            <source src="../Assets/Video/Homepage Video Bg.mp4"
                type="video/mp4">
        </video>

        <div class="overlay"></div>
        <div class="hero-content">
            <h1>Selamat Datang di<br>Laboratorium Analisa Bisnis</h1>
            <p>Menciptakan Solusi, Bukan Sekedar Analisis</p>
        </div>
    </section>

    <section class="profil-lab">
        <div class="profil-container">
            <div class="profil-image">
                <img src="../Assets/Image/Homepage/img/Profile.png"
                    alt="Profil Laboratorium">
            </div>
            <div class="profil-text">
                <h2>Profil Laboratorium</h2>
                <p>
                    Jelajahi profil lengkap laboratorium: fokus riset,
                    keunggulan, dan
                    informasi penting lainnya.
                </p>
                <a href="TentangKami.php" class="btn-profil">Cari Tahu Lebih
                    Lanjut</a>
            </div>
        </div>
    </section>

    <section class="visi-misi-section">
        <div class="vm-container">
            <div class="vm-card">
                <img src="../Assets/Image/Homepage/img/Visi.png"
                    alt="Ikon Visi" class="vm-icon">

                <h2 class="vm-title">Visi</h2>

                <p class="vm-text">
                    Menjadi laboratorium unggul rujukan nasional sebagai
                    inkubator solusi cerdas berbasis data,
                    yang berfungsi sebagai mitra strategis industri untuk
                    mengakselerasi transformasi bisnis
                    dan pengambilan keputusan yang berdampak.
                </p>
            </div>
            <div class="vm-card">
                <img src="../Assets/Image/Homepage/img/Misi.png"
                    alt="Ikon Misi" class="vm-icon">
                <h2 class="vm-title">Misi</h2>
                <ul class="vm-list">
                    <li>Mengembangkan riset terapan</li>
                    <li>Mengin­tegrasikan berbagai disiplin ilmu</li>
                    <li>Membangun kemitraan strategis dengan industry</li>
                    <li>Mengembangkan talenta (dosen dan mahasiswa)</li>
                    <li>Menjalankan tata kelola laboratorium yang
                        profesional, etis, dan berkelanjutan</li>
                </ul>
            </div>
        </div>
    </section>

    <div class="fokus-riset-section">
        <h2 class="judul-fokus">Fokus Riset</h2>

        <div class="fokus-row">
            <?php foreach (array_slice($fokusRisetRows, 0, 5) as $row): ?>
                <div class="fokus-card"><?= htmlspecialchars($row['nama_bidang_riset']) ?></div>
            <?php endforeach; ?>
        </div>

        <div class="fokus-row">
            <?php foreach (array_slice($fokusRisetRows, 5) as $row): ?>
                <div class="fokus-card row2"><?= htmlspecialchars($row['nama_bidang_riset']) ?></div>
            <?php endforeach; ?>
        </div>

        <div class="cta-container" style="margin-bottom: 0px">
            <a href="Riset.php" class="cta-btn">Lihat lebih banyak</a>
        </div>
    </div>


    <section class="news-section py-5">
        <div class="container-l px-5">
            <div class="news-container">
                <h3 class="section-title">Berita Terbaru</h3>

                <div class="news-flex">
                    <div class="berita-utama">
                        <?php if ($beritaUtama): ?>
                            <a href="Isi_berita.php?id=<?= urlencode($beritaUtama['id_berita']) ?>" style="text-decoration:none; color:inherit;">
                                <img
                                    src="/PBL-SMT-3/Assets/Image/Galeri-Berita/<?= htmlspecialchars($beritaUtama['gambar']) ?>"
                                    class="big-news-img"
                                    alt="big news">
                                <h4 class="judul-utama">
                                    <?= htmlspecialchars($beritaUtama['judul']) ?>
                                </h4>
                            </a>

                            <div class="meta-utama">
                                <span><?= date('d F Y', strtotime($beritaUtama['tanggal'])) ?></span>
                                <span>•</span>
                                <i class="fa-solid fa-user"></i>
                                <span><span><?= htmlspecialchars($beritaUtama['author_username'] ?? '-') ?></span></span>
                            </div>

                            <p class="deskripsi-utama">
                                <?= htmlspecialchars(mb_substr(strip_tags($beritaUtama['isi']), 0, 140)) ?>...
                            </p>
                        <?php endif; ?>

                    </div>

                    <div class="berita-list">
                        <?php foreach ($beritaKecil as $item): ?>
                            <a href="Isi_berita.php?id=<?= urlencode($item['id_berita']) ?>" style="text-decoration:none; color:inherit;">
                                <div class="berita-item">
                                    <img
                                        src="../Assets/Image/Galeri-Berita/<?= htmlspecialchars($item['gambar']) ?>"
                                        class="thumb"
                                        alt>
                                    <div>
                                        <div class="item-date"><?= date('F d, Y', strtotime($item['tanggal'])) ?> | 15:00 WIB</div>
                                        <h6 class="item-title">
                                            <?= htmlspecialchars($item['judul']) ?>
                                        </h6>
                                        <div class="item-author">
                                            <i class="fa-solid fa-user"></i>
                                            <span><?= htmlspecialchars($item['author_username'] ?? '-') ?></span>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>

                </div>

                <div class="cta-container" style="margin-bottom: 100px">
                    <a href="Berita.php" class="cta-btn">Lihat lebih banyak</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Galeri -->
    <section class="galeri-section py-5">
        <div class="container-fluid p-0">
            <h3 class="fw-bold text-center mb-4 section-title">Galeri</h3>

            <div class="gallery-wrapper d-flex justify-content-center gap-4"
                style="padding: 0 0 40px 0 !important;">

                <?php foreach ($galeriList as $g): ?>
                    <img
                        src="<?= htmlspecialchars('/PBL-SMT-3/Assets/Image/Galeri-Berita/' . $g['file_path']) ?>"
                        class="gallery-img"
                        alt="<?= htmlspecialchars($g['judul']) ?>">
                <?php endforeach; ?>

            </div>

            <div class="cta-container">
                <a href="Galeri.php" class="cta-btn">Lihat lebih banyak</a>
            </div>
        </div>
    </section>

    <section class="partnership-section">
        <div class="partnership-container">

            <div class="partnership-text">
                <h2>Partnership dan Kerjasma</h2>
                <p>
                    Business Analysis Laboratory menjalin kerjasama
                    dengan berbagai mitra
                    industri untuk meningkatkan kualitas penelitian,
                    pengembangan,
                    serta penerapan teknologi analitik.
                </p>
                <div class="cta-container" style="margin-bottom: 50px">
                    <a href="KontakKami.php" class="cta-btn">Bergabung
                        Dengan Kami</a>
                </div>
            </div>

            <div class="partnership-image">
                <img src="../Assets/Image/Homepage/img/Kerjasama.png"
                    alt="Partnership Image">
            </div>
        </div>
    </section>
    <div id="footer"></div>
    <script src="../Assets/Javascript/HeaderFooter.js"></script>
    <script src="../Assets/Javascript/Index.js"></script>
</body>

</html>