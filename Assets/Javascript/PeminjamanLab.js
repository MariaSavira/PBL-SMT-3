const form = document.getElementById("loanForm");
const notif = document.getElementById("notification");
const closeNotif = document.getElementById("closeNotification");

form.addEventListener("submit", function(e) {
    e.preventDefault();

    const formData = new FormData(form);

    fetch("submit_peminjaman.php", {
        method: "POST",
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error("Gagal mengirim data");
        notif.style.display = "flex"; // tampilkan notif jika sukses
        form.reset(); // kosongkan form
    })
    .catch(error => {
        alert("Terjadi kesalahan saat mengirim data.");
        console.error(error);
    });
});

closeNotif.addEventListener("click", () => {
    notif.style.display = "none";
});

document.getElementById('loanForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('submit_peminjaman.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) throw new Error('Gagal kirim data');
        alert("Data berhasil dikirim!");
        window.location.href = 'FormPeminjamanLab.html?success=1';
    })
    .catch(error => {
        alert('Terjadi kesalahan saat mengirim data.');
        console.error(error);
    });
});

