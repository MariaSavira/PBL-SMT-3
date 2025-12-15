document.addEventListener("DOMContentLoaded", function () {
    
    const toggle = document.querySelector(".filter-toggle");
    const menu   = document.querySelector(".filter-menu");

    if (toggle && menu) {
        toggle.addEventListener("click", function (e) {
            e.stopPropagation();
            menu.classList.toggle("open");
        });

        document.addEventListener("click", function (e) {
            if (!menu.contains(e.target) && !toggle.contains(e.target)) {
                menu.classList.remove("open");
            }
        });
    }
    
    const table = document.querySelector(".table-container");

    function closeAllMenus() {
        document
            .querySelectorAll(".action-menu.open")
            .forEach(m => m.classList.remove("open"));
    }

    if (table) {
        table.addEventListener("click", function (e) {
            const toggleBtn = e.target.closest(".action-toggle");
            const deleteBtn = e.target.closest(".action-delete");

            
            if (toggleBtn) {
                const cell = toggleBtn.closest(".action-cell");
                const menu = cell ? cell.querySelector(".action-menu") : null;
                if (!menu) return;

                const isOpen = menu.classList.contains("open");
                closeAllMenus();
                if (!isOpen) {
                    menu.classList.add("open");
                }
                e.stopPropagation();
                return;
            }

            
            if (deleteBtn && deleteBtn.closest(".action-cell")) {
                const cell = deleteBtn.closest(".action-cell");
                const form = cell.querySelector(".delete-anggota-form");
                if (!form) {
                    console.warn("Form delete tidak ditemukan");
                    return;
                }

                const yakin = confirm("Yakin ingin menghapus data anggota ini?");
                if (yakin) {
                    form.submit();
                }
                e.stopPropagation();
                return;
            }
        });

        
        document.addEventListener("click", function (e) {
            if (!e.target.closest(".action-cell")) {
                closeAllMenus();
            }
        });
    }

    
    const deleteSelectionBtn = document.querySelector(".delete-selection");

    if (deleteSelectionBtn) {
        deleteSelectionBtn.addEventListener("click", () => {
            const checked = document.querySelectorAll(".row-check:checked");

            if (checked.length === 0) {
                alert("Tidak ada data yang dipilih.");
                return;
            }

            if (!confirm("Yakin ingin menghapus data yang dipilih?")) {
                return;
            }

            const ids = Array.from(checked).map(c => c.value);
            const formBody = ids.map(id => `ids[]=${encodeURIComponent(id)}`).join("&");

            fetch("DeleteAnggotaBulk.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: formBody
            })
                .then(res => {
                    if (!res.ok) {
                        throw new Error("Gagal menghapus (status " + res.status + ")");
                    }
                    return res.text(); 
                })
                .then(() => {
                    location.reload();
                })
                .catch(err => {
                    alert("Terjadi error: " + err.message);
                });
        });
    }
});
