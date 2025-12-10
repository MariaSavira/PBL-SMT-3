// Global variables
let isCheckMode = false;
let selectedItems = [];
let allData = [];
let deleteId = null;
let activeDropdown = null;

// Load data on page load
document.addEventListener('DOMContentLoaded', function() {
    loadPengumuman();
    checkSidebarState();
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.btn-more') && !e.target.closest('.dropdown-menu')) {
            closeAllDropdowns();
        }
    });
});

// Check sidebar state
function checkSidebarState() {
    const sidebar = document.getElementById('sidebar');
    const mainContainer = document.getElementById('mainContainer');
    
    if (sidebar) {
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    if (sidebar.classList.contains('open')) {
                        mainContainer.classList.add('sidebar-open');
                    } else {
                        mainContainer.classList.remove('sidebar-open');
                    }
                }
            });
        });
        
        observer.observe(sidebar, { attributes: true });
    }
}

// Load Pengumuman
async function loadPengumuman() {
    const tableBody = document.getElementById('tableBody');
    
    try {
        const response = await fetch('proses_pengumuman.php?action=get_data');
        const result = await response.json();
        
        if (result.success && result.data) {
            allData = result.data;
            renderTable(allData);
        } else {
            tableBody.innerHTML = `
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px;">
                        <i class="fas fa-inbox" style="font-size: 48px; color: #cbd5e1; margin-bottom: 15px;"></i>
                        <p style="color: #94a3b8; font-size: 16px;">Tidak ada data pengumuman</p>
                    </td>
                </tr>
            `;
        }
    } catch (error) {
        console.error('Error:', error);
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px;">
                    <i class="fas fa-exclamation-circle" style="font-size: 48px; color: #ef4444; margin-bottom: 15px;"></i>
                    <p style="color: #ef4444; font-size: 16px;">Gagal memuat data</p>
                </td>
            </tr>
        `;
    }
}

// Render table
function renderTable(data) {
    const tableBody = document.getElementById('tableBody');
    document.getElementById('totalResult').textContent = data.length;
    
    if (data.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="6" style="text-align: center; padding: 40px;">
                    <i class="fas fa-search" style="font-size: 48px; color: #cbd5e1; margin-bottom: 15px;"></i>
                    <p style="color: #94a3b8; font-size: 16px;">Tidak ada hasil yang ditemukan</p>
                </td>
            </tr>
        `;
        return;
    }
    
    let html = '';
    data.forEach((item, index) => {
        const statusClass = item.status === 'Aktif' ? 'status-aktif' : 'status-nonaktif';
        const isChecked = selectedItems.includes(item.id_pengumuman);
        
        html += `
            <tr>
                <td>
                    ${isCheckMode ? `
                        <input type="checkbox" 
                               class="checkbox item-checkbox" 
                               value="${item.id_pengumuman}"
                               ${isChecked ? 'checked' : ''}
                               onchange="handleCheckboxChange()">
                    ` : `
                        <span style="color: #94a3b8; font-weight: 500;">${item.id_pengumuman}</span>
                    `}
                </td>
                <td>
                    <div class="news-content" style="max-width: 500px;">${truncateText(item.isi, 150)}</div>
                </td>
                <td>${formatDate(item.tanggal_terbit)}</td>
                <td>${item.uploader}</td>
                <td>
                    <span class="status-badge ${statusClass}">${item.status}</span>
                </td>
                <td>
                    <div class="action-buttons" style="display: ${isCheckMode ? 'none' : 'flex'}">
                        <button class="btn-action btn-more" 
                                onclick="toggleDropdown(event, ${item.id_pengumuman})"
                                title="Lainnya">
                            <i class="fas fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu" id="dropdown-${item.id_pengumuman}">
                            <button class="dropdown-item" onclick="editPengumuman(${item.id_pengumuman})">
                                <i class="fas fa-edit"></i>
                                <span>Edit</span>
                            </button>
                            <button class="dropdown-item delete-item" onclick="showDeleteModal(${item.id_pengumuman})">
                                <i class="fas fa-trash"></i>
                                <span>Hapus</span>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    });
    
    tableBody.innerHTML = html;
}

// Toggle dropdown menu
function toggleDropdown(event, id) {
    event.stopPropagation();
    
    const dropdown = document.getElementById(`dropdown-${id}`);
    const allDropdowns = document.querySelectorAll('.dropdown-menu');
    
    // Close all other dropdowns
    allDropdowns.forEach(d => {
        if (d.id !== `dropdown-${id}`) {
            d.classList.remove('active');
        }
    });
    
    // Toggle current dropdown
    dropdown.classList.toggle('active');
    activeDropdown = dropdown.classList.contains('active') ? dropdown : null;
}

// Close all dropdowns
function closeAllDropdowns() {
    const allDropdowns = document.querySelectorAll('.dropdown-menu');
    allDropdowns.forEach(d => d.classList.remove('active'));
    activeDropdown = null;
}

// Truncate text helper
function truncateText(text, maxLength) {
    if (!text) return '';
    const stripped = text.replace(/<[^>]*>/g, '');
    if (stripped.length <= maxLength) return stripped;
    return stripped.substr(0, maxLength) + '...';
}

// Format date
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}-${month}-${year}`;
}

// Toggle Check Mode
function toggleCheckMode() {
    isCheckMode = !isCheckMode;
    selectedItems = [];
    
    const editModeHeader = document.getElementById('editModeHeader');
    const btnCheckMode = document.getElementById('btnCheckMode');
    const checkModeText = document.getElementById('checkModeText');
    
    if (isCheckMode) {
        editModeHeader.classList.add('active');
        btnCheckMode.classList.add('active');
        checkModeText.textContent = 'Pilih';
    } else {
        editModeHeader.classList.remove('active');
        btnCheckMode.classList.remove('active');
        checkModeText.textContent = 'ID';
    }
    
    renderTable(allData);
    updateSelectedCount();
}

// Handle checkbox change
function handleCheckboxChange() {
    selectedItems = [];
    const checkboxes = document.querySelectorAll('.item-checkbox:checked');
    checkboxes.forEach(cb => selectedItems.push(parseInt(cb.value)));
    updateSelectedCount();
}

// Update selected count
function updateSelectedCount() {
    document.getElementById('selectedCount').textContent = selectedItems.length;
}

// Search table
function searchTable() {
    const searchInput = document.getElementById('searchInput').value.toLowerCase();
    
    const filteredData = allData.filter(item => {
        return item.isi.toLowerCase().includes(searchInput) ||
               item.uploader.toLowerCase().includes(searchInput) ||
               item.id_pengumuman.toString().includes(searchInput);
    });
    
    renderTable(filteredData);
}

// Edit Pengumuman
function editPengumuman(id) {
    closeAllDropdowns();
    window.location.href = `edit_pengumuman.php?id=${id}`;
}

// Show delete modal
function showDeleteModal(id) {
    closeAllDropdowns();
    deleteId = id;
    document.getElementById('deleteModal').classList.add('active');
}

// Close delete modal
function closeDeleteModal() {
    deleteId = null;
    document.getElementById('deleteModal').classList.remove('active');
}

// Confirm delete
async function confirmDelete() {
    if (!deleteId) return;
    
    const formData = new FormData();
    formData.append('action', 'hapus');
    formData.append('id', deleteId);
    
    try {
        const response = await fetch('proses_pengumuman.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeDeleteModal();
            loadPengumuman();
            showNotification('Pengumuman berhasil dihapus', 'success');
        } else {
            showNotification('Error: ' + result.message, 'error');
        }
    } catch (error) {
        showNotification('Terjadi kesalahan: ' + error.message, 'error');
    }
}

// Delete selected items
async function deleteSelected() {
    if (selectedItems.length === 0) {
        showNotification('Pilih minimal satu item untuk dihapus', 'error');
        return;
    }
    
    if (!confirm(`Hapus ${selectedItems.length} pengumuman yang dipilih?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'hapus_multiple');
    formData.append('ids', JSON.stringify(selectedItems));
    
    try {
        const response = await fetch('proses_pengumuman.php', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            toggleCheckMode();
            loadPengumuman();
            showNotification(result.message, 'success');
        } else {
            showNotification('Error: ' + result.message, 'error');
        }
    } catch (error) {
        showNotification('Terjadi kesalahan: ' + error.message, 'error');
    }
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        background: ${type === 'success' ? '#10b981' : '#ef4444'};
        color: white;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 10000;
        animation: slideIn 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    `;
    
    const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle';
    notification.innerHTML = `
        <i class="fas ${icon}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOut {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);