<?php
// admin/berita.php
session_start();
require_once 'config.php';

// Cek login (sesuaikan dengan sistem login Anda)
// if (!isset($_SESSION['user_id'])) {
//     header('Location: login.php');
//     exit;
// }

$user_name = $_SESSION['user_name'] ?? 'Maria Savira';

// Handle pencarian dan filter
$search = $_GET['search'] ?? '';
$filter = $_GET['filter'] ?? '';

$query = "SELECT * FROM berita WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (judul ILIKE ? OR isi ILIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($filter) {
    $query .= " AND status = ?";
    $params[] = $filter;
}

$query .= " ORDER BY tanggal DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$berita_list = $stmt->fetchAll();
$total_hasil = count($berita_list);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita - Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Sidebar Styles */
        #sidebar {
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            z-index: 999;
        }
        
        /* Main content responsive untuk sidebar */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            background: #f5f7fa;
            min-height: 100vh;
        }

        .main-content {
            margin-left: 80px;
            padding: 30px;
            transition: margin-left 0.3s ease;
        }

        /* Sidebar expanded state */
        body.sidebar-expanded .main-content {
            margin-left: 280px;
        }
        
        /* Responsive sidebar */
        @media (max-width: 1024px) {
            body.sidebar-expanded .main-content {
                margin-left: 250px;
            }
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 32px;
            color: #2c3e50;
            font-weight: 600;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-info span {
            color: #2c3e50;
            font-weight: 500;
        }

        .user-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: #3b82f6;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }

        .search-bar {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .search-container {
            display: flex;
            gap: 15px;
            align-items: center;
        }

        .search-box {
            flex: 1;
            display: flex;
            align-items: center;
            background: #f8f9fa;
            border-radius: 10px;
            padding: 12px 20px;
            gap: 10px;
        }

        .search-box input {
            border: none;
            background: transparent;
            flex: 1;
            font-size: 15px;
            color: #2c3e50;
            outline: none;
        }

        .search-box input::placeholder {
            color: #94a3b8;
        }

        .hasil-info {
            color: #64748b;
            font-weight: 500;
        }

        .btn-tambah {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 25px;
            border-radius: 10px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .btn-tambah:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }

        .filter-section {
            display: flex;
            gap: 10px;
            align-items: center;
            margin-top: 15px;
        }

        .filter-tag {
            background: #e0e7ff;
            color: #3b82f6;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-tag .close {
            cursor: pointer;
            font-weight: bold;
        }

        .filter-btn {
            background: transparent;
            border: none;
            color: #64748b;
            font-size: 14px;
            cursor: pointer;
            text-decoration: underline;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #64748b;
            font-size: 14px;
        }

        .pagination button {
            background: white;
            border: 1px solid #e2e8f0;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .pagination button:hover {
            background: #f8f9fa;
            border-color: #3b82f6;
        }

        .table-container {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background: #dbeafe;
        }

        th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 500;
            color: #1e293b;
            font-size: 14px;
        }

        tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: all 0.3s;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        td {
            padding: 20px;
            color: #475569;
            font-size: 14px;
        }

        .checkbox {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #3b82f6;
        }

        .berita-thumbnail {
            width: 120px;
            height: 80px;
            border-radius: 8px;
            object-fit: cover;
        }

        .berita-judul {
            font-weight: 500;
            color: #1e293b;
            margin-bottom: 5px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .berita-isi {
            color: #64748b;
            font-size: 13px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: 500;
            display: inline-block;
        }

        .status-publish {
            background: #d1fae5;
            color: #065f46;
        }

        .status-draft {
            background: #fef3c7;
            color: #92400e;
        }

        .menu-dots {
            background: transparent;
            border: none;
            font-size: 20px;
            cursor: pointer;
            padding: 5px 10px;
            color: #64748b;
            position: relative;
        }

        .menu-dots:hover {
            background: #f1f5f9;
            border-radius: 6px;
        }

        .dropdown-menu {
            position: absolute;
            right: 0;
            top: 100%;
            background: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            min-width: 150px;
            z-index: 100;
            display: none;
            margin-top: 5px;
        }

        .dropdown-menu.show {
            display: block;
        }

        .dropdown-menu a {
            display: block;
            padding: 12px 20px;
            color: #475569;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s;
        }

        .dropdown-menu a:hover {
            background: #f8fafc;
        }

        .dropdown-menu a:first-child {
            border-radius: 8px 8px 0 0;
        }

        .dropdown-menu a:last-child {
            border-radius: 0 0 8px 8px;
            color: #dc2626;
        }

        .dropdown-menu a:last-child:hover {
            background: #fef2f2;
        }


        .toast.show {
            display: flex;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
            }
            to {
                transform: translateX(0);
            }
        }

        /* Modal Hapus */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal {
            background: white;
            border-radius: 16px;
            padding: 30px;
            max-width: 400px;
            width: 90%;
            text-align: center;
        }

        .modal h3 {
            color: #1e293b;
            margin-bottom: 15px;
            font-size: 20px;
        }

        .modal p {
            color: #64748b;
            margin-bottom: 25px;
            line-height: 1.6;
        }

        .modal-buttons {
            display: flex;
            gap: 10px;
            justify-content: center;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #475569;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
        }

        .btn-delete {
            background: #dc2626;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 10px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-delete:hover {
            background: #b91c1c;
        }
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: white;
            padding: 15px 20px;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            display: none;
            align-items: center;
            gap: 15px;
            z-index: 1000;
        }

        .btn-delete-bulk {
            background: #dc2626;
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 14px;
            transition: all 0.3s;
        }

        .btn-delete-bulk:hover {
            background: #b91c1c;
            transform: translateY(-1px);
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            body.sidebar-expanded .main-content {
                margin-left: 0;
            }

            .search-container {
                flex-direction: column;
            }

            table {
                font-size: 13px;
            }

            .berita-thumbnail {
                width: 80px;
                height: 60px;
            }
        }
        .alert {
    padding: 15px 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    animation: slideIn 0.3s ease;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border-left: 4px solid #10b981;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #ef4444;
}

