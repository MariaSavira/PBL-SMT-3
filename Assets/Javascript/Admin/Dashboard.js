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

            if (!labelsPeminjaman.length || !dataPeminjaman.length) {
                // kalau kosong, jangan gambar apa-apa
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
    // 2. FILTER DROPDOWN
    // ==========================
    const filterToggle = document.getElementById("filterToggle");
    const filterMenu   = document.getElementById("filterMenu");
    const filterLabel  = document.getElementById("filterLabel");

    if (filterToggle && filterMenu && filterLabel) {
        filterToggle.addEventListener("click", function (e) {
            e.stopPropagation();
            filterMenu.classList.toggle("show");
        });

        filterMenu.querySelectorAll("button").forEach(btn => {
            btn.addEventListener("click", function () {
                filterLabel.textContent = this.textContent;
                filterMenu.querySelectorAll("button").forEach(b => b.classList.remove("active"));
                this.classList.add("active");
                filterMenu.classList.remove("show");
            });
        });

        document.addEventListener("click", () => {
            filterMenu.classList.remove("show");
        });
    }

    // ==========================
    // 3. KALENDER POPUP
    // ==========================
    const calendarBtn   = document.getElementById("calendarButton");
    const calendarInput = document.getElementById("calendarInput");

    if (calendarBtn && calendarInput && typeof calendarInput.showPicker === "function") {
        calendarBtn.addEventListener("click", (e) => {
            e.preventDefault();
            calendarInput.showPicker();
        });

        calendarInput.addEventListener("change", () => {
            console.log("Tanggal terpilih:", calendarInput.value);
        });
    }

});
