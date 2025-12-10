// Assets/Javascript/Admin/Dashboard.js

document.addEventListener("DOMContentLoaded", function () {

    // ==========================
    // 1. GRAFIK PEMINJAMAN (CHART.JS)
    // ==========================
    if (typeof Chart !== "undefined") {
        const canvasPeminjaman = document.getElementById("chartPeminjaman");

        if (canvasPeminjaman) {
            const ctx = canvasPeminjaman.getContext("2d");

            const labelsPeminjaman = window.peminjamanLabels || [];
            const dataPeminjaman   = window.peminjamanData   || [];

            console.log("Labels dari PHP:", labelsPeminjaman);
            console.log("Data   dari PHP:", dataPeminjaman);

            // tidak ada data, jangan gambar grafik
            if (!labelsPeminjaman.length || !dataPeminjaman.length) {
                return;
            }

            new Chart(ctx, {
                type: "bar",
                data: {
                    labels: labelsPeminjaman,
                    datasets: [{
                        label: "Jumlah Ajuan",
                        data: dataPeminjaman,
                        borderWidth: 1,
                        borderRadius: 8,
                        backgroundColor: "rgba(59, 130, 246, 0.4)",
                        borderColor: "rgba(59, 130, 246, 1)"
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        }
    }

    // ==========================
    // 2. AKSI EDIT / HAPUS ANGGOTA TERBARU
    // ==========================
    const editButtons   = document.querySelectorAll(".anggota-section .btn-edit");
    const deleteButtons = document.querySelectorAll(".anggota-section .btn-delete");

    // Catatan:
    // Saat ini .btn-edit & .btn-delete masih <a href="...">
    // JS di bawah hanya aktif kalau nanti diganti jadi <button>.
    editButtons.forEach(btn => {
        if (btn.tagName && btn.tagName.toLowerCase() === "a") return;

        btn.addEventListener("click", function () {
            const card = this.closest(".member-card");
            const id   = this.dataset.id || (card && card.dataset.id);
            if (!id) return;

            window.location.href = "EditAnggota.php?id=" + encodeURIComponent(id);
        });
    });

    deleteButtons.forEach(btn => {
        if (btn.tagName && btn.tagName.toLowerCase() === "a") return;

        btn.addEventListener("click", function () {
            const card = this.closest(".member-card");
            const id   = this.dataset.id || (card && card.dataset.id);
            if (!id) return;

            if (!confirm("Yakin ingin menghapus anggota ini?")) return;
            window.location.href = "HapusAnggota.php?id=" + encodeURIComponent(id);
        });
    });

    // ==========================
    // 3. NOTIFIKASI LONCENG (DROPDOWN + MARK AS READ)
    // ==========================
    const notifToggle = document.getElementById("notifToggle");
    const notifMenu   = document.getElementById("notifMenu");
    const notifBadge  = document.querySelector(".notif-badge");

    // supaya request tandai-dibaca hanya sekali
    let notifSudahKirimMarkRead = false;

    if (notifToggle && notifMenu) {
        notifToggle.addEventListener("click", function (e) {
            e.stopPropagation();

            // toggle dropdown
            notifMenu.classList.toggle("show");

            // kalau baru pertama kali dibuka, kirim request untuk tandai sudah dibaca
            if (notifMenu.classList.contains("show") && !notifSudahKirimMarkRead) {
                notifSudahKirimMarkRead = true;

                // cek dukungan fetch (browser lama banget aja yang nggak punya)
                if (typeof fetch === "function") {
                    fetch("TandaiNotifDibaca.php", {
                        method: "POST"
                    })
                    .then(function () {
                        // hilangkan badge merah kalau ada
                        if (notifBadge) {
                            notifBadge.remove();
                        }

                        // hapus class 'unread' di semua item notif
                        document
                            .querySelectorAll(".notif-item.unread")
                            .forEach(function (item) {
                                item.classList.remove("unread");
                            });
                    })
                    .catch(function (err) {
                        console.error("Gagal update status notif:", err);
                    });
                }
            }
        });

        // klik di dalam menu tidak menutup dropdown
        notifMenu.addEventListener("click", function (e) {
            e.stopPropagation();
        });

        // klik di luar menutup dropdown
        document.addEventListener("click", function () {
            notifMenu.classList.remove("show");
        });
    }

    // ==========================
    // 4. KALENDER (ICON -> INPUT DATE)
    // ==========================
    const calendarBtn   = document.getElementById("calendarButton");
    const calendarInput = document.getElementById("calendarInput");

    if (calendarBtn && calendarInput) {
        calendarBtn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();

            // showPicker() didukung Chrome/Edge/Opera modern
            if (typeof calendarInput.showPicker === "function") {
                calendarInput.showPicker();
            } else {
                // fallback browser lain
                calendarInput.focus();
                calendarInput.click();
            }
        });

        // opsional: listener ketika tanggal dipilih
        calendarInput.addEventListener("change", function () {
            console.log("Tanggal dipilih:", this.value);
            // kalau nanti mau dipakai filter data, logika bisa ditaruh di sini
        });
    }

});
