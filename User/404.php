<!DOCTYPE html>
<html lang="id">

<?php
    require_once __DIR__ . '/../Admin/Cek_Autentikasi.php';

    if (isset($_SESSION['id_anggota'])) {
        $redirect = "/PBL-SMT-3/Admin/IndexAnggota.php";
    } else if (!isset($_SESSION['id_anggota'])) {
        $redirect = "/PBL-SMT-3/User/Index.php";
    }
?>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Error 404</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
        <link rel="icon" type="images/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />
        <link rel="stylesheet" href="../Assets/Css/404.css">
    </head>
    <body>        
        <main class="container">
            <div class="error-wrapper">
                <section class="error-text">
                    <img src="../Assets/Image/404/2.png" style="max-width: 200px; margin-top: 75px">
                </section>

                <section class="error-image">
                    <img src="../Assets/Image/404/1.png" alt="Penguin 404">
                </section>
            </div>

            <div class="error-wrapper-bottom">
                <section class="error-text">
                    <p class="error-subtitle">Halaman yang Anda cari Tidak Ditemukan</p>
                    <a href="<?=  $redirect ?>" class="error-button">Kembali ke Beranda</a>
                </section>
            </div>
        </main>
        <script src="../Assets/Javascript/HeaderFooter.js"></script>
    </body>
</html>
