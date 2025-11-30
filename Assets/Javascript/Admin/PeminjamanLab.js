document.querySelectorAll(".btn-save").forEach(btn => {
    btn.addEventListener("click", function () {
        let id = this.dataset.id;
        let row = this.closest("tr");
        let status = row.querySelector(".status-dropdown").value;

        fetch("update_status.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id_peminjaman=" + id + "&status=" + status
        })
        .then(res => res.text())
        .then(response => {
            alert(response);
        })
        .catch(err => alert("Error: " + err));
    });
});
