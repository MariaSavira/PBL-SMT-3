// Assets/Javascript/Admin/Dashboard.js

document.addEventListener("DOMContentLoaded", function () {

    // ==========================
    // 1. GRAFIK PEMINJAMAN (CHART.JS)
    // ==========================
    if (typeof Chart !== "undefined") {
        const canvasPeminjaman = document.getElementById("chartPeminjaman");
        if (canvasPeminjaman) {
            const ctx = canvasPeminjaman.getContext("2d");

            // sementara pakai data dummy; nanti bisa diisi dari PHP
            const labelsPeminjaman = ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun"];
            const dataPeminjaman   = [3, 5, 2, 6, 4, 7];

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
    // 2. FILTER DROPDOWN ("Minggu Ini / Bulan Ini / Tahun Ini")
    // ==========================
    const filterToggle = document.getElementById("filterToggle");
    const filterMenu   = document.getElementById("filterMenu");
    const filterLabel  = document.getElementById("filterLabel");

    if (filterToggle && filterMenu && filterLabel) {
        // buka/tutup dropdown
        filterToggle.addEventListener("click", function (e) {
            e.stopPropagation();
            filterMenu.classList.toggle("show");
        });

        // pilih salah satu opsi
        filterMenu.querySelectorAll("button").forEach(btn => {
            btn.addEventListener("click", function () {
                // ubah teks tombol utama
                filterLabel.textContent = this.textContent;

                // state active di menu
                filterMenu.querySelectorAll("button").forEach(b => b.classList.remove("active"));
                this.classList.add("active");

                // tutup menu
                filterMenu.classList.remove("show");

                // kalau mau dipakai untuk filter data:
                // const range = this.dataset.range;   // "week" | "month" | "year"
                // console.log("Filter dipilih:", range);
            });
        });

        // klik di luar dropdown → tutup
        document.addEventListener("click", () => {
            filterMenu.classList.remove("show");
        });
    }

    // ==========================
    // 3. KALENDER POPUP (TOMBOL ICON)
    // ==========================
    const calendarBtn   = document.getElementById("calendarButton");
    const calendarInput = document.getElementById("calendarInput");

    // showPicker cuma ada di browser tertentu (Chrome, Edge, dll)
    if (calendarBtn && calendarInput && typeof calendarInput.showPicker === "function") {
        // klik icon → buka datepicker bawaan browser
        calendarBtn.addEventListener("click", (e) => {
            e.preventDefault();
            calendarInput.showPicker();
        });

        // saat tanggal dipilih
        calendarInput.addEventListener("change", () => {
            console.log("Tanggal terpilih:", calendarInput.value);

            // nanti bisa dipakai filter dashboard, contoh:
            // loadDataByDate(calendarInput.value);
        });
    }

});
