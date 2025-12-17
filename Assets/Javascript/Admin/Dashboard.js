document.addEventListener("DOMContentLoaded", function () {
    
    if (typeof Chart !== "undefined") {
        const canvasPeminjaman = document.getElementById("chartPeminjaman");

        if (canvasPeminjaman) {
            const ctx = canvasPeminjaman.getContext("2d");

            const labelsPeminjaman = window.peminjamanLabels || [];
            const dataPeminjaman   = window.peminjamanData   || [];

            console.log("Labels dari PHP:", labelsPeminjaman);
            console.log("Data   dari PHP:", dataPeminjaman);
            
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
    
    const editButtons   = document.querySelectorAll(".anggota-section .btn-edit");
    const deleteButtons = document.querySelectorAll(".anggota-section .btn-delete");    
    
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
    
    const notifToggle = document.getElementById("notifToggle");
    const notifMenu   = document.getElementById("notifMenu");
    const notifBadge  = document.querySelector(".notif-badge");

    let notifSudahKirimMarkRead = false;

    if (notifToggle && notifMenu) {
        notifToggle.addEventListener("click", function (e) {
            e.stopPropagation();
            
            notifMenu.classList.toggle("show");
            
            if (notifMenu.classList.contains("show") && !notifSudahKirimMarkRead) {
                notifSudahKirimMarkRead = true;
                
                if (typeof fetch === "function") {
                    fetch("TandaiNotifDibaca.php", {
                        method: "POST"
                    })
                    .then(function () {
                        
                        if (notifBadge) {
                            notifBadge.remove();
                        }
   
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
        
        notifMenu.addEventListener("click", function (e) {
            e.stopPropagation();
        });
        
        document.addEventListener("click", function () {
            notifMenu.classList.remove("show");
        });
    }
    
    const calendarBtn   = document.getElementById("calendarButton");
    const calendarInput = document.getElementById("calendarInput");

    if (calendarBtn && calendarInput) {
        calendarBtn.addEventListener("click", function (e) {
            e.preventDefault();
            e.stopPropagation();
            
            if (typeof calendarInput.showPicker === "function") {
                calendarInput.showPicker();
            } else {
                
                calendarInput.focus();
                calendarInput.click();
            }
        });
        
        calendarInput.addEventListener("change", function () {
            console.log("Tanggal dipilih:", this.value);
        });
    }
});
