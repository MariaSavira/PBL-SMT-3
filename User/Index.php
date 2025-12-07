<!DOCTYPE html>
<html lang="id">

<?php
    require_once __DIR__ . '/../Admin/Cek_Autentikasi.php';
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
                <a href="Anggota.html" class="btn-profil">Cari Tahu Lebih
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
            <div class="fokus-card">Anomaly Detection</div>
            <div class="fokus-card">Identity Theft</div>
            <div class="fokus-card">Fraud Detection</div>
            <div class="fokus-card">Brand Image Analysis</div>
            <div class="fokus-card">New Product Development</div>
        </div>
        <div class="fokus-row">
            <div class="fokus-card row2">Competitive Monitoring</div>
            <div class="fokus-card row2">Digital Marketing Analysis</div>
            <div class="fokus-card row2">Supply Chain Analytics</div>
        </div>

        <div class="cta-container" style="margin-bottom: 0px">
            <a href="Riset.html" class="cta-btn">Lihat lebih banyak</a>
        </div>
    </div>

    <section class="news-section py-5">
        <div class="container-l px-5">
            <!-- BERITA -->
            <!-- <section class="news-section"> -->
            <div class="news-container">
                <h3 class="section-title">Berita Terbaru</h3>

                <div class="news-flex">
                    <!-- Berita Utama -->
                    <div class="berita-utama">
                        <img src="../Assets/Image/Galeri-Berita/berita1.jpg"
                            class="big-news-img"
                            alt="big news">

                        <h4 class="judul-utama">
                            Jurusan Teknologi Informasi Politeknik Negeri
                            Malang berhasil meraih juara 2
                            umum pada Kompetensi Mahasiswa Informatika
                            Politeknik Nasional (KMIPN) 2025
                            dengan perolehan 1 emas, 1 perak dan 1 perunggu
                        </h4>

                        <div class="meta-utama">
                            <span>14 November 2025 | 16.51 WIB</span>
                            <span>•</span>
                            <i class="fa-solid fa-user"></i>
                            <span>Surya Dua Artha Simanjuntak</span>
                        </div>

                        <p class="deskripsi-utama">
                            Malang, 17 Oktober 2025 — Jurusan Teknologi
                            Informasi Politeknik Negeri
                            Malang (TI Polinema) kembali mengukir prestasi
                            gemilang...
                        </p>
                    </div>

                    <!-- Berita Kecil -->
                    <div class="berita-list">

                        <div class="berita-item">
                            <img
                                src="../Assets/Image/Galeri-Berita/berita2.png"
                                class="thumb"
                                alt>
                            <div>
                                <div class="item-date">November 13, 2025 |
                                    15:00 WIB</div>
                                <h6 class="item-title">
                                    Jurusan Teknologi Informasi Politeknik
                                    Negeri Malang melaksanakan
                                    kegiatan dengan tema “AI Ready ASEAN
                                    untuk Siswa”
                                </h6>
                                <div class="item-author">
                                    <i class="fa-solid fa-user"></i>
                                    <span>Surya Dua Artha Simanjuntak</span>
                                </div>
                            </div>
                        </div>

                        <div class="berita-item">
                            <img
                                src="../Assets/Image/Galeri-Berita/berita3.png"
                                class="thumb" alt>
                            <div>
                                <div class="item-date">November 13, 2025 |
                                    15:00 WIB</div>
                                <h6 class="item-title">
                                    Jurusan Teknologi Informasi Politeknik
                                    Negeri Malang menerima
                                    kunjungan dari SMA Negeri 3 Kota Malang
                                </h6>
                                <div class="item-author">
                                    <i class="fa-solid fa-user"></i>
                                    <span>Surya Dua Artha Simanjuntak</span>
                                </div>
                            </div>
                        </div>
                        <div class="berita-item">
                            <img
                                src="../Assets/Image/Galeri-Berita/berita4.png"
                                class="thumb"
                                alt>
                            <div>
                                <div class="item-date">November 13, 2025 |
                                    15:00 WIB</div>
                                <h6 class="item-title">
                                    Kolaborasi Jurusan Teknologi Informasi
                                    Politeknik Negeri Malang dan
                                    MGMP Informatika Kab Malang…
                                </h6>
                                <div class="item-author">
                                    <i class="fa-solid fa-user"></i>
                                    <span>Surya Dua Artha Simanjuntak</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="cta-container" style="margin-bottom: 100px">
                    <a href="Berita.html" class="cta-btn">Lihat lebih
                        banyak</a>
                </div>
                <!-- </div> -->
                <!-- </section> -->
            </div>

            <!-- GALERI -->
            <!-- </div>
                </div>
            </div> -->
        </div>
    </section>

    <section class="galeri-section py-5">
        <div class="container-fluid p-0">
            <h3 class="fw-bold text-center mb-4 section-title">Galeri</h3>

            <div class="gallery-wrapper d-flex justify-content-center gap-4"
                style="padding: 0 0 40px 0 !important;">
                <img src="../Assets/Image/Galeri-Berita/berita5.png"
                    class="gallery-img"
                    alt="galeri 1">
                <img src="../Assets/Image/Galeri-Berita/berita4.png"
                    class="gallery-img"
                    alt="galeri 2">
                <img src="../Assets/Image/Galeri-Berita/berita3.png"
                    class="gallery-img"
                    alt="galeri 3">
            </div>

            <div class="cta-container" style="margin-bottom: 50px">
                <a href class="cta-btn">Lihat lebih banyak</a>
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
                    <a href="KontakKami.html" class="cta-btn">Bergabung
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