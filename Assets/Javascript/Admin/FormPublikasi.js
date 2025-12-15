document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.field-select').forEach(dropdown => {

    const toggle = dropdown.querySelector('.dropdown-toggle');
    const label  = dropdown.querySelector('.dropdown-label');
    const menu   = dropdown.querySelector('.dropdown-menu');
    const hidden = dropdown.querySelector('input[name="status"]');
    const placeholder = dropdown.dataset.placeholder || 'Pilih';

    toggle.addEventListener('click', e => {
      e.stopPropagation();
      dropdown.classList.toggle('open');
    });

    menu.addEventListener('click', e => {
      const item = e.target.closest('.dropdown-item');
      if (!item) return;

      hidden.value = item.dataset.value;
      label.textContent = item.textContent.trim();
      dropdown.classList.remove('open');
    });

    document.addEventListener('click', () => {
      dropdown.classList.remove('open');
    });
  });
});