@keyframes slideOut {
    to {
        transform: translateX(400px);
        opacity: 0;
    }
}
    </style>
</head>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION['success']) ?>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-error">
        <?= htmlspecialchars($_SESSION['error']) ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<body>
    <!-- Sidebar -->
    <div id="sidebar"></div>
    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    
    <div class="main-content" id="mainContent">
        <div class="header">
            <h1>Berita</h1>
            <div class="user-info">
                <span><?= htmlspecialchars($user_name) ?></span>
                <div class="user-avatar">
                    <?= strtoupper(substr($user_name, 0, 1)) ?>
                </div>
            </div>
        </div>

        <div class="search-bar">
            <form method="GET" class="search-container">
                <div class="search-box">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                    </svg>
                    <input type="text" name="search" placeholder="Cari" value="<?= htmlspecialchars($search) ?>">
                </div>
                <span class="hasil-info"><?= $total_hasil ?> hasil</span>
                <button type="button" class="btn-tambah" onclick="location.href='tambah_berita.php'">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 5v14M5 12h14"/>
                    </svg>
                    Tambah
                </button>
            </form>

            <?php if ($filter): ?>
            <div class="filter-section">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M3 6h18M7 12h10M11 18h2"/>
                </svg>
                <div class="filter-tag">
                    <?= ucfirst($filter) ?>
                    <span class="close" onclick="location.href='berita.php'">×</span>
                </div>
                <button class="filter-btn" onclick="location.href='berita.php'">Hapus Filter</button>
            </div>
            <?php endif; ?>

            <div class="filter-section" style="justify-content: space-between; margin-top: 15px;">
                <div class="pagination">
                    <span>1 of 1</span>
                    <button>‹</button>
                    <button>›</button>
                </div>
            </div>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" class="checkbox" id="checkAll">
                        </th>
                        <th style="width: 80px;">id_berita</th>
                        <th>judul</th>
                        <th>isi</th>
                        <th>gambar</th>
                        <th>tanggal</th>
                        <th>uploader</th>
                        <th>status</th>
                        <th style="width: 50px;"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($berita_list)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 40px; color: #94a3b8;">
                            Tidak ada data berita
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach ($berita_list as $index => $berita): ?>
                        <tr>
                        <td>
                            <input type="checkbox" class="checkbox" value="<?= $berita['id_berita'] ?>">
                        </td>
                            <td><?= str_pad($berita['id_berita'], 2, '0', STR_PAD_LEFT) ?></td>
                            <td style="max-width: 300px;">
                                <div class="berita-judul"><?= htmlspecialchars($berita['judul']) ?></div>
                            </td>
                            <td style="max-width: 400px;">
                                <div class="berita-isi"><?= htmlspecialchars(substr($berita['isi'], 0, 150)) ?>...</div>
                            </td>
                            <td>
                                            <?php if ($berita['gambar']): ?>
                                                <img src="../../../Assets/Image/Galeri-Berita/<?= htmlspecialchars($berita['gambar']) ?>" 
                                                    alt="Gambar Berita" 
                                                    class="berita-thumbnail">
                                            <?php else: ?>
                                    <div style="width: 120px; height: 80px; background: #f1f5f9; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8;">
                                        No Image
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d-m-Y', strtotime($berita['tanggal'])) ?></td>
                            <td><?= htmlspecialchars($berita['uploaded_by']) ?></td>
                            <td>
                                <span class="status-badge status-<?= strtolower($berita['status']) ?>">
                                    <?= ucfirst($berita['status']) ?>
                                </span>
                            </td>
                            <td style="position: relative;">
                                <button class="menu-dots" onclick="toggleMenu(event, <?= $berita['id_berita'] ?>)">⋮</button>
                                <div class="dropdown-menu" id="menu-<?= $berita['id_berita'] ?>">
                                    <a href="edit_berita.php?id=<?= $berita['id_berita'] ?>">Edit</a>
                                    <a href="#" onclick="confirmDelete(<?= $berita['id_berita'] ?>, '<?= htmlspecialchars(addslashes($berita['judul'])) ?>'); return false;">Hapus</a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <div class="toast" id="deleteToast">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            <span id="toastText">Hapus data yang dipilih</span>
            <button class="btn-delete-bulk" onclick="confirmBulkDelete()">Hapus</button>
        </div>
    </div>

    <!-- Modal Hapus -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal">
            <h3>Hapus Berita?</h3>
            <p id="deleteMessage">Apakah Anda yakin ingin menghapus berita ini? Tindakan ini tidak dapat dibatalkan.</p>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeDeleteModal()">Batal</button>
                <button class="btn-delete" id="confirmDeleteBtn">Hapus</button>
            </div>
        </div>
    </div>

    <script src="../../../Assets/Javascript/Admin/berita.js"></script>
   <script>
