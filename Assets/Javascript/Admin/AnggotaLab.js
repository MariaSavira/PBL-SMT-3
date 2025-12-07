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