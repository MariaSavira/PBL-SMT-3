document.addEventListener("DOMContentLoaded", () => {
    
    const sortBtn  = document.getElementById("sort-btn");
    const sortMenu = document.getElementById("sort-menu");

    if (sortBtn && sortMenu) {
        
        sortBtn.addEventListener("click", (e) => {
            e.stopPropagation();
            sortMenu.classList.toggle("hidden");
        });

        
        document.addEventListener("click", (e) => {
            if (!sortMenu.contains(e.target) && !sortBtn.contains(e.target)) {
                sortMenu.classList.add("hidden");
            }
        });

        
        sortMenu.querySelectorAll("div[data-sort]").forEach(item => {
            item.addEventListener("click", () => {
                const url = new URL(window.location.href);
                url.searchParams.set("sort", item.dataset.sort);
                url.searchParams.set("page", 1);
                window.location.href = url.toString();
            });
        });
    }

    
    const searchInput = document.getElementById("search-input");

    if (searchInput) {
        let timer = null;

        searchInput.addEventListener("keyup", function () {
            
            clearTimeout(timer);
            const value = this.value;

            timer = setTimeout(() => {
                const url = new URL(window.location.href);
                url.searchParams.set("q", value);
                url.searchParams.set("page", 1);
                window.location.href = url.toString();
            }, 400);
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

            fetch("DeletePublikasi.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: formBody
            })
                .then(res => res.text())
                .then(msg => {
                    alert(msg || "Data berhasil dihapus.");
                    location.reload();
                })
                .catch(err => {
                    alert("Terjadi error: " + err);
                });
        });
    }
});