// Toggle dropdown menu
function toggleMenu(event, id) {
    event.stopPropagation();
    const menu = document.getElementById('menu-' + id);
    const allMenus = document.querySelectorAll('.dropdown-menu');
    
    allMenus.forEach(m => {
        if (m !== menu) m.classList.remove('show');
    });
    
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

// Check all functionality
document.getElementById('checkAll')?.addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('tbody .checkbox');
    checkboxes.forEach(cb => cb.checked = this.checked);
    updateToast();
});

// Show toast when individual checkboxes are checked
document.querySelectorAll('tbody .checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        updateToast();
        
        // Update check all status
        const allCheckboxes = document.querySelectorAll('tbody .checkbox');
        const checkedBoxes = document.querySelectorAll('tbody .checkbox:checked');
        const checkAll = document.getElementById('checkAll');
        if (checkAll) {
            checkAll.checked = allCheckboxes.length === checkedBoxes.length && checkedBoxes.length > 0;
        }
    });
});

// Update toast notification
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
        closeDeleteModal();
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
    
    // Set handler untuk single delete
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    confirmBtn.onclick = function() {
        if (deleteId) {
            window.location.href = 'hapus_berita.php?id=' + deleteId;
        }
    };
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('show');
    deleteId = null;
}

// Close modal on outside click
document.getElementById('deleteModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Close modal on ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});

// Sidebar toggle
document.addEventListener('DOMContentLoaded', function() {
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
    
    setTimeout(checkSidebarState, 100);
    
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                checkSidebarState();
            }
        });
    });
    
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
    
    document.addEventListener('sidebarToggle', checkSidebarState);
    window.addEventListener('sidebarStateChange', checkSidebarState);
});

console.log('Berita script loaded');
</script>
</body>
</html>