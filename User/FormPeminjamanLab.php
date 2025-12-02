<!DOCTYPE html>
<html lang="id">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Formulir Peminjaman Laboratorium</title>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../Assets/Css/FormPeminjaman.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
        <link rel="icon" type="images/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />
    </head>
    <body>
        <div id="header"></div>
        <div class="heading">
            <h1>Formulir Peminjaman Laboratorium</h1>
            <p>Silahkan isi formulir peminjaman berikut</p>
        </div>

            <div class="form-wrapper">
                <h2 class="text-center mb-4">Formulir Peminjaman</h2>

                <form id="loanForm" action="ProsesPeminjaman.php" method="POST">

                    <div class="form-flex">

                        <div class="form-left">
                            <label class="form-label">Nama
                                <span>*</span></label>
                            <input name="nama_peminjam"
                                class="form-control" placeholder="Nama Lengkap"
                                required>

                            <label class="form-label">Email
                                <span>*</span></label>
                            <input name="email"
                                class="form-control"
                                placeholder="Masukkan Alamat Email" required>

                            <label class="form-label">Instansi
                                <span>*</span></label>
                            <input name="instansi"
                                class="form-control"
                                placeholder="Masukkan Asal Instansi" required>

                            <label class="form-label">Tanggal Digunakan
                                <span>*</span></label>
                            <input type="date" name="tanggal_pakai"
                                class="form-control" required>
                        </div>

                        <div class="form-right">
                            <label class="form-label">Keperluan
                                <span>*</span></label>
                            <textarea name="keperluan"
                                class="form-control textarea-kep"
                                placeholder="Masukkan Keperluan"
                                required></textarea>

                            <div class="maskot-container">
                                <img
                                    src="../Assets/Image/Logo/Maskot Peminjaman.png"
                                    class="maskot" alt="maskot">
                            </div>
                        </div>

                    </div>

                    <div class="submit-section">
                        <button type="submit" class="cta-btn">Kirim</button>
                    </div>
                </form>
            </div>

        </div>

        <!-- NOTIFICATION -->
        <div id="notification" class="notification" style="display:none;">
            <div class="notification-content">

                <div class="notification-icon success">
                    <i class="fa-solid fa-circle-check"></i>
                </div>

                <div class="notification-text">
                    <div class="notification-title">Peminjaman Berhasil
                        Dikirim!</div>
                    <div class="notification-message">Permohonan Anda sedang
                        diproses. Harap periksa email untuk info
                        selanjutnya.</div>
                </div>

                <button id="closeNotification" class="close-btn">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        </div>

        <footer class="footer">
            <div class="footer-logo">
                <img src="../Assets/Image/Logo/Logo With Text.png"
                    style="width: 178px; height: 91px" />
            </div>

            <ul class="footer-menu">
                <li>Tentang Kami</li>
                <li>Anggota</li>
                <li>Publikasi</li>
                <li>Riset</li>
                <li>Resource</li>
                <li>Berita</li>
                <li>Kontak Kami</li>
                <li>Login</li>
            </ul>

            <div class="footer-social">
                <i class="fab fa-facebook"></i>
                <i class="fas fa-envelope"></i>
                <i class="fab fa-twitter"></i>
                <i class="fab fa-instagram"></i>
            </div>

            <p class="footer-copyright">
                © 2025 Tim PBL Business Analyst SIB 2C
            </p>
        </footer>

        <script src="../Assets/Javascript/HeaderFooter.js"></script>
         <script src="../Assets/Javascript/PeminjamanLab.js"></script>
    
    </body>
</html>
