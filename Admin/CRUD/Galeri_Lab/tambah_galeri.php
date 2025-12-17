<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require_once 'config.php';

$user_name = $_SESSION['nama'] ?? ($_SESSION['username'] ?? '');

$defaultTanggal = $_SESSION['form_data']['tanggal_upload'] ?? date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Galeri</title>

    <link rel="stylesheet" href="/PBL-SMT-3/Assets/Css/Admin/FormBerita.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet">
    <link rel="icon" type="images/x-icon" href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div id="sidebar"></div>

    <main class="content" id="content">
        <div id="header"></div>

        <div class="content-header page-header">
            <a href="galeri.php" class="back-button">
                <button type="button" class="btn-back">
                    <i class="fa-solid fa-chevron-left"></i>
                    Kembali
                </button>
            </a>

            <h1 class="page-title">Tambah Galeri</h1>
            <div></div>
        </div>

        <section class="profile-layout">
            <form id="formGaleri" method="POST" action="proses_galeri.php" enctype="multipart/form-data" class="profile-form">
                <input type="hidden" name="action" value="tambah">

                <div class="form-card">
                    <h2 class="form-title">Data Galeri</h2>
                    <p class="form-subtitle">Lengkapi informasi mengenai galeri berikut.</p>

                    <div class="form-grid">
                        
                        <div class="field-group">
                            <label for="judul">Judul</label>
                            <input
                                type="text"
                                id="judul"
                                name="judul"
                                placeholder="Masukkan judul foto"
                                class="field-input"
                                value="<?= htmlspecialchars($_SESSION['form_data']['judul'] ?? '') ?>"
                                required
                            >
                        </div>

                        <div class="field-group">
                            <label for="tanggal_upload">Tanggal Terbit</label>
                            <input
                                type="date"
                                id="tanggal_upload"
                                name="tanggal_upload"
                                class="field-input"
                                value="<?= htmlspecialchars($defaultTanggal) ?>"
                                required
                            >
                        </div>

                        <div class="field-group">
                            <label for="uploaded_by">Author</label>
                            <input
                                type="text"
                                placeholder="Masukkan nama author"
                                class="field-input"
                                value="<?= htmlspecialchars($_SESSION['form_data']['uploaded_by'] ?? $user_name) ?>"
                                disabled
                            >
                            <input
                                type="hidden"
                                id="uploaded_by"
                                name="uploaded_by"
                                class="field-input"
                                value="<?= htmlspecialchars($_SESSION['id_anggota']) ?>"
                            >
                        </div>

                        <div class="field-group">
                            <label for="tipe_media">Tipe Media</label>
                            <select id="tipe_media" name="tipe_media" class="field-input" required>
                                <?php $tipe = $_SESSION['form_data']['tipe_media'] ?? 'foto'; ?>
                                <option value="foto"  <?= $tipe === 'foto' ? 'selected' : '' ?>>Foto</option>
                                <option value="video" <?= $tipe === 'video' ? 'selected' : '' ?>>Video</option>
                            </select>
                        </div>

                        <div class="field-group">
                            <label for="fileInput">Gambar</label>

                            <div class="upload-area" id="uploadArea">
                                <div class="upload-content">
                                    <div class="upload-icon">
                                        <svg width="40" height="40" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                            <circle cx="12" cy="13" r="4" />
                                        </svg>
                                    </div>
                                    <p><strong>Tambahkan Foto</strong></p>
                                    <p style="font-size: 13px; color: #94a3b8;">Format: JPG, PNG, GIF (Max 5MB)</p>
                                </div>

                                <img id="imagePreview" class="image-preview" alt="Preview">

                                <button type="button" class="change-image-btn" id="changeImageBtn">
                                    <svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z" />
                                        <circle cx="12" cy="13" r="4" />
                                    </svg>
                                </button>
                            </div>

                            <input
                                type="file"
                                id="fileInput"
                                name="file"
                                accept="image/*"
                                style="display:none;"
                                required
                            >
                        </div>

                        <div class="field-group">
                            <label for="deskripsi">Deskripsi</label>
                            <textarea
                                id="deskripsi"
                                name="deskripsi"
                                placeholder="Masukkan deskripsi"
                                rows="4"
                                class="field-input"
                                required
                            ><?= htmlspecialchars($_SESSION['form_data']['deskripsi'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary btn-save" id="submitBtn">
                            Simpan Galeri
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function () {

        const tgl = document.getElementById('tanggal_upload');
        if (tgl && !tgl.value) tgl.valueAsDate = new Date();

        const uploadArea = document.getElementById('uploadArea');
        const inputFile  = document.getElementById('fileInput');
        const preview    = document.getElementById('imagePreview');
        const changeBtn  = document.getElementById('changeImageBtn');

        function openPicker(e){
            if (e) e.stopPropagation();
            inputFile?.click();
        }

        uploadArea?.addEventListener('click', openPicker);
        changeBtn?.addEventListener('click', openPicker);

        inputFile?.addEventListener('change', function () {
            if (!this.files || !this.files[0]) return;

            const file = this.files[0];
            const allowed = ['image/jpeg','image/jpg','image/png','image/gif'];

            if (!allowed.includes(file.type)) {
                alert('Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.');
                this.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Ukuran file terlalu besar. Maksimal 5MB.');
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                preview.src = e.target.result;
                preview.classList.add('show');
                uploadArea.classList.add('has-image');
                changeBtn.classList.add('show');
                const uploadContent = uploadArea.querySelector('.upload-content');
                if (uploadContent) uploadContent.style.display = 'none';
            };
            reader.readAsDataURL(file);
        });

        const form = document.getElementById('formGaleri');
        const submitBtn = document.getElementById('submitBtn');
        form?.addEventListener('submit', function () {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';
        });
    });
    </script>

    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script src="../../../Assets/Javascript/Admin/Header.js"></script>
</body>
</html>
