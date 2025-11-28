
        window.addEventListener("scroll", function () {
            const navbar = document.getElementById("navbar");
            if (window.scrollY > 20) {
                navbar.classList.add("navbar-scrolled");
            } else {
                navbar.classList.remove("navbar-scrolled");
            }
        });

        document.addEventListener('DOMContentLoaded', function () {
            const fileInput = document.getElementById('fileInput');
            const uploadBtn = document.getElementById('uploadTrigger');
            const filePreview = document.getElementById('filePreview');
            const fileNameEl = document.getElementById('fileName');
            const fileSizeEl = document.getElementById('fileSize');
            const fileViewBtn = document.getElementById('fileViewBtn');

            uploadBtn.addEventListener('click', function () {
                fileInput.click();
            });

            let currentFileUrl = null;

            fileInput.addEventListener('change', function () {
                const file = this.files[0];
                if (!file) return;

                if (file.type !== 'application/pdf') {
                    alert('Hanya file PDF yang diperbolehkan.');
                    this.value = '';
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    alert('Ukuran file maksimal 5 MB.');
                    this.value = '';
                    return;
                }

                fileNameEl.textContent = file.name;
                const sizeMB = file.size / (1024 * 1024);
                fileSizeEl.textContent = sizeMB.toFixed(1) + ' MB';

                if (currentFileUrl) {
                    URL.revokeObjectURL(currentFileUrl);
                }
                currentFileUrl = URL.createObjectURL(file);

                fileViewBtn.onclick = function () {
                    window.open(currentFileUrl, '_blank');
                };

                filePreview.style.display = 'flex';
            });
        });
