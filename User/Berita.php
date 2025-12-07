<!DOCTYPE html>
<html lang="id">

<?php
    require_once __DIR__ . '/../Admin/Cek_Autentikasi.php';
?>

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="../Assets/Css/berita.css" />
      <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />  
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
            <link rel="icon" type="image/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    />
    <title>Berita Laboratorium</title>
</head>
<body>
    
    <!-- NAVBAR (from JS) -->
    <div id="header"></div>
 
    <!-- HERO -->
    <!-- <section class="hero">
        <img src="../Assets/Image/Galeri-Berita/Heading.png" alt="Hero">
        <div class="hero-overlay"></div>
    <h1 style="font-size: 32px;">Berita Laboratorium</h1>
    <p style="font-size: 16px;">
        Ikuti perkembangan terbaru seputar kegiatan laboratorium, pengumuman penting, 
        dan agenda riset yang sedang berjalan. Halaman ini menjadi pusat informasi bagi 
        anggota, mitra, dan publik yang ingin mengetahui tren dan dinamika analisis 
        bisnis berbasis data.
    </p>
    </section>
     -->

    <div class="heading">
        <h1>Berita Laboratorium</h1>
        <p style="margin-top: 16px; color: #fff;">
            Ikuti perkembangan terbaru seputar kegiatan laboratorium, pengumuman penting, 
            dan agenda riset yang sedang<br> berjalan. Halaman ini menjadi pusat informasi bagi 
            anggota, mitra, dan publik yang ingin mengetahui tren dan dinamika<br> analisis 
            bisnis berbasis data.
        </p>
    </div>

    <!-- HIGHLIGHT BERITA -->
    <section class="highlight-floating">
           <div class="highlight-wrapper">
        <div class="highlight-card">
            <img src="../Assets/Image/Galeri-Berita/highlightberita1.jpeg" class="highlight-img" />

            <div class="highlight-text">
                <h2>
                    Jurusan Teknologi Informasi Politeknik Negeri Malang berhasil meraih juara 2 umum pada 
                    Kompetensi Mahasiswa Informatika Politeknik Nasional (KMIPN) 2025 yang berlangsung 
                    pada tanggal 13 – 16 Oktober 2025 di Politeknik Negeri Padang dengan perolehan 
                    1 emas, 1 perak dan 1 perunggu.
                </h2>

                <a href="../User/Isi_berita.html" class="btn-primary">Baca Selengkapnya</a>
            </div>
        </div>
    </section>
<!-- SECTION BAWAH WRAPPER -->
<section class="content-wrapper">

    <!-- PENGUMUMAN -->
     <div class="pengumuman-wrapper">
    <h3 class="judul-pengumuman">Pengumuman</h3>

    <div class="pengumuman">

        <div class="pengumuman-item">
            <div class="icon">
                <img src="../Assets/Image/Galeri-Berita/icon1.svg" alt="icon">
            </div>
            <div>
                <h4>Pengumuman Persyaratan Bantuan UKT / SPP Tahun 2025</h4>
                <p class="tanggal2">November 10, 2025</p>
            </div>
        </div>

        <div class="pengumuman-item">
            <div class="icon">
                <img src="../Assets/Image/Galeri-Berita/icon2.svg" alt="icon">
            </div>
            <div>
                <h4>BEASISWA UNGGULAN BAGI MASYARAKAT BERPRESTASI DAN PENYANDANG DISABILITAS 2025</h4>
                <p class="tanggal">November 10, 2025</p>
            </div>
        </div>

        <div class="pengumuman-item">
            <div class="icon">
                <img src="../Assets/Image/Galeri-Berita/icon3.svg" alt="icon">
            </div>
            <div>
                <h4>Batas Pendaftaran dan Pelaksanaan Ujian Skripsi Tahap III Tahun Ajaran 2024/2025</h4>
                <p class="tanggal">November 10, 2025</p>
            </div>
        </div>
