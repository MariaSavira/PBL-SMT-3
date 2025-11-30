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
    const kirimBtn    = document.getElementById('btnKirim');

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

    uploadBtn.addEventListener('click', function () {
        fileInput.click();
    });

    let currentFileUrl = null;

    fileInput.addEventListener('change', function () {
        const file = this.files[0];
        if (!file) return;

        if (file.type !== 'application/pdf') {
            alert('Hanya file PDF yang diperbolehkan.');
            this.value = '';
            return;
        }

        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file maksimal 5 MB.');
            this.value = '';
            return;
        }

        fileNameEl.textContent = file.name;
        const sizeMB = file.size / (1024 * 1024);
        fileSizeEl.textContent = sizeMB.toFixed(1) + ' MB';


        if (currentFileUrl) {
            URL.revokeObjectURL(currentFileUrl);
        }
        currentFileUrl = URL.createObjectURL(file);

        fileViewBtn.onclick = function () {
            window.open(currentFileUrl, '_blank');
        };


        filePreview.style.display = 'flex';
    });

    function showNotification(isSuccess) {
        notification.classList.remove('success', 'error');

        if (isSuccess) {
            notification.classList.add('success');
            notifTitleEl.textContent   = 'Success';
            notifMessageEl.textContent = 'Pesan Anda telah berhasil terkirim!';
            notifIconContainer.innerHTML = '<i class="fa-regular fa-circle-check"></i>';
        } else {
            notification.classList.add('error');
            notifTitleEl.textContent   = 'Error';
            notifMessageEl.textContent = 'Terjadi kesalahan. Silakan periksa kembali form Anda.';
            notifIconContainer.innerHTML = '<i class="fa-regular fa-circle-xmark"></i>';
        }

        notification.style.display = 'block';
        overlay.style.display = 'block';

        setTimeout(() => {
            notification.style.display = 'none';
            overlay.style.display = 'none';
        }, 5000);
    }

    closeBtn.addEventListener('click', function () {
        notification.style.display = 'none';
        overlay.style.display = 'none';
    });

    kirimBtn.addEventListener('click', function (e) {
        e.preventDefault();

        const nama     = namaInput.value.trim();
        const instansi = instansiInput.value.trim();
        const alasan   = alasanSelect.value;
        const pesan    = pesanTextarea.value.trim();
        const file     = fileInput.files[0];

        const isFormValid =
            nama !== '' &&
            instansi !== '' &&
            alasan !== '' &&
            alasan !== '-- Pilih Alasan --' &&
            pesan !== '' &&
            !!file; 

        if (!isFormValid) {
            showNotification(false);
            return;
        }

        showNotification(true);

        namaInput.value = '';
        instansiInput.value = '';
        alasanSelect.value = '-- Pilih Alasan --';
        pesanTextarea.value = '';
        fileInput.value = '';
        filePreview.style.display = 'none';

        if (currentFileUrl) {
            URL.revokeObjectURL(currentFileUrl);
            currentFileUrl = null;
        }
    });
});
