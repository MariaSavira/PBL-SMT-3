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
    <link rel="stylesheet" href="../Assets/Css/AnggotaIni.css">
    <link rel="stylesheet" href="../Assets/Css/Index.css">
</head>

<!-- <body>
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
    </body> -->

<body>
    <div id="header"></div>

    <div class="heading">
        <h1>Anggota Laboratorium</h1>
        <p>Anggota Laboratorium Business Analytics merupakan dosen dan peneliti
            yang memiliki keahlian di bidang analisis bisnis<br> berbasis data
            dan berperan aktif dalam kegiatan penelitian, pengabdian, serta
            pengembangan ilmu pengetahuan<br> di lingkungan akademik.</p>
    </div>

    <!-- KARTU PUTIH BESAR -->
    <div class="anggota-wrapper">
        <div class="kepala-lab-card" href="DetailAnggota.html">
            <div class="kepala-lab-photo">
                <!-- GANTI SRC SESUAI FOTO KAMU -->
                <img src="../Assets/Image/AnggotaLab/profile1.png"
                    alt="Kepala Laboratorium">

                <div class="kepala-lab-badge">
                    Kepala Laboratorium
                </div>
            </div>

            <a href="DetailAnggota.php" style="text-decoration: none;">
            <div class="kepala-lab-info">
                <h2 class="kepala-lab-name">
                    Dr. Rakhmat Arianto, S.ST., M.Kom.
                </h2>
                <p class="kepala-lab-desc">
                    Mengampu kuliah di Teknik Informatika dan memiliki minat penelitian
                    pada Natural Language Processing, Data Science, serta penerapan
                    analitik data untuk mendukung pengambilan keputusan di sektor bisnis
                    dan publik.
                </p>

                <hr>

                <div class="kepala-lab-socmed">
                    <a href="#" aria-label="Google Scholar">
                        <i class="fa-solid fa-graduation-cap"></i>
                    </a>
                    <a href="#" aria-label="LinkedIn">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                    <a href="#" aria-label="ResearchGate">
                        <i class="fa-brands fa-researchgate"></i>
                    </a>
                    <a href="#" aria-label="Facebook">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                </div>

                <h3 class="kepala-lab-subtitle">Bidang Keahlian</h3>
                <div class="kepala-lab-tags">
                    <span class="anggota-tag">Natural Language Processing</span>
                    <span class="anggota-tag">Data Science</span>
                </div>
            </div>
        </div>
        </a>

        <!-- JUDUL PARA PENELITI -->
        <div class="section-title anggota-section-title">
            Para Peneliti
        </div>

        <!-- GRID KARTU PENELITI -->
        <div class="peneliti-grid">
            <!-- 1 -->
            <div class="peneliti-card">
                <a href="DetailAnggota.php">
                    <div class="peneliti-photo">
                        <img src="../Assets/Image/AnggotaLab/2.png"
                            alt="Hendra Pradibta, S.E., M.Sc.">
                    </div>
                    <div class="peneliti-body">
                        <h4 class="peneliti-name">Hendra Pradibta, S.E., M.Sc.</h4>
                        <div class="peneliti-tags">
                            <span class="anggota-tag">Manajemen Bisnis</span>
                        </div>
                    </div>
                </a>
            </div>

            <!-- 2 -->
            <div class="peneliti-card">
                <div class="peneliti-photo">
                    <img src="../Assets/Image/AnggotaLab/3.png"
                        alt="Rudy Ariyanto, S.T., M.Cs.">
                </div>
                <div class="peneliti-body">
                    <h4 class="peneliti-name">Rudy Ariyanto, S.T., M.Cs.</h4>
                    <div class="peneliti-tags">
                        <span class="anggota-tag">Data Analytics</span>
                    </div>
                </div>
            </div>

            <!-- 3 -->
            <div class="peneliti-card">
                <div class="peneliti-photo">
                    <img src="../Assets/Image/AnggotaLab/5.png"
                        alt="Ahmadi Yuli Ananta, S.T., M.M.">
                </div>
                <div class="peneliti-body">
                    <h4 class="peneliti-name">Ahmadi Yuli Ananta, S.T., M.M.</h4>
                    <div class="peneliti-tags">
                        <span class="anggota-tag">Enterprise System</span>
                        <span class="anggota-tag">Business Process Reengineering</span>
                    </div>
                </div>
            </div>

            <!-- 4 -->
            <div class="peneliti-card">
                <div class="peneliti-photo">
                    <img src="../Assets/Image/AnggotaLab/4.png"
                        alt="Rokhimatul Wakhidah, S.Pd., M.T.">
                </div>
                <div class="peneliti-body">
                    <h4 class="peneliti-name">Rokhimatul Wakhidah, S.Pd., M.T.</h4>
                    <div class="peneliti-tags">
                        <span class="anggota-tag">IT Governance</span>
                    </div>
                </div>
            </div>

            <!-- 5 -->
            <div class="peneliti-card">
                <div class="peneliti-photo">
                    <img src="../Assets/Image/AnggotaLab/6.png"
                        alt="Candra Bella Vista, S.Kom., M.T.">
                </div>
                <div class="peneliti-body">
                    <h4 class="peneliti-name">Candra Bella Vista, S.Kom., M.T.</h4>
                    <div class="peneliti-tags">
                        <span class="anggota-tag">Natural Language Processing</span>
                        <span class="anggota-tag">Business Intelligence</span>
                    </div>
                </div>
            </div>

            <!-- 6 -->
            <div class="peneliti-card">
                <div class="peneliti-photo">
                    <img src="../Assets/Image/AnggotaLab/7.png"
                        alt="Endah Septa Sintiya, S.Pd., M.Kom.">
                </div>
                <div class="peneliti-body">
                    <h4 class="peneliti-name">Endah Septa Sintiya, S.Pd., M.Kom.</h4>
                    <div class="peneliti-tags">
                        <span class="anggota-tag">Data Driven Decision Making</span>
                    </div>
                </div>
            </div>

            <!-- 7 -->
            <div class="peneliti-card">
                <div class="peneliti-photo">
                    <img src="../Assets/Image/AnggotaLab/8.png"
                        alt="Dhebys Suryani, S.Kom., M.T.">
                </div>
                <div class="peneliti-body">
                    <h4 class="peneliti-name">Dhebys Suryani, S.Kom., M.T.</h4>
                    <div class="peneliti-tags">
                        <span class="anggota-tag">Digital Marketing Analysis</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="footer"></div>

        <script src="../Assets/Javascript/HeaderFooter.js"></script>
</body>

</html>
</body>

</html>