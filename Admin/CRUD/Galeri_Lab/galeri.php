<?php
// galeri.php - Main Gallery Page (COMPLETE FIXED VERSION)
session_start();
require_once 'config.php';

// Ambil data galeri dari database
try {
    $stmt = $pdo->query("SELECT * FROM galeri ORDER BY tanggal_upload DESC");
    $galeri_items = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
    $galeri_items = [];
}

// Ambil informasi user (sesuaikan dengan sistem login Anda)
$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Maria Savira';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri - Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: #f5f7fa;
            display: flex;
        }

        /* Sidebar placeholder */
        #sidebar {
            width: 80px;
            transition: width 0.3s ease;
        }

        #sidebar.active {
            width: 250px;
        }

        /* Main content */
        .main-content {
            flex: 1;
            padding: 30px;
            margin-left: 0;
            transition: margin-left 0.3s ease;
        }

        .main-content.shifted {
            margin-left: 170px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header h1 {
            font-size: 32px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .user-name {
            font-size: 14px;
            color: #333;
            font-weight: 500;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: #cbd5e1;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .user-avatar svg {
            width: 24px;
            height: 24px;
            fill: #64748b;
        }

        /* Controls */
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .left-controls {
            display: flex;
            gap: 15px;
        }

        .left-controls .btn-secondary {
            display: none;
        }

        .edit-mode-active .left-controls .btn-secondary {
            display: inline-flex !important;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-secondary {
            background: white;
            color: #333;
            border: 1px solid #e2e8f0;
        }

        .btn-secondary:hover {
            background: #f8fafc;
        }

        .right-controls {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .edit-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .edit-toggle label {
            font-size: 14px;
            color: #64748b;
            cursor: pointer;
        }

        .toggle-switch {
            position: relative;
            width: 48px;
            height: 26px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #cbd5e1;
            transition: 0.3s;
            border-radius: 26px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 20px;
            width: 20px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: 0.3s;
            border-radius: 50%;
        }

        .toggle-switch input:checked + .slider {
            background-color: #2563eb;
        }

        .toggle-switch input:checked + .slider:before {
            transform: translateX(22px);
        }

        .pagination-info {
            font-size: 14px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .pagination-nav {
            display: flex;
            gap: 8px;
        }

        .pagination-btn {
            width: 32px;
            height: 32px;
            border: 1px solid #e2e8f0;
            background: white;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .pagination-btn:hover {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .pagination-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Gallery Grid */
        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .gallery-item {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            background: white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            cursor: default;
        }

        .gallery-item:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        .edit-mode-active .gallery-item {
            cursor: pointer;
        }

        .gallery-item img {
            width: 100%;
            height: 220px;
            object-fit: cover;
            display: block;
            pointer-events: none;
        }

        .select-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(37, 99, 235, 0.15);
            display: none;
            pointer-events: none;
            z-index: 1;
        }

        .select-checkbox {
            position: absolute;
            top: 15px;
            right: 15px;
            width: 28px;
            height: 28px;
            background: white;
            border: 2px solid #cbd5e1;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 10;
            transition: all 0.3s ease;
            pointer-events: auto;
        }

        .edit-mode-active .select-checkbox {
            display: flex !important;
        }

        .gallery-item.selected .select-checkbox {
            background: #2563eb !important;
            border-color: #2563eb !important;
        }

        .gallery-item.selected .select-checkbox::after {
            content: '✓';
            color: white;
            font-size: 14px;
            font-weight: bold;
        }

        .gallery-item.selected .select-overlay {
            display: block !important;
        }

        .gallery-item.selected {
            box-shadow: 0 0 0 3px #2563eb !important;
        }

        /* Delete notification */
        .delete-notification {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: white;
            padding: 16px 24px;
            border-radius: 8px;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
            display: none;
            align-items: center;
            gap: 15px;
            z-index: 1000;
        }

        .delete-notification.show {
            display: flex !important;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .delete-notification span {
            font-size: 14px;
            color: #333;
        }

        .btn-delete {
            padding: 8px 16px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 2000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 12px;
            max-width: 450px;
            width: 90%;
            animation: slideUp 0.3s ease;
        }

        @keyframes slideUp {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-content h3 {
            font-size: 20px;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 12px;
        }

        .modal-content p {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .modal-buttons {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
        }

        .btn-cancel {
            padding: 10px 20px;
            background: #f1f5f9;
            color: #64748b;
            border: none;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
        }

        .btn-confirm {
            padding: 10px 20px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-confirm:hover {
            background: #dc2626;
        }

        /* Alert Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }

            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .controls {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .gallery-grid {
                grid-template-columns: 1fr;
            }

            .delete-notification {
                left: 20px;
                right: 20px;
                bottom: 20px;
            }
        }
    </style>
</head>
<body>
    <div id="sidebar"></div>

    <div class="main-content">
        <!-- Header -->
        <div class="header">
            <h1>Galeri</h1>
            <div class="user-info">
                <span class="user-name"><?php echo htmlspecialchars($username); ?></span>
                <div class="user-avatar">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                        <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z"/>
                    </svg>
                </div>
            </div>
        </div>

        <?php if(isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success">
                <span>✓</span>
                <span><?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?></span>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error_message'])): ?>
            <div class="alert alert-error">
                <span>✗</span>
                <span><?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?></span>
            </div>
        <?php endif; ?>

        <!-- Controls -->
        <div class="controls">
            <div class="left-controls">
                <button class="btn btn-primary" onclick="window.location.href='tambah_galeri.php'">
                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M8 3.33334V12.6667M3.33333 8H12.6667" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                    </svg>
                    Tambah
                </button>
                <button class="btn btn-secondary" onclick="selectAll()">Pilih Semua</button>
            </div>
            <div class="right-controls">
                <div class="edit-toggle">
                    <label for="editToggle">edit</label>
                    <label class="toggle-switch">
                        <input type="checkbox" id="editToggle" onchange="toggleEditMode()">
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="pagination-info">
                    <span id="pageInfo">1 of 1</span>
                    <div class="pagination-nav">
                        <button class="pagination-btn" onclick="prevPage()">&lt;</button>
                        <button class="pagination-btn" onclick="nextPage()">&gt;</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Gallery Grid -->
        <div class="gallery-grid" id="galleryGrid">
            <?php foreach($galeri_items as $item): ?>
            <div class="gallery-item" data-id="<?php echo $item['id_galeri']; ?>">
                <div class="select-checkbox"></div>
                <img src="../../../Assets/Image/Galeri-Berita/<?php echo htmlspecialchars($item['file_path']); ?>" 
                     alt="<?php echo htmlspecialchars($item['judul']); ?>"
                     onerror="this.src='https://via.placeholder.com/320x220?text=Image+Not+Found'">
                <div class="select-overlay"></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Delete Notification -->
        <div class="delete-notification" id="deleteNotification">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none">
                <path d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" fill="#ef4444"/>
            </svg>
            <span id="deleteCount">0 item dipilih</span>
            <button class="btn-delete" onclick="confirmDelete()">Hapus data yang dipilih</button>
        </div>

        <!-- Delete Confirmation Modal -->
        <div class="modal" id="deleteModal">
            <div class="modal-content">
                <h3>Hapus data yang dipilih?</h3>
                <p>Data yang sudah dihapus tidak dapat dikembalikan lagi. Apakah Anda yakin ingin menghapus data ini?</p>
                <div class="modal-buttons">
                    <button class="btn-cancel" onclick="closeModal()">Batal</button>
                    <button class="btn-confirm" onclick="deleteSelected()">Ya, Hapus</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Load Sidebar first -->
    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    
    <!-- Load Gallery script last -->
    <script src="../../../Assets/Javascript/Admin/galeri.js"></script>
    
    <!-- Debug: Check if scripts loaded -->
    <script>
        console.log('HTML loaded, waiting for gallery.js...');
    </script>
</body>
</html>