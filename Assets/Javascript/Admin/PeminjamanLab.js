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
});

const dirtyRows = new Set();

let editingId = null;
const modal = document.getElementById("catatan-modal");
const modalTextarea = document.getElementById("catatan-modal-text");

document.querySelectorAll('.edit-catatan').forEach(icon => {
    icon.addEventListener('click', () => {
        editingId = icon.dataset.id;

        const currentText = document.getElementById(`catatan-text-${editingId}`).innerText.trim();
        modalTextarea.value = currentText;


        modal.classList.add('open');
    });
});

document.getElementById("catatan-modal-cancel").addEventListener("click", () => {
    modal.classList.remove('open');
    editingId = null;
});

document.getElementById("catatan-modal-save").addEventListener("click", () => {
    if (!editingId) return;
    const newText = modalTextarea.value;
    document.getElementById(`catatan-text-${editingId}`).innerText = newText;
    document.getElementById(`catatan-input-${editingId}`).value = newText;
    modal.classList.remove('open');

    alert("Catatan berhasil diperbarui.");
});

document.querySelectorAll('.btn-save').forEach(btn => {
    btn.addEventListener('click', () => {

        let id = btn.dataset.id;
        let status = document.querySelector(`.status-dropdown[data-id="${id}"]`).value;
        let catatan = document.getElementById(`catatan-input-${id}`).value;


        if (!confirm("Yakin ingin menyimpan perubahan ini?")) {
            return;
        }

        fetch("../../UpdateStatus.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: `id=${id}&status=${encodeURIComponent(status)}&catatan=${encodeURIComponent(catatan)}`
        })
            .then(res => res.text())
            .then(res => {
                console.log(res);

                if (res.startsWith("ERROR")) {
                    alert(" Gagal menyimpan: " + res);
                    return;
                }


                alert("Perubahan berhasil disimpan!\n" + "Email pemberitahuan telah dikirim ke peminjam.");


                document.getElementById(`catatan-text-${id}`).innerText = catatan;
                document.getElementById(`catatan-input-${id}`).style.display = "none";
                document.getElementById(`catatan-text-${id}`).style.display = "inline";
            })
            .catch(err => {
                alert("Terjadi kesalahan: " + err);
            });
    });
});

const searchInput = document.getElementById("search-input");
let searchTimer = null;

if (searchInput) {
    searchInput.addEventListener("input", function () {
        clearTimeout(searchTimer);

        searchTimer = setTimeout(() => {
            const url = new URL(window.location.href);

            const q = searchInput.value.trim();
            if (q) url.searchParams.set("q", q);
            else url.searchParams.delete("q");

            url.searchParams.set("page", "1");
            window.location.href = url.toString();
        }, 350);
    });
}

const sortBtn = document.getElementById("sort-btn");
const sortMenu = document.getElementById("sort-menu");


sortBtn.addEventListener("click", () => {
    sortMenu.classList.toggle("hidden");
});


document.addEventListener("click", (e) => {
    if (!sortMenu.contains(e.target) && !sortBtn.contains(e.target)) {
        sortMenu.classList.add("hidden");
    }
});


sortMenu.querySelectorAll("div[data-sort]").forEach(item => {
    item.addEventListener("click", () => {
        const type = item.dataset.sort;


        const url = new URL(window.location.href);
        url.searchParams.set("sort", type);
        url.searchParams.set("page", 1);

        window.location.href = url.toString();
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