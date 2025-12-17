<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require_once 'config.php';

    $status  = $_SESSION['flash_status']  ?? '';
    $message = $_SESSION['flash_message'] ?? '';
    $redirectTo = $_SESSION['flash_redirect'] ?? '';

    unset(
        $_SESSION['flash_status'],
        $_SESSION['flash_message'],
        $_SESSION['flash_redirect']
    );

    $user_name = $_SESSION['username'] ?? '1';

    $defaultStatus = $_SESSION['form_data']['status'] ?? 'publish';
    $statusLabel   = ($defaultStatus === 'draft') ? 'Draft' : 'Publish';

    $defaultTanggal = $_SESSION['form_data']['tanggal'] ?? date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Berita</title>

    <link rel="stylesheet" href="/PBL-SMT-3/Assets/Css/Admin/FormBerita.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap" rel="stylesheet">

    <link rel="icon" type="images/x-icon" href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>
    <div id="sidebar"></div>

    <main class="content" id="content">
        <div id="header"></div>

        <div class="content-header page-header">
            <a href="berita.php" class="back-button">
                <button type="button" class="btn-back">
                    <i class="fa-solid fa-chevron-left"></i>
                    Kembali
                </button>
            </a>

            <h1 class="page-title">Tambah Berita</h1>
            <div></div>
        </div>

        <section class="profile-layout">

            <form action="proses_berita.php" method="POST" enctype="multipart/form-data" id="formBerita" class="profile-form">
                <input type="hidden" name="action" value="tambah">

                <div class="form-card">
                    <h2 class="form-title">Data Berita</h2>
                    <p class="form-subtitle">Lengkapi informasi mengenai berita berikut.</p>

                    <div class="form-grid">

                        <div class="field-group">
                            <label for="judul">Judul</label>
                            <input
                                type="text"
                                id="judul"
                                name="judul"
                                placeholder="Masukkan judul"
                                value="<?= htmlspecialchars($_SESSION['form_data']['judul'] ?? '') ?>"
                                class="field-input"
                                required>
                        </div>

                        <div class="field-group">
                            <label for="tanggal">Tanggal Terbit</label>
                            <input
                                type="date"
                                id="tanggal"
                                name="tanggal"
                                value="<?= htmlspecialchars($defaultTanggal) ?>"
                                class="field-input"
                                required>
                        </div>

                        <!-- STATUS: custom dropdown + hidden input -->
                        <div class="field-group">
                            <label>Status Publikasi</label>

                            <div class="field-select <?= $defaultStatus ? 'filled' : '' ?>" id="statusSelect" data-placeholder="Pilih Status Publikasi">

                                <button type="button" class="dropdown-toggle" aria-expanded="false">
                                    <span class="dropdown-label"><?= htmlspecialchars($statusLabel) ?></span>
                                    <i class="fa-solid fa-chevron-down caret"></i>
                                </button>

                                <div class="dropdown-menu">
                                    <button type="button" class="dropdown-item" data-value="publish">Publish</button>
                                    <button type="button" class="dropdown-item" data-value="draft">Draft</button>
                                </div>

                                <!-- SATU-SATUNYA INPUT STATUS -->
                                <input
                                    type="hidden"
                                    name="status"
                                    id="statusValue"
                                    value="<?= htmlspecialchars($defaultStatus) ?>">
                            </div>
                        </div>

                        <!-- GAMBAR -->
                        <div class="field-group">
                            <label for="gambar">Gambar</label>

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
                                id="gambar"
                                name="gambar"
                                accept="image/*"
                                style="display:none;">
                        </div>

                        <div class="field-group">
                            <label for="isi">Isi Berita</label>
                            <textarea
                                id="isi"
                                name="isi"
                                placeholder="Masukkan Isi Berita"
                                rows="4"
                                class="field-input"
                                required><?= htmlspecialchars($_SESSION['form_data']['isi'] ?? '') ?></textarea>
                        </div>

                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary btn-save" id="submitBtn">
                            Simpan Berita
                            <i class="fa-solid fa-check"></i>
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </main>

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

    <script src="../../../Assets/Javascript/Admin/berita.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const tanggalEl = document.getElementById('tanggal');
            if (tanggalEl && !tanggalEl.value) {
                tanggalEl.valueAsDate = new Date();
            }

            document.querySelectorAll('.field-select').forEach(dropdown => {
                const toggle = dropdown.querySelector('.dropdown-toggle');
                const label = dropdown.querySelector('.dropdown-label');
                const menu = dropdown.querySelector('.dropdown-menu');
                const hidden = dropdown.querySelector('input[name="status"]');

                if (!toggle || !label || !menu || !hidden) return;

                toggle.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    dropdown.classList.toggle('open');
                    toggle.setAttribute('aria-expanded', dropdown.classList.contains('open') ? 'true' : 'false');
                });

                menu.addEventListener('click', (e) => {
                    const item = e.target.closest('.dropdown-item');
                    if (!item) return;

                    hidden.value = item.dataset.value || '';
                    label.textContent = item.textContent.trim();

                    dropdown.classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                    dropdown.classList.toggle('filled', !!hidden.value);
                });

                document.addEventListener('click', () => {
                    dropdown.classList.remove('open');
                    toggle.setAttribute('aria-expanded', 'false');
                });
            });

            const uploadArea = document.getElementById('uploadArea');
            const inputFile = document.getElementById('gambar');
            const preview = document.getElementById('imagePreview');
            const changeBtn = document.getElementById('changeImageBtn');

            function openPicker(e) {
                if (e) e.stopPropagation();
                if (inputFile) inputFile.click();
            }

            if (uploadArea) uploadArea.addEventListener('click', openPicker);
            if (changeBtn) changeBtn.addEventListener('click', openPicker);

            if (inputFile) {
                inputFile.addEventListener('change', function() {
                    if (!this.files || !this.files[0]) return;

                    const file = this.files[0];
                    const allowed = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

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
                        if (preview) {
                            preview.src = e.target.result;
                            preview.classList.add('show');
                        }
                        uploadArea?.classList.add('has-image');
                        changeBtn?.classList.add('show');
                        const uploadContent = uploadArea?.querySelector('.upload-content');
                        if (uploadContent) uploadContent.style.display = 'none';
                    };
                    reader.readAsDataURL(file);
                });
            }

            const isiTextarea = document.getElementById('isi');
            if (isiTextarea) {
                isiTextarea.addEventListener('input', function() {
                    const maxLength = 10000;
                    const currentLength = this.value.length;

                    let counter = document.getElementById('isiCounter');
                    if (!counter) {
                        counter = document.createElement('div');
                        counter.id = 'isiCounter';
                        counter.style.cssText = 'text-align:right;color:#64748b;font-size:13px;margin-top:5px;';
                        this.parentElement.appendChild(counter);
                    }
                    counter.textContent = `${currentLength.toLocaleString()} / ${maxLength.toLocaleString()} karakter`;
                });
            }

            const form = document.getElementById('formBerita');
            const submitBtn = document.getElementById('submitBtn');
            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Menyimpan...';
                });
            }

        });

        function showAlert(message, type = 'error') {
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(a => a.remove());

            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type}`;

            const icon = type === 'error' ?
                '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M15 9l-6 6M9 9l6 6"/></svg>' :
                '<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 6L9 17l-5-5"/></svg>';

            alertDiv.innerHTML = `${icon}<span>${message}</span>`;

            const form = document.getElementById('formBerita');
            if (form && form.parentElement) {
                form.parentElement.insertBefore(alertDiv, form);
            }

            setTimeout(() => {
                alertDiv.style.animation = 'slideUp 0.3s ease';
                setTimeout(() => alertDiv.remove(), 300);
            }, 5000);
        }
    </script>

    <script>
        window.profileStatus = <?= json_encode($status  ?? '') ?>;
        window.profileMessage = <?= json_encode($message ?? '') ?>;
        window.profileRedirectUrl = <?= json_encode($redirectTo) ?>;
    </script>
    <script src="../../../Assets/Javascript/Admin/Profile.js"></script>
    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script src="../../../Assets/Javascript/Admin/Header.js"></script>
</body>

</html>