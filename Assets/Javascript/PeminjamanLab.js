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