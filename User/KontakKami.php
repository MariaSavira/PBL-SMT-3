<?php
    require_once __DIR__ . '../../Admin/Cek_Autentikasi.php';
    require __DIR__ . '../../Admin/Koneksi/KoneksiNajwa.php';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Kontak Kami</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css">
    <link rel="icon" type="image/png" href="../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="../Assets/Css/KontakKami.css">
</head>

<body>
    <div id="header"></div>

    <!-- HEADING -->
    <div class="heading">
        <h1>Kontak Kami</h1>
        <p>Jika memiliki pertanyaan atau ingin berkomunikasi dengan kami,
            Anda dapat mengisi formulir di bawah ini, dan kami akan segera merespons.</p>
    </div>

    <div class="container">
        <div class="card">

            <!-- KIRI -->
            <div class="left">
                <h2>Hubungi Kami</h2>
                <p>Silakan tinggalkan pesan atau pertanyaan Anda melalui
                    formulir di bawah ini.</p>

                <!-- kirim.php ada di luar folder User, jadi pakai ../ -->
                <form action="../kirim.php" method="POST" enctype="multipart/form-data">

                    <label for="nama">Nama Lengkap</label>
                    <input
                        id="nama"
                        name="nama"
                        type="text"
                        placeholder="Masukkan nama lengkap Anda"
                        required>

                    <label for="instansi">Instansi</label>
                    <input
                        id="instansi"
                        name="instansi"
                        type="text"
                        placeholder="Instansi">

                    <label for="alasan">Alasan</label>
                    <select id="alasan" name="alasan" required>
                        <option selected disabled>-- Pilih Alasan --</option>
                        <option value="Informasi">Informasi</option>
                        <option value="Pendaftaran">Pendaftaran</option>
                        <option value="Kerja Sama">Kerja Sama</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>

                    <label for="pesan">Isi Pesan</label>
                    <textarea
                        id="pesan"
                        name="pesan"
                        placeholder="Tuliskan pesan atau pertanyaan Anda di sini"
                        required></textarea>

                    <label for="fileInput">Upload Dokumen</label>
                    <button type="button" class="upload-btn" id="uploadTrigger">
                        <i class="fa-solid fa-upload"></i>
                        <span>Upload File</span>
                    </button>

                    <!-- FILE INPUT HARUS PUNYA NAME & ADA DI DALAM FORM -->
                    <input
                        type="file"
                        id="fileInput"
                        name="dokumen"
                        accept="application/pdf"
                        hidden>

                    <p class="upload-help">Format file: PDF • Maksimal 5 MB</p>

                    <div id="filePreview" class="file-card" style="display:none;">
                        <div class="file-left">
                            <div class="file-icon">
                                <i class="fa-solid fa-file-pdf"></i>
                            </div>
                            <div class="file-text">
                                <div class="file-name" id="fileName"></div>
                                <div class="file-size" id="fileSize"></div>
                            </div>
                        </div>
                        <button type="button" class="file-link" id="fileViewBtn">Lihat</button>
                    </div>

                    <!-- BUTTON KIRIM -->
                    <button type="submit" class="btn" id="btn-kirim">Kirim Pesan</button>
                </form>
            </div> <!-- end .left -->

            <!-- KANAN -->
            <div class="right">
                <h2>Informasi Kontak</h2>
                <p class="sub">Kami menyediakan beberapa opsi kontak untuk
                    memudahkan Anda terhubung dengan tim kami secara cepat dan efisien.</p>

                <div class="info">
                    <div class="item">
                        <div class="icon"><i class="fa-brands fa-whatsapp"></i></div>
                        <div>
                            <div class="item-title">WhatsApp</div>
                            <div class="item-desc">+6285730666454</div>
                        </div>
                    </div>

                    <div class="item">
                        <div class="icon"><i class="fa-solid fa-envelope"></i></div>
                        <div>
                            <div class="item-title">Alamat Email</div>
                            <div class="item-desc">jti@polienema.ac.id</div>
                        </div>
                    </div>

                    <div class="item">
                        <div class="icon"><i class="fa-solid fa-clock"></i></div>
                        <div>
                            <div class="item-title">Jam Operasional</div>
                            <div class="item-desc">Senin - Jumat, 07.00 - 16.00</div>
                        </div>
                    </div>

                    <div class="item">
                        <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                        <div>
                            <div class="item-title">Lokasi</div>
                            <div class="item-desc">
                                Gedung Teknik Sipil, Politeknik Negeri Malang<br>
                                Lab BA, Lantai 8
                            </div>
                        </div>
                    </div>
                </div>

                <div class="map">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3951.5304465643894!2d112.6145444!3d-7.9440069!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd629dfd58aaf95%3A0xe72a182dfd18e01c!2sGedung%20Teknik%20Sipil%2C%20Teknik%20Informatika%20%26%20Magister%20Terapan%2C%20POLITEKNIK%20NEGERI%20MALANG!5e0!3m2!1sid!2sid!4v1763884713528!5m2!1sid!2sid"
                        loading="lazy" allowfullscreen></iframe>
                </div>
            </div> <!-- end .right -->

        </div> <!-- end .card -->
    </div> <!-- end .container -->

    <!-- NOTIFICATION -->
    <div id="notification" class="notification" style="display:none;">
        <div class="notification-content">
            <div class="notification-icon" id="notification-icon"></div>
            <div class="notification-text">
                <div class="notification-title" id="notification-title"></div>
                <div class="notification-message" id="notification-message"></div>
            </div>
            <button id="closeNotification" class="close-btn">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    </div>
    <div id="overlay" class="overlay" style="display:none;"></div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const fileInput   = document.getElementById('fileInput');
            const uploadBtn   = document.getElementById('uploadTrigger');
            const filePreview = document.getElementById('filePreview');
            const fileNameEl  = document.getElementById('fileName');
            const fileSizeEl  = document.getElementById('fileSize');
            const fileViewBtn = document.getElementById('fileViewBtn');

            const notification       = document.getElementById('notification');
            const notifIconContainer = document.getElementById('notification-icon');
            const notifTitleEl       = document.getElementById('notification-title');
            const notifMessageEl     = document.getElementById('notification-message');
            const closeBtn           = document.getElementById('closeNotification');
            const overlay            = document.getElementById('overlay');

            // Trigger file input
            uploadBtn.addEventListener('click', function () {
                fileInput.click();
            });

            let currentFileUrl = null;

            // Fungsi notifikasi (success / error)
            function showNotification(isSuccess, customMessage) {
                notification.classList.remove('success', 'error');

                if (isSuccess) {
                    notification.classList.add('success');
                    notifTitleEl.textContent   = 'Success';
                    notifMessageEl.textContent = customMessage || 'Pesan Anda telah berhasil terkirim!';
                    notifIconContainer.innerHTML = '<i class="fa-regular fa-circle-check"></i>';
                } else {
                    notification.classList.add('error');
                    notifTitleEl.textContent   = 'Error';
                    notifMessageEl.textContent = customMessage || 'Terjadi kesalahan. Silakan coba lagi.';
                    notifIconContainer.innerHTML = '<i class="fa-regular fa-circle-xmark"></i>';
                }

                notification.style.display = 'block';
                overlay.style.display      = 'block';

                setTimeout(() => {
                    notification.style.display = 'none';
                    overlay.style.display      = 'none';
                }, 5000);
            }

            // Preview file PDF + validasi
            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;

                // Hanya PDF
                if (file.type !== 'application/pdf') {
                    showNotification(false, 'Hanya file PDF yang diperbolehkan.');
                    this.value = '';
                    filePreview.style.display = 'none';
                    return;
                }

                // Maksimal 5 MB
                if (file.size > 5 * 1024 * 1024) {
                    showNotification(false, 'Ukuran file maksimal 5 MB.');
                    this.value = '';
                    filePreview.style.display = 'none';
                    return;
                }

                // Tampilkan preview
                fileNameEl.textContent = file.name;
                const sizeMB = file.size / (1024 * 1024);
                fileSizeEl.textContent = sizeMB.toFixed(1) + ' MB';

                // revoke URL lama kalau ada
                if (currentFileUrl) {
                    URL.revokeObjectURL(currentFileUrl);
                }
                currentFileUrl = URL.createObjectURL(file);

                fileViewBtn.onclick = function () {
                    window.open(currentFileUrl, '_blank');
                };

                filePreview.style.display = 'flex';
            });

            // Tutup notif manual
            closeBtn.addEventListener('click', function () {
                notification.style.display = 'none';
                overlay.style.display      = 'none';
            });

            // BACA STATUS DARI URL (?status=...)
            const params = new URLSearchParams(window.location.search);
            const status = params.get('status');

            if (status === 'success') {
                showNotification(true);
            } else if (status === 'error') {
                showNotification(false, 'Terjadi kesalahan saat mengirim pesan. Silakan coba lagi.');
            } else if (status === 'validation_error') {
                showNotification(false, 'Data yang dikirim belum lengkap atau tidak valid.');
            }
        });
    </script>

    <div id="footer"></div>
    <script src="../Assets/Javascript/HeaderFooter.js"></script>
</body>
</html>
