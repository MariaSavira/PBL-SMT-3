document.addEventListener('DOMContentLoaded', function () {
    const inputFoto = document.getElementById('avatarInput');
    const imgPreview = document.getElementById('avatarPreview');

    if (inputFoto && imgPreview) {
        inputFoto.addEventListener('change', function () {
            const file = this.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                imgPreview.src = e.target.result;
            };
            reader.readAsDataURL(file);
        });
    }

    const notifEl = document.getElementById('notification');
    const notifIconEl = document.getElementById('notification-icon');
    const notifTitleEl = document.getElementById('notification-title');
    const notifMsgEl = document.getElementById('notification-message');
    const btnTutupNotif = document.getElementById('closeNotification');
    const overlay = document.getElementById('overlay');

    if (!notifEl || !notifIconEl || !notifTitleEl || !notifMsgEl || !btnTutupNotif || !overlay) {
        return;
    }

    function tampilNotif(isSuccess, pesan) {
        notifEl.classList.remove('success', 'error');

        if (isSuccess) {
            notifEl.classList.add('success');
            notifTitleEl.textContent = 'Success';
            notifMsgEl.textContent = pesan || 'Perubahan profil berhasil disimpan.';
            notifIconEl.innerHTML = '<i class="fa-regular fa-circle-check"></i>';
        } else {
            notifEl.classList.add('error');
            notifTitleEl.textContent = 'Error';
            notifMsgEl.textContent = pesan || 'Terjadi kesalahan. Silakan coba lagi.';
            notifIconEl.innerHTML = '<i class="fa-regular fa-circle-xmark"></i>';
        }

        notifEl.style.display = 'block';
        overlay.style.display = 'block';

        setTimeout(function () {
            notifEl.style.display = 'none';
            overlay.style.display = 'none';
        }, 5000);
    }

    function hideNotif() {
        notifEl.style.display = 'none';
        overlay.style.display = 'none';
    }

    btnTutupNotif.addEventListener('click', hideNotif);

    const statusFromPhp = window.profileStatus || '';
    const messageFromPhp = window.profileMessage || '';
    const redirectUrl = window.profileRedirectUrl || '';

    if (statusFromPhp === 'success') {
        tampilNotif(true, messageFromPhp);

        setTimeout(() => {
            if (redirectUrl) window.location.href = redirectUrl;
            else window.history.back();
        }, 2000);

        setTimeout(hideNotif, 2000);
    } else if (statusFromPhp === 'error') {
        tampilNotif(false, messageFromPhp);
    }
});