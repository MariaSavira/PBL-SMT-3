<!DOCTYPE html>
<html lang="id">

<?php
    require_once __DIR__ . '/../Admin/Cek_Autentikasi.php';
?>

    <head> 
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Anggota Laboratorium</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link
            href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
            rel="stylesheet">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
        <link rel="icon" type="images/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />
        <link rel="stylesheet" href="../Assets/Css/Anggota.css">
    </head>

    <body>
        <div id="header"></div>

        <div class="heading">
            <h1>Anggota Laboratorium</h1>
            <p>Anggota Laboratorium Business Analytics merupakan dosen-dosen yang memiliki keahlian di bidang analisis bisnis<br>
                berbasis data dan berperan aktif dalam kegiatan penelitian, pengabdian, serta pengembangan ilmu pengetahuan<br>
                di lingkungan akademik.</p>
        </div>

        <div class="diagram-container">
            <div class="kepala-lab">
                <img class="section-kiri" src="../Assets/Image/AnggotaLab/Pak Anto.png">
                <label class="jabatan">Kepala Laboratorium</label>
                
                <div class="section-kanan">
                    <h1>Dr. Rakhmat Arianto, S.ST., M.Kom.</h1>
                    <p>Merupakan lulusan D4 Teknik Informatika PENS-ITS tahun 2009 dengan IPK 2,91
                        dan Magister Teknik Informatika ITS pada 17 Maret
                        2013 dengan IPK 3,58. Spesialisasi dalam Rekayasa Perangkat Lunak, 
                        dengan pengalaman kerja di PT Inti Eka Fajar Konsultan sejak 
                        Agustus 2009 - April 2013. Menguasai bahasa Inggris dengan baik.</p>

                    <div class="sosmed">
                        <img src="../Assets/Image/AnggotaLab/Google Scholar.png">
                        <img src="../Assets/Image/AnggotaLab/Linkedin.png">
                        <img src="../Assets/Image/AnggotaLab/Research Gate.png">
                        <img src="../Assets/Image/AnggotaLab/Facebook.png">
                    </div>
                </div>
            </div>
        </div>

        <div id="footer"></div>

        <script src="../Assets/Javascript/HeaderFooter.js"></script>
    </body>
</html>