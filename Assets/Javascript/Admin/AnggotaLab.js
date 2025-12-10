document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.querySelector('.filter-toggle');
    const menu = document.querySelector('.filter-menu');

    if (!toggle || !menu) return;

    toggle.addEventListener('click', function (e) {
        e.stopPropagation();
        menu.classList.toggle('open');
    });

    document.addEventListener('click', function (e) {
        if (!menu.contains(e.target)) {
            menu.classList.remove('open');
        }
    });

    const table = document.querySelector('.table-container');

    if (!table) return;

    function closeAllMenus() {
        document
            .querySelectorAll('.action-menu.open')
            .forEach(m => m.classList.remove('open'));
    }

    table.addEventListener('click', function (e) {
        const toggleBtn = e.target.closest('.action-toggle');
        const deleteBtn = e.target.closest('.action-delete');

        if (toggleBtn) {
            const cell = toggleBtn.closest('.action-cell');
            const menu = cell.querySelector('.action-menu');

            const isOpen = menu.classList.contains('open');
            closeAllMenus();
            if (!isOpen) {
                menu.classList.add('open');
            }
            e.stopPropagation();
            return;
        }

        if (deleteBtn) {
            const cell = deleteBtn.closest('.action-cell');
            const form = cell.querySelector('.delete-anggota-form');
            if (!form) {
                console.warn('Form delete tidak ditemukan');
                return;
            }

            const yakin = confirm('Yakin ingin menghapus data anggota ini?');
            if (yakin) {
                form.submit(); // POST ke DeleteAnggota.php
            }
            e.stopPropagation();
            return;
        }
    });

    // document.addEventListener('click', function () {
    //     closeAllMenus();
    // });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.action-cell')) {
            closeAllMenus();
        }
    });
});

document.querySelector('.delete-selection').addEventListener('click', () => {
    const checked = document.querySelectorAll('.row-check:checked');

    if (checked.length === 0) {
        alert("Tidak ada data yang dipilih.");
        return;
    }

    if (!confirm("Yakin ingin menghapus data yang dipilih?")) {
        return;
    }

    let ids = [...checked].map(c => c.value);
    let formBody = ids.map(id => `ids[]=${encodeURIComponent(id)}`).join("&");

    fetch("hapuspeminjaman.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: formBody
    })
        .then(res => res.text())
        .then(msg => {
            alert(msg);
            location.reload();
        })
        .catch(err => alert("Terjadi error: " + err));
});