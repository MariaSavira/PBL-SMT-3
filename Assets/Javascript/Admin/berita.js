// Assets/Javascript/Admin/berita.js
// JavaScript untuk handling interaksi halaman berita

// Toggle dropdown menu
function toggleMenu(event, id) {
    event.stopPropagation();
    const menu = document.getElementById('menu-' + id);
    const allMenus = document.querySelectorAll('.dropdown-menu');
    
    // Close all other menus
    allMenus.forEach(m => {
        if (m !== menu) m.classList.remove('show');
    });
    
    // Toggle current menu
    menu.classList.toggle('show');
}

// Close dropdown when clicking outside
document.addEventListener('click', function(e) {
    if (!e.target.closest('.menu-dots')) {
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});

// Checkbox "Select All" functionality
const checkAllBox = document.getElementById('checkAll');
if (checkAllBox) {
    checkAllBox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('tbody .checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateToast();
    });
}

// Individual checkbox change
document.querySelectorAll('tbody .checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        updateToast();
        
        // Update "check all" status
        const allCheckboxes = document.querySelectorAll('tbody .checkbox');
        const checkedBoxes = document.querySelectorAll('tbody .checkbox:checked');
        const checkAllBox = document.getElementById('checkAll');
        
        if (checkAllBox) {
            checkAllBox.checked = allCheckboxes.length === checkedBoxes.length && checkedBoxes.length > 0;
        }
    });
});

// Update toast notification based on selected items
function updateToast() {
    const checkedBoxes = document.querySelectorAll('tbody .checkbox:checked');
    const toast = document.getElementById('deleteToast');
    const toastText = document.getElementById('toastText');
    
    if (checkedBoxes.length > 0) {
        toast.classList.add('show');
        toastText.textContent = `Hapus ${checkedBoxes.length} data yang dipilih`;
    } else {
        toast.classList.remove('show');
    }
}

// Bulk delete confirmation
function confirmBulkDelete() {
    const checkedBoxes = document.querySelectorAll('tbody .checkbox:checked');
    
    if (checkedBoxes.length === 0) {
        alert('Pilih setidaknya satu berita untuk dihapus');
        return;
    }
    
    const ids = Array.from(checkedBoxes).map(cb => cb.value);
    const count = ids.length;
    
    const message = `Apakah Anda yakin ingin menghapus ${count} berita? Tindakan ini tidak dapat dibatalkan.`;
    document.getElementById('deleteMessage').textContent = message;
    document.getElementById('deleteModal').classList.add('show');
    
    // Set up bulk delete handler
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.onclick = function() {
        executeBulkDelete(ids);
    };
}

// Execute bulk delete
function executeBulkDelete(ids) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'hapus_berita.php';
    
    ids.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        form.appendChild(input);
    });
    
    document.body.appendChild(form);
    form.submit();
}

// Single delete confirmation
let deleteId = null;

function confirmDelete(id, judul) {
    deleteId = id;
    const message = `Apakah Anda yakin ingin menghapus berita "${judul}"? Tindakan ini tidak dapat dibatalkan.`;
    document.getElementById('deleteMessage').textContent = message;
    document.getElementById('deleteModal').classList.add('show');
    
    // Set up single delete handler
    document.getElementById('confirmDeleteBtn').onclick = function() {
        if (deleteId) {
            window.location.href = 'hapus_berita.php?id=' + deleteId;
        }
    };
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
    deleteId = null;
}

// Close modal when clicking overlay
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// ESC key to close modal
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});

// Search functionality with debounce
let searchTimeout;
const searchInput = document.querySelector('input[name="search"]');
if (searchInput) {
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            this.form.submit();
        }, 500);
    });
}

// Auto-hide success/error messages
window.addEventListener('DOMContentLoaded', function() {
    const successMsg = document.querySelector('.alert-success');
    const errorMsg = document.querySelector('.alert-error');
    
    if (successMsg) {
        setTimeout(() => {
            successMsg.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => successMsg.remove(), 300);
        }, 3000);
    }
    
    if (errorMsg) {
        setTimeout(() => {
            errorMsg.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => errorMsg.remove(), 300);
        }, 5000);
    }
});

// Image preview in forms
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const uploadArea = document.getElementById('uploadArea');
    const changeBtn = document.getElementById('changeImageBtn');
    const uploadContent = uploadArea?.querySelector('.upload-content');

    if (input.files && input.files[0]) {
        const file = input.files[0];
        
        // Validate file type
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Format file tidak diizinkan. Gunakan JPG, PNG, atau GIF.');
            input.value = '';
            return;
        }
        
        // Validate file size (500MB)
        if (file.size > 500000000) {
            alert('Ukuran file terlalu besar. Maksimal 500MB.');
            input.value = '';
            return;
        }
        
        const reader = new FileReader();
        
        reader.onload = function(e) {
            if (preview) {
                preview.src = e.target.result;
                preview.classList.add('show');
            }
            if (uploadArea) {
                uploadArea.classList.add('has-image');
            }
            if (changeBtn) {
                changeBtn.classList.add('show');
            }
            if (uploadContent) {
                uploadContent.style.display = 'none';
            }
        }
        
        reader.readAsDataURL(file);
    }
}

// Form validation
const beritaForm = document.getElementById('formBerita');
if (beritaForm) {
    beritaForm.addEventListener('submit', function(e) {
        const judul = document.getElementById('judul').value.trim();
        const isi = document.getElementById('isi').value.trim();
        const tanggal = document.getElementById('tanggal').value;
        
        if (!judul) {
            alert('Judul berita harus diisi!');
            e.preventDefault();
            return;
        }
        
        if (!isi) {
            alert('Isi berita harus diisi!');
            e.preventDefault();
            return;
        }
        
        if (!tanggal) {
            alert('Tanggal terbit harus diisi!');
            e.preventDefault();
            return;
        }
        
        // Disable submit button to prevent double submission
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';
        }
    });
}

// Sidebar responsive handling
document.addEventListener('DOMContentLoaded', function() {
    // Function to check sidebar state
    function checkSidebarState() {
        const sidebar = document.querySelector('.sidebar, #sidebar');
        if (sidebar) {
            if (sidebar.classList.contains('expanded') || sidebar.classList.contains('open')) {
                document.body.classList.add('sidebar-expanded');
            } else {
                document.body.classList.remove('sidebar-expanded');
            }
        }
    }
    
    // Check initial state
    setTimeout(checkSidebarState, 100);
    
    // Observe sidebar class changes
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                checkSidebarState();
            }
        });
    });
    
    // Start observing
    setTimeout(function() {
        const sidebar = document.querySelector('.sidebar, #sidebar');
        if (sidebar) {
            observer.observe(sidebar, { 
                attributes: true,
                attributeFilter: ['class'],
                subtree: true
            });
        }
    }, 200);
    
    // Also listen for custom events if sidebar uses them
    document.addEventListener('sidebarToggle', checkSidebarState);
    window.addEventListener('sidebarStateChange', checkSidebarState);
});

// Smooth scroll for navigation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Loading state for buttons
function setButtonLoading(button, isLoading) {
    if (isLoading) {
        button.dataset.originalText = button.textContent;
        button.textContent = 'Memproses...';
        button.disabled = true;
    } else {
        button.textContent = button.dataset.originalText || button.textContent;
        button.disabled = false;
    }
}

console.log('Berita.js loaded successfully');
console.log('Current page:', window.location.pathname);