<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengumuman - Dashboard Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f5f7fa;
            overflow-x: hidden;
        }

        .main-container {
            margin-left: 80px;
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .main-container.sidebar-open {
            margin-left: 280px;
        }

        .header {
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            font-size: 28px;
            color: #1e293b;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .filter-section {
            background: white;
            padding: 25px 30px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .filter-row {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .search-box {
            flex: 1;
            min-width: 300px;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 12px 45px 12px 20px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
        }

        .search-box input:focus {
            outline: none;
            border-color: #3b82f6;
        }

        .search-box i {
            position: absolute;
            right: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        .filter-badge {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 20px;
            background: #f1f5f9;
            border-radius: 8px;
        }

        .btn-add {
            padding: 12px 30px;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }

        .table-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #f8fafc;
        }

        thead th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 600;
            color: #475569;
            font-size: 13px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid #e2e8f0;
        }

        tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.2s;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        tbody td {
            padding: 20px;
            color: #334155;
            font-size: 14px;
        }

        .checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #3b82f6;
        }

        .news-content {
            font-size: 13px;
            color: #64748b;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .status-badge {
            padding: 6px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-aktif {
            background: #dcfce7;
            color: #16a34a;
        }

        .status-nonaktif {
            background: #fee2e2;
            color: #dc2626;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
            position: relative;
        }

        .btn-action {
            width: 35px;
            height: 35px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .btn-more {
            background: #f8fafc;
            color: #64748b;
        }

        .btn-more:hover {
            background: #e2e8f0;
        }

        /* Dropdown Menu */
        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            min-width: 160px;
            z-index: 1000;
            display: none;
            margin-top: 5px;
        }

        .dropdown-menu.active {
            display: block;
        }

        .dropdown-item {
            padding: 12px 20px;
            display: flex;
            align-items: center;
            gap: 12px;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            font-size: 14px;
            color: #334155;
        }

        .dropdown-item:first-child {
            border-radius: 8px 8px 0 0;
        }

        .dropdown-item:last-child {
            border-radius: 0 0 8px 8px;
        }

        .dropdown-item:hover {
            background: #f8fafc;
        }

        .dropdown-item.delete-item {
            color: #ef4444;
        }

        .dropdown-item.delete-item:hover {
            background: #fef2f2;
        }

        /* Checkbox column */
        .checkbox-column {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-check {
            width: 35px;
            height: 35px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
            background: white;
            color: #94a3b8;
        }

        .btn-check:hover {
            border-color: #3b82f6;
            color: #3b82f6;
        }

        .btn-check.active {
            background: #3b82f6;
            border-color: #3b82f6;
            color: white;
        }

        /* Edit Mode */
        .edit-mode-header {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            color: white;
            padding: 15px 30px;
            border-radius: 10px;
            margin-bottom: 20px;
            display: none;
            align-items: center;
            justify-content: space-between;
        }

        .edit-mode-header.active {
            display: flex;
        }

        .btn-delete-selected {
            padding: 10px 25px;
            background: white;
            color: #dc2626;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-delete-selected:hover {
            background: #fee2e2;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            max-width: 450px;
            width: 90%;
            text-align: center;
        }

        .modal-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: #fee2e2;
            color: #dc2626;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
        }

        .modal-content h2 {
            font-size: 22px;
            color: #1e293b;
            margin-bottom: 10px;
        }

        .modal-content p {
            color: #64748b;
            margin-bottom: 25px;
        }

        .modal-buttons {
            display: flex;
            gap: 15px;
            justify-content: center;
        }

        .btn-modal {
            padding: 12px 30px;
            border: none;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #475569;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
        }

        .btn-confirm {
            background: #dc2626;
            color: white;
        }

        .btn-confirm:hover {
            background: #b91c1c;
        }

        /* Responsive */
        @media (max-width: 1024px) {
            .main-container {
                margin-left: 0;
                padding: 15px;
            }

            .main-container.sidebar-open {
                margin-left: 0;
            }

            .filter-row {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                min-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }

            table {
                font-size: 12px;
            }
        }
    </style>
</head>
<body>
    <div id="sidebar"></div>

    <div class="main-container" id="mainContainer">
        <div class="header">
            <h1>Pengumuman</h1>
            <div class="user-info">
                <span>Maria Savira</span>
                <div class="user-avatar">MS</div>
            </div>
        </div>

        <div class="edit-mode-header" id="editModeHeader">
            <div>
                <i class="fas fa-check-circle me-2"></i>
                <strong id="selectedCount">0</strong> item dipilih
            </div>
            <div style="display: flex; gap: 15px;">
                <button class="btn-delete-selected" onclick="deleteSelected()">
                    <i class="fas fa-trash"></i>
                    <span>Hapus Terpilih</span>
                </button>
                <button class="btn-cancel" style="padding: 10px 25px; border-radius: 8px;" onclick="toggleCheckMode()">
                    <i class="fas fa-times me-2"></i> Batal
                </button>
            </div>
        </div>

        <div class="filter-section">
            <div class="filter-row">
                <div class="search-box">
                    <input type="text" id="searchInput" placeholder="Cari pengumuman..." onkeyup="searchTable()">
                    <i class="fas fa-search"></i>
                </div>
                <div class="filter-badge">
                    <span><strong id="totalResult">0</strong> hasil</span>
                </div>
                <button class="btn-add" onclick="window.location.href='tambah_pengumuman.php'">
                    <i class="fas fa-plus"></i> Tambah
                </button>
            </div>
        </div>

        <div class="table-container">
            <table id="pengumumanTable">
                <thead>
                    <tr>
                        <th style="width: 60px;">
                            <button class="btn-check" id="btnCheckMode" onclick="toggleCheckMode()" title="Pilih Item">
                                <i class="fas fa-check"></i>
                            </button>
                        </th>
                        <th style="width: 80px;">ID</th>
                        <th>Isi Pengumuman</th>
                        <th style="width: 120px;">Tanggal</th>
                        <th style="width: 150px;">Uploader</th>
                        <th style="width: 100px;">Status</th>
                        <th style="width: 120px;">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 24px; color: #94a3b8;"></i>
                            <p style="margin-top: 10px; color: #94a3b8;">Memuat data...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal Konfirmasi Hapus -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <h2>Hapus Pengumuman?</h2>
            <p>Data yang dihapus tidak dapat dikembalikan</p>
            <div class="modal-buttons">
                <button class="btn-modal btn-cancel" onclick="closeDeleteModal()">Batal</button>
                <button class="btn-modal btn-confirm" onclick="confirmDelete()">Ya, Hapus</button>
            </div>
        </div>
    </div>

    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script>
        // Global variables
        let isCheckMode = false;
        let selectedItems = [];
        let allData = [];
        let deleteId = null;

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
                            <td colspan="7" style="text-align: center; padding: 40px;">
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
                        <td colspan="7" style="text-align: center; padding: 40px;">
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
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            <i class="fas fa-search" style="font-size: 48px; color: #cbd5e1; margin-bottom: 15px;"></i>
                            <p style="color: #94a3b8; font-size: 16px;">Tidak ada hasil yang ditemukan</p>
                        </td>
                    </tr>
                `;
                return;
            }
            
            let html = '';
            data.forEach((item) => {
                const statusClass = item.status === 'Aktif' ? 'status-aktif' : 'status-nonaktif';
                const isChecked = selectedItems.includes(item.id_pengumuman);
                
                html += `
                    <tr>
                        <td style="text-align: center;">
                            ${isCheckMode ? `
                                <input type="checkbox" 
                                       class="checkbox item-checkbox" 
                                       value="${item.id_pengumuman}"
                                       ${isChecked ? 'checked' : ''}
                                       onchange="handleCheckboxChange()">
                            ` : `
                                <span style="color: #cbd5e1;">—</span>
                            `}
                        </td>
                        <td>
                            <span style="color: #94a3b8; font-weight: 500;">${item.id_pengumuman}</span>
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
        }

        // Close all dropdowns
        function closeAllDropdowns() {
            const allDropdowns = document.querySelectorAll('.dropdown-menu');
            allDropdowns.forEach(d => d.classList.remove('active'));
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
            
            if (isCheckMode) {
                editModeHeader.classList.add('active');
                btnCheckMode.classList.add('active');
            } else {
                editModeHeader.classList.remove('active');
                btnCheckMode.classList.remove('active');
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
            
            // Hapus satu per satu
            let successCount = 0;
            let failCount = 0;
            
            for (const id of selectedItems) {
                try {
                    const formData = new FormData();
                    formData.append('action', 'hapus');
                    formData.append('id', id);
                    
                    const response = await fetch('proses_pengumuman.php', {
                        method: 'POST',
                        body: formData
                    });
                    
                    const result = await response.json();
                    
                    if (result.success) {
                        successCount++;
                    } else {
                        failCount++;
                    }
                } catch (error) {
                    failCount++;
                }
            }
            
            // Reset mode dan reload
            toggleCheckMode();
            await loadPengumuman();
            
            // Show result
            if (failCount === 0) {
                showNotification(`${successCount} pengumuman berhasil dihapus`, 'success');
            } else {
                showNotification(`${successCount} berhasil, ${failCount} gagal dihapus`, 'error');
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
    </script>
</body>
</html>