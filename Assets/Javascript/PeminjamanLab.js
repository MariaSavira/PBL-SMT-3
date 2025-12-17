document.getElementById('loanForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    fetch('ProsesPeminjaman.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())    
    .then(data => {
        if (data.status !== 'success') {
            alert("Gagal: " + data.message);
            return;
        }

        document.getElementById('notification').style.display = 'flex';
        form.reset();
    })
    .catch(err => {
        console.error(err);
        alert("Terjadi kesalahan saat mengirim data");
    });
});

document.getElementById('closeNotification').addEventListener('click', () => {
    document.getElementById('notification').style.display = 'none';
});
