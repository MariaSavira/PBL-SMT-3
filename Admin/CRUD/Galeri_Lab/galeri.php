<?php
session_start();
require_once 'config.php';

try {
    $stmt = $pdo->query("SELECT * FROM galeri ORDER BY tanggal_upload DESC");
    $galeri_items = $stmt->fetchAll();
} catch(PDOException $e) {
    $error = "Error: " . $e->getMessage();
    $galeri_items = [];
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Maria Savira';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="icon" type="images/x-icon"
        href="../../../Assets/Image/Logo/Logo Without Text.png" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="../../../Assets/Css/Admin/Galeri.css">
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

    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script src="../../../Assets/Javascript/Admin/galeri.js"></script>
    <script src="../../../Assets/Javascript/Admin/Header.js"></script>
</body>
</html>