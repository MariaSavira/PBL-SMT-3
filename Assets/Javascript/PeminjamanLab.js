        const form = document.getElementById("loanForm");
        const notif = document.getElementById("notification");
        const closeNotif = document.getElementById("closeNotification");

        form.addEventListener("submit", (e) => {
            e.preventDefault();
            notif.style.display = "flex";
        });

        closeNotif.addEventListener("click", () => {
            notif.style.display = "none";
        });

  document.getElementById('PeminjamanForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const formData = new FormData(this);

    fetch('Admin/CRUD/PeminjamanLab.php', {
      method: 'POST',
      body: formData
    })
    .then(response => {
      if (!response.ok) throw new Error('Gagal kirim data');
      // setelah berhasil, arahkan ke index.php
      window.location.href = '../CRUD/PeminjamanLab.php';
    })
    .catch(error => {
      alert('Terjadi kesalahan saat mengirim data.');
      console.error(error);
    });
  });
