document.addEventListener('DOMContentLoaded', function () {
    const dropdowns = document.querySelectorAll('.field-select');

    dropdowns.forEach((dropdown) => {
        const toggleBtn = dropdown.querySelector('.dropdown-toggle');
        const menu = dropdown.querySelector('.dropdown-menu');
        const labelEl = dropdown.querySelector('.dropdown-label');
        const hidden = dropdown.querySelector('input[type="hidden"]');
        const placeholder = dropdown.dataset.placeholder || 'Pilih opsi';

        if (!toggleBtn || !menu || !labelEl || !hidden) return;

        
        if (!hidden.value) {
            labelEl.textContent = placeholder;
            dropdown.classList.remove('filled');
        } else {
            dropdown.classList.add('filled');
        }

        
        toggleBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const isOpen = dropdown.classList.toggle('open');
            toggleBtn.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });

        
        menu.addEventListener('click', function (e) {
            const item = e.target.closest('.dropdown-item');
            if (!item) return;

            const value = item.dataset.value || '';
            const text = item.textContent.trim() || placeholder;

            hidden.value = value;
            labelEl.textContent = text;

            dropdown.classList.remove('open');
            toggleBtn.setAttribute('aria-expanded', 'false');

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

    
    const btnTambah = document.getElementById('btnTambahLink');
    const wrapper = document.getElementById('links-wrapper');

    if (btnTambah && wrapper) {
        btnTambah.addEventListener('click', function () {
            const row = document.createElement('div');
            row.className = 'link-row';
            row.innerHTML = `
                    <input
                        type="text"
                        name="link_label[]"
                        class="field-input link-label"
                        placeholder="Nama platform (mis. Sinta, Scholar)"
                    >
                    <input
                        type="url"
                        name="link_url[]"
                        class="field-input link-url"
                        placeholder="https:
                    >
                    <button type="button" class="btn-remove-link">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                `;
            wrapper.appendChild(row);
        });

        wrapper.addEventListener("click", (e) => {
            if (e.target.closest(".btn-remove-link")) {
                const row = e.target.closest(".link-row");
                if (row) row.remove();
            }
        });
    }
});