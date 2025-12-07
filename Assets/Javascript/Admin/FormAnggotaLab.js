document.addEventListener('DOMContentLoaded', function () {
    const dropdown = document.querySelector('.dropdown-jabatan');
    if (!dropdown) return;

    const toggleBtn = dropdown.querySelector('.dropdown-toggle');
    const menu      = dropdown.querySelector('.dropdown-menu');
    const labelEl   = dropdown.querySelector('.dropdown-label');
    const hidden    = dropdown.querySelector('input[type="hidden"]');

    toggleBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        const isOpen = dropdown.classList.toggle('open');
        toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
    });

    menu.addEventListener('click', function (e) {
        const item = e.target.closest('.dropdown-item');
        if (!item) return;

        const value = item.dataset.value || '';
        labelEl.textContent = value || 'Pilih jabatan';
        hidden.value = value;
        dropdown.classList.remove('open');

        if (value) {
            dropdown.classList.add('filled');
        } else {
            dropdown.classList.remove('filled');
        }
    });

    document.addEventListener('click', function (e) {
        if (!dropdown.contains(e.target)) {
            dropdown.classList.remove('open');
            toggleBtn.setAttribute('aria-expanded', 'false');
        }
    });
});
