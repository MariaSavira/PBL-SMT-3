<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require_once __DIR__ . '/config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    header('Location: pengumuman.php');
    exit;
}

$currentStatus = 'Aktif';
$statusLabel   = 'Aktif';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Pengumuman</title>

    <link rel="stylesheet" href="/PBL-SMT-3/Assets/Css/Admin/FormPublikasi.css">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet"
    >
    <link rel="icon" type="images/x-icon" href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        .field-input.textarea-isi { min-height: 180px; resize: vertical; }
        .helper-text { display:block; margin-top:6px; font-size:12px; opacity:.75; }
        .required { color: #dc2626; }
        .is-loading { opacity: .7; pointer-events: none; }
    </style>
</head>

<body>
<div id="sidebar"></div>

<main class="content" id="content">
    <div id="header"></div>

    <div class="content-header page-header">
        <a href="pengumuman.php">
            <button type="button" class="btn-back">
                <i class="fa-solid fa-chevron-left"></i>
                Kembali
            </button>
        </a>

        <h1 class="page-title">Edit Pengumuman</h1>
        <div></div>
    </div>

    <section class="profile-layout">
        <form class="profile-form" id="pengumumanForm" autocomplete="off">
            <div class="form-card">
                <h2 class="form-title">Data Pengumuman</h2>
                <p class="form-subtitle">Perbarui informasi pengumuman berikut.</p>

                <div class="form-grid">
                    
                    <div class="field-group">
                        <label for="tanggal_terbit">Tanggal Terbit <span class="required">*</span></label>
                        <input
                            type="date"
                            id="tanggal_terbit"
                            name="tanggal_terbit"
                            class="field-input"
                            required
                        >
                        <small class="helper-text">Ubah tanggal terbit bila diperlukan.</small>
                    </div>
                    
                    <div class="field-group">
                        <label for="statusValue">Status</label>

                        <div class="field-select filled" id="statusSelect" data-placeholder="Pilih Status">
                            <button type="button" class="dropdown-toggle" aria-haspopup="listbox" aria-expanded="false">
                                <span class="dropdown-label"><?= htmlspecialchars($statusLabel) ?></span>
                                <i class="fa-solid fa-chevron-down caret"></i>
                            </button>

                            <div class="dropdown-menu">
                                <button type="button" class="dropdown-item" data-value="Aktif">Aktif</button>
                                <button type="button" class="dropdown-item" data-value="Nonaktif">Nonaktif</button>
                            </div>

                            
                            <input type="hidden" name="status" id="statusValue" value="<?= htmlspecialchars($currentStatus) ?>">
                        </div>

                        <small class="helper-text">Pilih status pengumuman.</small>
                    </div>

                    
                    <div class="field-group" style="grid-column: 1 / -1;">
                        <label for="isi">Isi Pengumuman <span class="required">*</span></label>
                        <textarea
                            id="isi"
                            name="isi"
                            class="field-input textarea-isi"
                            placeholder="Tulis isi pengumuman..."
                            required
                        ></textarea>
                        <small class="helper-text">Tulis ringkas, jelas, dan mudah dipahami.</small>
                    </div>
                </div>

                <input type="hidden" id="id_pengumuman" name="id_pengumuman" value="<?= (int)$id ?>">
                <input type="hidden" id="action" name="action" value="edit">

                <div class="form-actions">
                    <button type="submit" class="btn-primary btn-save" id="submitBtn">
                        Simpan Perubahan
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
        <button id="closeNotification" class="close-btn" type="button">
            <i class="fa-solid fa-xmark"></i>
        </button>
    </div>
</div>
<div id="overlay" class="overlay" style="display:none;"></div>

<script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
<script src="../../../Assets/Javascript/Admin/Header.js"></script>

<script>
    function showNotif(type, title, message) {
        const notif   = document.getElementById('notification');
        const overlay = document.getElementById('overlay');

        const iconEl  = document.getElementById('notification-icon');
        const titleEl = document.getElementById('notification-title');
        const msgEl   = document.getElementById('notification-message');

        const okIcon  = '<i class="fa-solid fa-circle-check"></i>';
        const errIcon = '<i class="fa-solid fa-triangle-exclamation"></i>';

        iconEl.innerHTML = (type === 'success') ? okIcon : errIcon;
        titleEl.textContent = title || (type === 'success' ? 'Berhasil' : 'Gagal');
        msgEl.textContent = message || '';

        notif.style.display = 'block';
        overlay.style.display = 'block';

        notif.classList.remove('success', 'error');
        notif.classList.add(type === 'success' ? 'success' : 'error');
    }

    function closeNotif() {
        document.getElementById('notification').style.display = 'none';
        document.getElementById('overlay').style.display = 'none';
    }

    document.getElementById('closeNotification').addEventListener('click', closeNotif);
    document.getElementById('overlay').addEventListener('click', closeNotif);

    async function fetchJsonSafe(url, options) {
        const res = await fetch(url, options);
        const text = await res.text();
        try { return JSON.parse(text); }
        catch (e) { throw new Error("Response bukan JSON. Cuplikan: " + text.slice(0, 200)); }
    }

    document.addEventListener('DOMContentLoaded', function () {
        const dropdown = document.getElementById('statusSelect');
        if (dropdown) {
            const toggle = dropdown.querySelector('.dropdown-toggle');
            const label = dropdown.querySelector('.dropdown-label');
            const menu = dropdown.querySelector('.dropdown-menu');
            const hidden = document.getElementById('statusValue');

            toggle?.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                dropdown.classList.toggle('open');
                toggle.setAttribute('aria-expanded', dropdown.classList.contains('open') ? 'true' : 'false');
            });

            menu?.addEventListener('click', (e) => {
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
                toggle?.setAttribute('aria-expanded', 'false');
            });
        }
    });

    const editId = <?= (int)$id ?>;

    function setStatusUI(val) {
        const hidden = document.getElementById('statusValue');
        const dropdown = document.getElementById('statusSelect');
        const label = dropdown?.querySelector('.dropdown-label');

        const status = (val && String(val).trim()) ? String(val).trim() : 'Aktif';

        hidden.value = status;
        if (label) label.textContent = status;

        dropdown?.classList.toggle('filled', !!hidden.value);
    }

    async function loadEditData() {
        try {
            const data = await fetchJsonSafe(`proses_pengumuman.php?action=get_data&id=${editId}`, { method: 'GET' });

            if (!data.success) {
                showNotif('error', 'Gagal', data.message || 'Data tidak ditemukan');
                setTimeout(() => window.location.href = 'pengumuman.php', 900);
                return;
            }

            const row = data.data || {};

            document.getElementById('tanggal_terbit').value = (row.tanggal_terbit || '').slice(0, 10);
            document.getElementById('isi').value = row.isi || '';

            setStatusUI(row.status || 'Aktif');

        } catch (err) {
            showNotif('error', 'Error', err.message);
            setTimeout(() => window.location.href = 'pengumuman.php', 1200);
        }
    }

    document.getElementById('pengumumanForm').addEventListener('submit', async function(e){
        e.preventDefault();

        const tanggal = document.getElementById('tanggal_terbit').value;
        const isi     = document.getElementById('isi').value.trim();
        const status  = document.getElementById('statusValue').value;

        if (!tanggal) { showNotif('error','Validasi','Tanggal terbit harus diisi.'); return; }
        if (!isi)     { showNotif('error','Validasi','Isi pengumuman harus diisi.'); return; }
        if (!status)  { showNotif('error','Validasi','Status harus dipilih.'); return; }

        const submitBtn = document.getElementById('submitBtn');
        const card = document.querySelector('.form-card');
        submitBtn.disabled = true;
        card.classList.add('is-loading');

        try {
            const formData = new FormData(this);
            formData.set('action', 'edit');
            formData.set('id_pengumuman', String(editId));

            const result = await fetchJsonSafe('proses_pengumuman.php', {
                method: 'POST',
                body: formData
            });

            if (result.success) {
                showNotif('success', 'Berhasil', result.message || 'Pengumuman berhasil diupdate.');
                setTimeout(() => window.location.href = 'pengumuman.php', 900);
            } else {
                showNotif('error', 'Gagal', result.message || 'Terjadi kesalahan.');
            }

        } catch (err) {
            showNotif('error', 'Error', err.message);
        } finally {
            submitBtn.disabled = false;
            card.classList.remove('is-loading');
        }
    });

    loadEditData();
</script>
</body>
</html>