</div>
    </div>

    <!-- BERITA TERKINI -->
    <div class="berita-terkini">
        <div class="berita-terkini-header">
            <h3>Berita Terkini</h3>
            <div class="filter-wrapper">
    <div class="filter-select">
        <select>
            <option>Tag</option>
            <option>Tag 1</option>
            <option>Tag 2</option>
            <option>Tag 3</option>
        </select>
    </div>

    <div class="filter-select">
        <select>
            <option>Tahun</option>
            <option>2023</option>
            <option>2024</option>
            <option>2025</option>
        </select>
    </div>
</div>

        </div>

        <!-- LIST BERITA -->
    <div class="berita-card">
    <div class="img" style="background-image: url('../Assets/Image/Galeri-Berita/berita1.jpg');"></div>
            <div class="text">
                <p class="tanggal">November 13, 2025 | 15:00 WIB</p>
                <h4>Jurusan Teknologi Informasi Politeknik Negeri Malang melaksanakan kegiatan dengan tema “AI Ready ASEAN untuk Siswa”</h4>
                <p>Jurusan Teknologi Informasi Politeknik Negeri Malang melaksanakan kegiatan “AI Ready ASEAN untuk Siswa” pada Selasa,</p>
                <span class="author">Surya Dua Artha Simanjuntak</span>
            </div>
        </div>

        <div class="berita-card">
            <div class="img" style="background-image: url('../Assets/Image/Galeri-Berita/berita2.png');"></div>
            <div class="text">
                <p class="tanggal">November 13, 2025 | 15:00 WIB</p>
                <h4>Jurusan Teknologi Informasi Politeknik Negeri Malang menerima kunjungan dari SMA Negeri 3 Kota Malang</h4>
                <p>Jurusan Teknologi Informasi Politeknik Negeri Malang menerima kunjungan dari SMA Negeri 3 Kota Malang dalam</p>
                <span class="author">Surya Dua Artha Simanjuntak</span>
            </div>
        </div>

        <div class="berita-card">
            <div class="img" style="background-image: url('../Assets/Image/Galeri-Berita/berita3.png');"></div>
            <div class="text">
                <p class="tanggal">November 13, 2025 | 15:00 WIB</p>
                <h4>Politeknik Negeri Malang Tampilkan Inovasi Digital di Indonesia Creative Cities Network (ICCN) 2025</h4>
                <p>Politeknik Negeri Malang (POLINEMA) melalui Jurusan Teknologi Informasi kembali menorehkan prestasi</p>
                <span class="author">Surya Dua Artha Simanjuntak</span>
            </div>
        </div>

        <div class="berita-card">
           <div class="img" style="background-image: url('../Assets/Image/Galeri-Berita/berita4.png');"></div>
            <div class="text">
                <p class="tanggal">November 13, 2025 | 15:00 WIB</p>
                <h4>Kolaborasi Jurusan Teknologi Informasi Politeknik Negeri Malang dan MGMP Informatika Kab Malang dalam Pengembangan Kompetensi Guru di Era Revolusi Industri 4.0</h4>
                <p>Perkembangan dunia digital dan revolusi industri 4.0 menuntut Indonesia untuk memperkuat keterampilan berpikir kritis</p>
                <span class="author">Surya Dua Artha Simanjuntak</span>
            </div>
        </div>

        <div class="berita-card">
            <div class="img" style="background-image: url('../Assets/Image/Galeri-Berita/berita5.png');"></div>
            <div class="text">
                <p class="tanggal">November 13, 2025 | 15:00 WIB</p>
                <h4>Jurusan Teknologi Informasi Politeknik Negeri Malang melaksanakan kegiatan Yudisium Mid Semester Ganjil Tahun Akademik 2025/2026.</h4>
                <p>Jurusan Teknologi Informasi Politeknik Negeri Malang melaksanakan kegiatan Yudisium Mid Semester Ganjil</p>
                <span class="author">Surya Dua Artha Simanjuntak</span>
            </div>
        </div>

    </div>

</section>

<div id="footer"></div>
<script src="../Assets/Javascript/HeaderFooter.js"></script>
  </body>
</html>