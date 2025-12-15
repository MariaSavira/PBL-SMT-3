(() => {
  "use strict";

  // =========================
  // Helpers
  // =========================
  function qs(sel, root = document) { return root.querySelector(sel); }
  function qsa(sel, root = document) { return Array.from(root.querySelectorAll(sel)); }

  // =========================
  // NOTIFICATION (Profile.js style)
  // =========================
  function initNotification() {
    const notifEl = document.getElementById("notification");
    const notifIconEl = document.getElementById("notification-icon");
    const notifTitleEl = document.getElementById("notification-title");
    const notifMsgEl = document.getElementById("notification-message");
    const btnTutupNotif = document.getElementById("closeNotification");
    const overlay = document.getElementById("overlay");

    if (!notifEl || !notifIconEl || !notifTitleEl || !notifMsgEl || !btnTutupNotif || !overlay) return;

    function hideNotif() {
      notifEl.style.display = "none";
      overlay.style.display = "none";
    }

    function tampilNotif(isSuccess, pesan) {
      notifEl.classList.remove("success", "error");

      if (isSuccess) {
        notifEl.classList.add("success");
        notifTitleEl.textContent = "Success";
        notifMsgEl.textContent = pesan || "Aksi berhasil dilakukan.";
        notifIconEl.innerHTML = '<i class="fa-regular fa-circle-check"></i>';
      } else {
        notifEl.classList.add("error");
        notifTitleEl.textContent = "Error";
        notifMsgEl.textContent = pesan || "Terjadi kesalahan. Silakan coba lagi.";
        notifIconEl.innerHTML = '<i class="fa-regular fa-circle-xmark"></i>';
      }

      notifEl.style.display = "block";
      overlay.style.display = "block";

      setTimeout(hideNotif, 5000);
    }

    btnTutupNotif.addEventListener("click", hideNotif);

    const statusFromPhp = window.profileStatus || "";
    const messageFromPhp = window.profileMessage || "";
    const redirectUrl = window.profileRedirectUrl || "";

    if (statusFromPhp === "success") {
      tampilNotif(true, messageFromPhp);
      setTimeout(() => {
        if (redirectUrl) window.location.href = redirectUrl;
      }, 2000);
    } else if (statusFromPhp === "error") {
      tampilNotif(false, messageFromPhp);
    }
  }

  // =========================
  // DROPDOWN ACTION (⋮)
  // =========================
  function toggleMenu(event, id) {
    event.stopPropagation();

    const menu = document.getElementById("menu-" + id);
    if (!menu) return;

    const isOpen = menu.classList.contains("show");
    qsa(".action-menu").forEach(m => m.classList.remove("show"));
    if (!isOpen) menu.classList.add("show");
  }

  // =========================
  // FILTER DROPDOWN (STATUS)
  // =========================
  function initFilterDropdown() {
    const filterToggle = qs(".filter-toggle");
    const filterMenu = qs(".filter-menu");
    if (!filterToggle || !filterMenu) return;

    filterToggle.addEventListener("click", function (e) {
      e.stopPropagation();
      filterMenu.classList.toggle("open");
    });

    document.addEventListener("click", function (e) {
      if (!filterMenu.contains(e.target) && !filterToggle.contains(e.target)) {
        filterMenu.classList.remove("open");
      }
    });
  }

  // =========================
  // SORT DROPDOWN
  // =========================
  function initSortDropdown() {
    const sortBtn = document.getElementById("sort-btn");
    const sortMenu = document.getElementById("sort-menu");
    if (!sortBtn || !sortMenu) return;

    sortBtn.addEventListener("click", function (e) {
      e.stopPropagation();
      sortMenu.classList.toggle("hidden");
    });

    qsa("[data-sort]", sortMenu).forEach(item => {
      item.addEventListener("click", function () {
        const sort = this.dataset.sort;
        const url = new URL(window.location.href);
        url.searchParams.set("sort", sort);
        url.searchParams.set("page", "1");
        window.location.href = url.toString();
      });
    });

    document.addEventListener("click", function () {
      sortMenu.classList.add("hidden");
    });
  }

  // =========================
  // CLEAR FILTER
  // =========================
  function initClearFilter() {
    const clearBtn = document.getElementById("clearFilterBtn");
    if (!clearBtn) return;

    clearBtn.addEventListener("click", function (e) {
      e.preventDefault();

      const url = new URL(window.location.href);
      url.searchParams.delete("status");
      url.searchParams.delete("search");
      url.searchParams.delete("sort");
      url.searchParams.set("page", "1");
      window.location.href = url.toString();
    });
  }

  // =========================
  // TOAST (bulk selection)
  // =========================
  function updateToast() {
    const checkedBoxes = qsa("tbody .checkbox:checked");
    const toast = document.getElementById("deleteToast");
    if (!toast) return;

    const toastText = document.getElementById("toastText");
    if (checkedBoxes.length > 0) {
      toast.classList.add("show");
      if (toastText) toastText.textContent = `Hapus ${checkedBoxes.length} data yang dipilih`;
    } else {
      toast.classList.remove("show");
      if (toastText) toastText.textContent = "";
    }
  }

  function initBulkSelection() {
    const checkAllBox = document.getElementById("checkAll");
    const rowCheckboxes = qsa("tbody .checkbox");

    if (checkAllBox) {
      checkAllBox.addEventListener("change", function () {
        rowCheckboxes.forEach(cb => cb.checked = this.checked);
        updateToast();
      });
    }

    rowCheckboxes.forEach(cb => {
      cb.addEventListener("change", function () {
        updateToast();

        const allCheckboxes = qsa("tbody .checkbox");
        const checkedBoxes = qsa("tbody .checkbox:checked");
        if (checkAllBox) {
          checkAllBox.checked = (allCheckboxes.length === checkedBoxes.length && checkedBoxes.length > 0);
        }
      });
    });
  }

  // =========================
  // DELETE (SIMPLE confirm)
  // =========================
  function confirmDelete(id, judul) {
    // tutup menu ⋮ biar rapi
    qsa(".action-menu").forEach(m => m.classList.remove("show"));

    const ok = window.confirm(`Apakah Anda yakin ingin menghapus berita "${judul}"?\nTindakan ini tidak dapat dibatalkan.`);
    if (!ok) return false;

    window.location.href = "hapus_berita.php?id=" + encodeURIComponent(id);
    return false; // penting biar gak nge-trigger apa2 lagi
  }

  function confirmBulkDelete() {
    const checkedBoxes = qsa("tbody .checkbox:checked");
    if (checkedBoxes.length === 0) {
      alert("Pilih setidaknya satu berita untuk dihapus");
      return false;
    }

    const ids = checkedBoxes.map(cb => cb.value);
    const ok = window.confirm(`Apakah Anda yakin ingin menghapus ${ids.length} berita?\nTindakan ini tidak dapat dibatalkan.`);
    if (!ok) return false;

    const form = document.createElement("form");
    form.method = "POST";
    form.action = "hapus_berita.php";

    ids.forEach(id => {
      const input = document.createElement("input");
      input.type = "hidden";
      input.name = "ids[]";
      input.value = id;
      form.appendChild(input);
    });

    document.body.appendChild(form);
    form.submit();
    return false;
  }

  // (opsional) biar gak error kalau HTML lama masih manggil
  function closeDeleteModal() { return false; }

  // =========================
  // SEARCH debounce (auto submit)
  // =========================
  function initSearchDebounce() {
    let searchTimeout;
    const searchInput = qs('input[name="search"]');
    if (searchInput && searchInput.form) {
      searchInput.addEventListener("input", function () {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => this.form.submit(), 500);
      });
    }
  }

  // =========================
  // Image preview (form tambah/edit)
  // =========================
  function previewImage(input) {
    const preview = document.getElementById("imagePreview");
    const uploadArea = document.getElementById("uploadArea");
    const changeBtn = document.getElementById("changeImageBtn");
    const uploadContent = uploadArea?.querySelector(".upload-content");

    if (!input.files || !input.files[0]) return;

    const file = input.files[0];
    const allowedTypes = ["image/jpeg", "image/jpg", "image/png", "image/gif"];

    if (!allowedTypes.includes(file.type)) {
      alert("Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.");
      input.value = "";
      return;
    }

    // FIX: 5MB
    if (file.size > 5 * 1024 * 1024) {
      alert("Ukuran file terlalu besar. Maksimal 5MB.");
      input.value = "";
      return;
    }

    const reader = new FileReader();
    reader.onload = function (e) {
      if (preview) {
        preview.src = e.target.result;
        preview.classList.add("show");
      }
      if (uploadArea) uploadArea.classList.add("has-image");
      if (changeBtn) changeBtn.classList.add("show");
      if (uploadContent) uploadContent.style.display = "none";
    };
    reader.readAsDataURL(file);
  }

  // =========================
  // Close action menus when click outside
  // =========================
  function initGlobalCloseMenus() {
    document.addEventListener("click", function () {
      qsa(".action-menu").forEach(m => m.classList.remove("show"));
    });
  }

  // =========================
  // Boot
  // =========================
  document.addEventListener("DOMContentLoaded", function () {
    initNotification();
    initFilterDropdown();
    initSortDropdown();
    initClearFilter();
    initBulkSelection();
    initSearchDebounce();
    initGlobalCloseMenus();
  });

  // =========================
  // Expose global untuk inline onclick
  // =========================
  window.toggleMenu = toggleMenu;
  window.confirmDelete = confirmDelete;             // SINGLE
  window.confirmBulkDelete = confirmBulkDelete;     // BULK
  window.closeDeleteModal = closeDeleteModal;       // compat
  window.previewImage = previewImage;

})();
