// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Search filter
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });
    }
    
    // Delete confirmation
    const deleteButtons = document.querySelectorAll('.btn-delete');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (this.getAttribute('href') && !confirm('Apakah Anda yakin ingin menghapus publikasi ini?')) {
                e.preventDefault();
            }
        });
    });
    
    // Alert close buttons
    const closeButtons = document.querySelectorAll('.close-alert');
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            this.parentElement.style.display = 'none';
        });
    });
    
    // Form validation
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';
                    field.style.boxShadow = '0 0 0 3px rgba(231, 76, 60, 0.1)';
                } else {
                    field.style.borderColor = '#ddd';
                    field.style.boxShadow = 'none';
                }
            });
            
            // Validate ID format (huruf+angka)
            const idField = this.querySelector('input[name="id_publikasi"]');
            if (idField && !idField.readOnly) {
                const idPattern = /^[A-Za-z]+[0-9]+$/;
                if (!idPattern.test(idField.value)) {
                    isValid = false;
                    idField.style.borderColor = '#e74c3c';
                    idField.style.boxShadow = '0 0 0 3px rgba(231, 76, 60, 0.1)';
                    alert('Format ID Publikasi harus huruf diikuti angka (contoh: J01, BK01)');
                }
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Harap lengkapi semua field yang wajib diisi dengan benar!');
            }
        });
    });
    
    // Auto-close alerts after 5 seconds
    setTimeout(() => {
        document.querySelectorAll('.alert').forEach(alert => {
            alert.style.display = 'none';
        });
    }, 5000);
    
    // Status badge colors
    const statusBadges = document.querySelectorAll('.status-badge');
    statusBadges.forEach(badge => {
        const status = badge.textContent.trim().toLowerCase();
        badge.classList.add(`status-${status}`);
    });
    
    // Export button functionality (simulasi)
    const exportBtn = document.querySelector('.btn-primary');
    if (exportBtn && exportBtn.textContent.includes('Export')) {
        exportBtn.addEventListener('click', function(e) {
            e.preventDefault();
            alert('Fitur export akan mengunduh data dalam format CSV. Fitur ini sedang dalam pengembangan.');
        });
    }
    
    // Filter button functionality
    const filterBtn = document.querySelector('.btn-secondary');
    if (filterBtn && filterBtn.textContent.includes('Hapus Filter')) {
        filterBtn.addEventListener('click', function() {
            const searchInput = document.getElementById('searchInput');
            if (searchInput) {
                searchInput.value = '';
                const rows = document.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    row.style.display = '';
                });
            }
        });
    }
    
    // Pagination button functionality
    const prevBtn = document.querySelector('.pagination-buttons .btn-secondary:not([disabled])');
    const nextBtn = document.querySelector('.pagination-buttons .btn-primary');
    
    if (prevBtn) {
        prevBtn.addEventListener('click', function() {
            alert('Navigasi ke halaman sebelumnya');
        });
    }
    
    if (nextBtn) {
        nextBtn.addEventListener('click', function() {
            alert('Navigasi ke halaman selanjutnya');
        });
    }
    
    // Form date validation (tidak boleh lebih dari hari ini)
    const dateInput = document.querySelector('input[type="date"]');
    if (dateInput) {
        const today = new Date().toISOString().split('T')[0];
        dateInput.max = today;
        
        dateInput.addEventListener('change', function() {
            if (this.value > today) {
                alert('Tanggal tidak boleh lebih dari hari ini!');
                this.value = today;
            }
        });
    }
});