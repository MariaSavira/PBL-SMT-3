document.getElementById('loanForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    fetch('ProsesPeminjaman.php', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())    // <-- WAJIB: Ubah dari res.ok menjadi JSON
    .then(data => {
        if (data.status !== 'success') {
            alert("Gagal: " + data.message);
            return; // jangan tampil notif sukses
        }

        // Kalau sukses
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

document.querySelector('.delete-selection').addEventListener('click', function() {
    // Ambil semua checkbox yang dicentang
    let selected = Array.from(document.querySelectorAll('.select-item:checked'))
                        .map(cb => cb.value);

    if(selected.length === 0) {
        alert('Pilih data dulu!');
        return; // kalau tidak ada yang dicentang, stop
    }

    // Konfirmasi hapus
    if(confirm('Hapus data ini?')) {
        // Jika user klik OK, submit form ke PHP
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = 'hapuspeminjaman.php';

        selected.forEach(id => {
            let input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'id[]';
            input.value = id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
    // Jika user klik Cancel, tidak terjadi apa-apa
});
