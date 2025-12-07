<!DOCTYPE html>
<html lang="id">
<?php
    require_once __DIR__ . '/../Admin/Cek_Autentikasi.php';
?>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Galeri Laboratorium</title>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
            <link rel="icon" type="image/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    />
    <!-- CSS terpisah -->
    <link rel="stylesheet" href="../Assets/Css/galeri.css" />
  </head>

  <body>
    <div id="header"></div>

    <!-- HERO HEADING -->
    <section class="heading">
      <h1>Galeri Laboratorium</h1>
      <p>
        jelajahi berbagai dokumentasi kegiatan, riset, dan momen penting yang
        terjadi di Laboratorium Business Analytics
      </p>
    </section>

    <!-- GALLERY -->
    <div class="gallery-wrapper">
      <div class="gallery-row">
        <img
          src="../Assets/Image/Galeri-Berita/berita1.jpg"
          class="gallery-img card-385"
        />
        <img
          src="../Assets/Image/Galeri-Berita/gambar2.jpeg"
          class="gallery-img card-385"
        />
        <img
          src="../Assets/Image/Galeri-Berita/berita2.png"
          class="gallery-img card-385"
        />

        <img
          src="../Assets/Image/Galeri-Berita/gambar4.jpeg"
          class="gallery-img card-338"
        />
        <img
          src="../Assets/Image/Galeri-Berita/berita3.png"
          class="gallery-img card-496"
        />
        <img
          src="../Assets/Image/Galeri-Berita/berita4.png"
          class="gallery-img card-338"
        />

        <img
          src="../Assets/Image/Galeri-Berita/berita5.png"
          class="gallery-img card-438"
        />
        <img
          src="../Assets/Image/Galeri-Berita/gambar8.jpeg"
          class="gallery-img card-239"
        />
        <img
          src="../Assets/Image/Galeri-Berita/highlightberita1.jpeg"
          class="gallery-img card-438"
        />
      </div>
    </div>

    <div id="footer"></div>
    <script src="../Assets/Javascript/HeaderFooter.js"></script>
  </body>
</html>
