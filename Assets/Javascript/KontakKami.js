// Assets/Javascript/KontakKami.js

// Navbar scroll
window.addEventListener("scroll", function () {
    const navbar = document.getElementById("navbar");
    if (!navbar) return;

    if (window.scrollY > 20) {
        navbar.classList.add("navbar-scrolled");
    } else {
        navbar.classList.remove("navbar-scrolled");
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const fileInput   = document.getElementById('fileInput');
    const uploadBtn   = document.getElementById('uploadTrigger');
    const filePreview = document.getElementById('filePreview');
    const fileNameEl  = document.getElementById('fileName');
    const fileSizeEl  = document.getElementById('fileSize');
    const fileViewBtn = document.getElementById('fileViewBtn');
    const kirimBtn    = document.getElementById('btnKirim') || document.getElementById('btn-kirim');

    const namaInput     = document.getElementById('nama');
    const instansiInput = document.getElementById('instansi');
    const alasanSelect  = document.getElementById('alasan');
    const pesanTextarea = document.getElementById('pesan');

    const notification       = document.getElementById('notification');
    const notifIconContainer = document.getElementById('notification-icon');
    const notifTitleEl       = document.getElementById('notification-title');
    const notifMessageEl     = document.getElementById('notification-message');
    const closeBtn           = document.getElementById('closeNotification');
    const overlay            = document.getElementById('overlay');

    let currentFileUrl = null;

    /* ========= FUNGSI NOTIFIKASI ========= */
    function showNotification(isSuccess, customMessage) {
        if (!notification) return;

        notification.classList.remove('success', 'error');

        if (isSuccess) {
            notification.classList.add('success');
            notifTitleEl.textContent   = 'Success';
            notifMessageEl.textContent = customMessage || 'Pesan Anda telah berhasil terkirim!';
            notifIconContainer.innerHTML = '<i class="fa-regular fa-circle-check"></i>';
        } else {
            notification.classList.add('error');
            notifTitleEl.textContent   = 'Error';
            notifMessageEl.textContent = customMessage || 'Terjadi kesalahan. Silakan periksa kembali form Anda.';
            notifIconContainer.innerHTML = '<i class="fa-regular fa-circle-xmark"></i>';
        }

        notification.style.display = 'block';
        overlay.style.display      = 'block';

        setTimeout(() => {
            notification.style.display = 'none';
            overlay.style.display      = 'none';
        }, 5000);
    }

    /* ========= CLOSE NOTIF ========= */
    if (closeBtn) {
        closeBtn.addEventListener('click', function () {
            notification.style.display = 'none';
            overlay.style.display      = 'none';
        });
    }

    /* ========= TRIGGER INPUT FILE ========= */
    if (uploadBtn && fileInput) {
        uploadBtn.addEventListener('click', function () {
            fileInput.click();
        });
    }

    /* ========= PREVIEW & VALIDASI FILE ========= */
    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            // tipe harus PDF
            if (file.type !== 'application/pdf') {
                showNotification(false, 'Hanya file PDF yang diperbolehkan.');
                this.value = '';
                filePreview.style.display = 'none';
                return;
            }

            // maksimal 5 MB
            if (file.size > 5 * 1024 * 1024) {
                showNotification(false, 'Ukuran file maksimal 5 MB.');
                this.value = '';
                filePreview.style.display = 'none';
                return;
            }

            // tampilkan preview
            fileNameEl.textContent = file.name;
            const sizeMB = file.size / (1024 * 1024);
            fileSizeEl.textContent = sizeMB.toFixed(1) + ' MB';

            if (currentFileUrl) {
                URL.revokeObjectURL(currentFileUrl);
            }
            currentFileUrl = URL.createObjectURL(file);

            if (fileViewBtn) {
                fileViewBtn.onclick = function () {
                    window.open(currentFileUrl, '_blank');
                };
            }

            filePreview.style.display = 'flex';
        });
    }

    /* ========= VALIDASI KETIKA KIRIM (FRONTEND) ========= */
    if (kirimBtn) {
        kirimBtn.addEventListener('click', function (e) {
            e.preventDefault();

            const nama     = namaInput ? namaInput.value.trim() : '';
            const instansi = instansiInput ? instansiInput.value.trim() : '';
            const alasan   = alasanSelect ? alasanSelect.value : '';
            const pesan    = pesanTextarea ? pesanTextarea.value.trim() : '';

            const isFormValid =
                nama !== '' &&
                instansi !== '' &&
                alasan !== '' &&
                alasan !== '-- Pilih Alasan --' &&
                pesan !== '';

            if (!isFormValid) {
                showNotification(false, 'Terjadi kesalahan. Silakan periksa kembali form Anda.');
                return;
            }

            showNotification(true);

            if (namaInput)     namaInput.value = '';
            if (instansiInput) instansiInput.value = '';
            if (alasanSelect)  alasanSelect.value = '-- Pilih Alasan --';
            if (pesanTextarea) pesanTextarea.value = '';
            if (fileInput)     fileInput.value = '';
            if (filePreview)   filePreview.style.display = 'none';

            if (currentFileUrl) {
                URL.revokeObjectURL(currentFileUrl);
                currentFileUrl = null;
            }

            // kalau nanti mau submit beneran:
            // const form = kirimBtn.closest('form');
            // if (form) form.submit();
        });
    }
});
