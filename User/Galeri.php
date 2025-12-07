<!DOCTYPE html>
<html lang="id">
<?php
    require_once __DIR__ . '/../Admin/Cek_Autentikasi.php';
?>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Galeri Laboratorium</title>

    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet"
    />
    <link rel="icon" type="image/x-icon" href="../Assets/Image/Logo/Logo Without Text.png" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
    />
    <!-- CSS terpisah -->
    <link rel="stylesheet" href="../Assets/Css/galeri.css" />
    
    <!-- Additional CSS untuk loading & modal -->
    <style>
      /* Loading State */
      .loading-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        min-height: 400px;
        padding: 60px 20px;
      }

      .spinner {
        width: 50px;
        height: 50px;
        border: 4px solid #f3f3f3;
        border-top: 4px solid #2563eb;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin-bottom: 20px;
      }

      @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
      }

      .loading-text {
        color: #64748b;
        font-size: 14px;
        font-family: 'Poppins', sans-serif;
      }

      /* Error State */
      .error-wrapper {
        background: #fee2e2;
        color: #991b1b;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        margin: 20px auto;
        max-width: 600px;
      }

      /* Empty State */
      .empty-wrapper {
        text-align: center;
        padding: 80px 20px;
      }

      .empty-icon {
        width: 100px;
        height: 100px;
        background: #e2e8f0;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
      }

      .empty-icon i {
        font-size: 50px;
        color: #94a3b8;
      }

      .empty-wrapper h3 {
        font-size: 20px;
        color: #475569;
        margin-bottom: 8px;
      }

      .empty-wrapper p {
        font-size: 14px;
        color: #94a3b8;
      }

      /* Modal Overlay */
      .modal-overlay {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.85);
        animation: fadeIn 0.3s ease;
      }

      .modal-overlay.show {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
      }

      .modal-content-box {
        background: white;
        border-radius: 16px;
        max-width: 900px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
        animation: slideUp 0.3s ease;
      }

      @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
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

      .modal-image-box {
        width: 100%;
        max-height: 500px;
        object-fit: contain;
        background: #000;
        border-radius: 16px 16px 0 0;
      }

      .modal-body-box {
        padding: 30px;
      }

      .modal-title-box {
        font-size: 24px;
        font-weight: 600;
        color: #1a1a1a;
        margin-bottom: 12px;
        font-family: 'Poppins', sans-serif;
      }

      .modal-description-box {
        font-size: 15px;
        color: #475569;
        line-height: 1.7;
        margin-bottom: 20px;
        font-family: 'Poppins', sans-serif;
      }

      .modal-meta-box {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 20px;
        border-top: 1px solid #e2e8f0;
      }

      .modal-author-box {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 14px;
        color: #64748b;
        font-family: 'Poppins', sans-serif;
      }

      .author-icon-box {
        width: 36px;
        height: 36px;
        background: #e0e7ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4f46e5;
        font-weight: 600;
        font-size: 14px;
      }

      .modal-date-box {
        font-size: 13px;
        color: #94a3b8;
        font-family: 'Poppins', sans-serif;
      }

      .modal-close-btn {
        position: absolute;
        top: 20px;
        right: 20px;
        width: 40px;
        height: 40px;
        background: white;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #64748b;
        transition: all 0.3s ease;
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      }

      .modal-close-btn:hover {
        background: #f1f5f9;
        color: #1a1a1a;
      }

      /* Cursor pointer untuk gallery items */
      .gallery-img {
        cursor: pointer;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
      }

      .gallery-img:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
      }

      @media (max-width: 768px) {
        .modal-content-box {
          margin: 10px;
        }

        .modal-body-box {
          padding: 20px;
        }

        .modal-title-box {
          font-size: 20px;
        }
      }
    </style>
  </head>

  <body>
    <div id="header"></div>

    <!-- HERO HEADING -->
    <section class="heading">
      <h1>Galeri Laboratorium</h1>
      <p>
        Jelajahi berbagai dokumentasi kegiatan, riset, dan momen penting yang
        terjadi di Laboratorium Business Analytics
      </p>
    </section>

    <!-- Loading State -->
    <div id="loadingState" class="loading-wrapper">
      <div class="spinner"></div>
      <p class="loading-text">Memuat galeri...</p>
    </div>

    <!-- Error State -->
    <div id="errorState" class="error-wrapper" style="display: none;">
      <i class="fas fa-exclamation-triangle" style="font-size: 24px; margin-bottom: 10px;"></i>
      <p>Gagal memuat galeri. Silakan refresh halaman.</p>
    </div>

    <!-- Empty State -->
    <div id="emptyState" class="empty-wrapper" style="display: none;">
      <div class="empty-icon">
        <i class="fas fa-images"></i>
      </div>
      <h3>Belum Ada Galeri</h3>
      <p>Galeri akan ditampilkan setelah admin menambahkan konten</p>
    </div>

    <!-- GALLERY (akan diisi dinamis dari API) -->
    <div class="gallery-wrapper" id="galleryWrapper" style="display: none;">
      <div class="gallery-row" id="galleryRow">
        <!-- Images akan diisi oleh JavaScript -->
      </div>
    </div>

    <!-- Modal Detail -->
    <div id="galleryModal" class="modal-overlay">
      <div class="modal-content-box">
        <button class="modal-close-btn" onclick="closeModal()">&times;</button>
        <img id="modalImage" class="modal-image-box" src="" alt="">
        <div class="modal-body-box">
          <h2 id="modalTitle" class="modal-title-box"></h2>
          <p id="modalDescription" class="modal-description-box"></p>
          <div class="modal-meta-box">
            <div class="modal-author-box">
              <div class="author-icon-box" id="modalAuthorIcon"></div>
              <span id="modalAuthor"></span>
            </div>
            <div class="modal-date-box" id="modalDate"></div>
          </div>
        </div>
      </div>
    </div>

    <div id="footer"></div>
    
    <script src="../Assets/Javascript/HeaderFooter.js"></script>
    
    <!-- Gallery API Script -->
    <script>
      // ========================================
      // KONFIGURASI API
      // ========================================
      // SESUAIKAN PATH INI dengan struktur folder Anda!
      const API_URL = '../Admin/CRUD/Galeri_Lab/api_galeri.php';

      // Class variants untuk masonry layout (sesuai CSS Anda)
      const cardClasses = ['card-385', 'card-338', 'card-496', 'card-438', 'card-239'];

      // ========================================
      // LOAD GALLERY ON PAGE LOAD
      // ========================================
      document.addEventListener('DOMContentLoaded', function() {
        console.log('Loading gallery from API...');
        loadGallery();
      });

      // ========================================
      // LOAD GALLERY FROM API
      // ========================================
      async function loadGallery() {
        const loadingState = document.getElementById('loadingState');
        const errorState = document.getElementById('errorState');
        const emptyState = document.getElementById('emptyState');
        const galleryWrapper = document.getElementById('galleryWrapper');
        const galleryRow = document.getElementById('galleryRow');

        try {
          console.log('Fetching from:', API_URL);
          const response = await fetch(API_URL);
          
          if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
          }
          
          const data = await response.json();
          console.log('API Response:', data);

          // Hide loading
          loadingState.style.display = 'none';

          if (!data.success) {
            throw new Error(data.error || 'Failed to load gallery');
          }

          if (data.data.length === 0) {
            // Show empty state
            emptyState.style.display = 'block';
            return;
          }

          // Clear existing content
          galleryRow.innerHTML = '';

          // Render gallery items dengan masonry layout
          data.data.forEach((item, index) => {
            const img = document.createElement('img');
            img.src = item.image;
            img.alt = item.judul;
            
            // Rotate through card classes untuk varied layout
            const cardClass = cardClasses[index % cardClasses.length];
            img.className = `gallery-img ${cardClass}`;
            
            // Error handling untuk gambar
            img.onerror = function() {
              this.src = 'https://via.placeholder.com/400x300?text=Image+Not+Found';
            };
            
            // Click handler untuk modal
            img.onclick = function() {
              openModal(item);
            };
            
            galleryRow.appendChild(img);
          });

          // Show gallery wrapper
          galleryWrapper.style.display = 'block';

          // Store data globally untuk modal
          window.galleryData = data.data;

          console.log('Gallery loaded successfully!', data.data.length, 'items');

        } catch (error) {
          console.error('Error loading gallery:', error);
          loadingState.style.display = 'none';
          errorState.style.display = 'block';
          errorState.innerHTML = `
            <i class="fas fa-exclamation-triangle" style="font-size: 24px; margin-bottom: 10px;"></i>
            <p>Gagal memuat galeri: ${error.message}</p>
            <p style="font-size: 12px; margin-top: 10px;">Pastikan API accessible dan database terkoneksi.</p>
          `;
        }
      }

      // ========================================
      // OPEN MODAL WITH DETAILS
      // ========================================
      function openModal(item) {
        const modal = document.getElementById('galleryModal');
        const modalImage = document.getElementById('modalImage');
        const modalTitle = document.getElementById('modalTitle');
        const modalDescription = document.getElementById('modalDescription');
        const modalAuthor = document.getElementById('modalAuthor');
        const modalAuthorIcon = document.getElementById('modalAuthorIcon');
        const modalDate = document.getElementById('modalDate');

        modalImage.src = item.image;
        modalTitle.textContent = item.judul;
        modalDescription.textContent = item.deskripsi;
        modalAuthor.textContent = item.author;
        modalAuthorIcon.textContent = getInitials(item.author);
        modalDate.textContent = item.tanggal;

        modal.classList.add('show');
        
        // Prevent body scroll when modal open
        document.body.style.overflow = 'hidden';
      }

      // ========================================
      // CLOSE MODAL
      // ========================================
      function closeModal() {
        const modal = document.getElementById('galleryModal');
        modal.classList.remove('show');
        
        // Restore body scroll
        document.body.style.overflow = '';
      }

      // ========================================
      // CLOSE MODAL ON OUTSIDE CLICK
      // ========================================
      window.addEventListener('click', function(e) {
        const modal = document.getElementById('galleryModal');
        if (e.target === modal) {
          closeModal();
        }
      });

      // ========================================
      // CLOSE MODAL ON ESC KEY
      // ========================================
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
          closeModal();
        }
      });

      // ========================================
      // UTILITY FUNCTIONS
      // ========================================
      
      // Get initials from name for avatar
      function getInitials(name) {
        if (!name) return '??';
        return name
          .split(' ')
          .map(word => word[0])
          .join('')
          .toUpperCase()
          .substring(0, 2);
      }

      // Escape HTML to prevent XSS
      function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
      }

      // ========================================
      // AUTO REFRESH (Optional - every 30 seconds)
      // ========================================
      // Uncomment baris di bawah jika ingin auto refresh
      // setInterval(loadGallery, 30000);
    </script>
  </body>
</html>