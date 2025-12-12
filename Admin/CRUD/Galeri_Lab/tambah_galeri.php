<?php
// tambah_galeri.php - Add Gallery Form (FIXED VERSION)
session_start();
require_once 'config.php';

$username = isset($_SESSION['username']) ? $_SESSION['username'] : 'Maria Savira';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Galeri - Admin Dashboard</title>
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

        #sidebar {
            width: 80px;
            transition: width 0.3s ease;
        }

        #sidebar.active {
            width: 250px;
        }

        .main-content {
            flex: 1;
            padding: 30px;
            margin-left: 0;
            transition: margin-left 0.3s ease;
            max-width: 1200px;
        }

        .main-content.shifted {
            margin-left: 170px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 600;
            color: #1a1a1a;
            text-align: center;
            margin-bottom: 30px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            margin-bottom: 20px;
            transition: all 0.3s ease;
        }

        .back-button:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .form-container {
            background: white;
            border-radius: 12px;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            max-width: 800px;
            margin: 0 auto;
        }

        .form-grid {
            display: grid;
            gap: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .form-group label {
            font-size: 14px;
            font-weight: 600;
            color: #1a1a1a;
        }

        .form-group label .required {
            color: #ef4444;
            margin-left: 4px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            padding: 12px 16px;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #333;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: #94a3b8;
        }

        .form-group textarea {
            min-height: 120px;
            resize: vertical;
        }

        .upload-area {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            min-height: 280px;
        }

        .upload-area:hover {
            border-color: #2563eb;
            background: #eff6ff;
        }

        .upload-area.dragover {
            border-color: #2563eb;
            background: #dbeafe;
        }

        .upload-area.has-image {
            padding: 0;
            border: 1px solid #e2e8f0;
            background: #000;
        }

        .upload-area.has-image:hover {
            border-color: #2563eb;
        }

        .upload-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            transition: opacity 0.3s ease;
        }

        .upload-area.has-image .upload-placeholder {
            display: none;
        }

        .upload-icon {
            width: 64px;
            height: 64px;
            background: #2563eb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 16px;
        }

        .upload-icon svg {
            width: 32px;
            height: 32px;
            fill: white;
        }

        .upload-text {
            font-size: 14px;
            color: #1a1a1a;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .upload-hint {
            font-size: 12px;
            color: #64748b;
        }

        .upload-area input[type="file"] {
            display: none;
        }

        .preview-container {
            display: none;
            width: 100%;
            height: 280px;
            position: relative;
            border-radius: 12px;
            overflow: hidden;
        }

        .upload-area.has-image .preview-container {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .preview-image {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 12px;
        }

        .change-image-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
            border-radius: 12px;
        }

        .upload-area.has-image:hover .change-image-overlay {
            opacity: 1;
        }

        .change-image-text {
            color: white;
            font-size: 14px;
            font-weight: 500;
            padding: 10px 20px;
            background: rgba(37, 99, 235, 0.9);
            border-radius: 6px;
        }

        .form-buttons {
            display: flex;
            gap: 12px;
            justify-content: flex-end;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid #e2e8f0;
        }

        .btn-submit {
            padding: 12px 32px;
            background: #2563eb;
            color: white;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: #1d4ed8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .btn-submit:disabled {
            background: #cbd5e1;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .form-group.error input,
        .form-group.error textarea,
        .form-group.error select {
            border-color: #ef4444;
        }

        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
            border: 1px solid #fca5a5;
        }

        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }

        .loading.show {
            display: block;
        }

        .spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid #2563eb;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }

            .form-container {
                padding: 24px;
            }

            .form-buttons {
                flex-direction: column;
            }

            .btn-submit {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div id="sidebar"></div>

    <div class="main-content">
        <a href="galeri.php" class="back-button">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
            Kembali
        </a>

        <h1 class="page-title">Tambah Galeri</h1>

        <div class="form-container">
            <form id="tambahGaleriForm" method="POST" action="proses_galeri.php" enctype="multipart/form-data">
                <input type="hidden" name="action" value="tambah">
                
                <div class="form-grid">
                    <!-- Judul -->
                    <div class="form-group">
                        <label for="judul">Judul<span class="required">*</span></label>
                        <input type="text" id="judul" name="judul" placeholder="Masukkan judul foto" required>
                        <span class="error-message" id="error-judul">Judul harus diisi</span>
                    </div>

                    <!-- Deskripsi -->
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi<span class="required">*</span></label>
                        <textarea id="deskripsi" name="deskripsi" placeholder="Masukkan deskripsi" required></textarea>
                        <span class="error-message" id="error-deskripsi">Deskripsi harus diisi</span>
                    </div>

                    <!-- Upload Foto -->
                    <div class="form-group">
                        <label>Tambahkan Foto<span class="required">*</span></label>
                        <div class="upload-area" id="uploadArea">
                            <!-- Placeholder elements -->
                            <div class="upload-placeholder">
                                <div class="upload-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                        <path d="M21 19V5c0-1.1-.9-2-2-2H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2zM8.5 13.5l2.5 3.01L14.5 12l4.5 6H5l3.5-4.5z"/>
                                    </svg>
                                </div>
                                <div class="upload-text">Tambahkan Foto</div>
                                <div class="upload-hint">Format: JPG, PNG, GIF (Max 5MB)</div>
                            </div>
                            
                            <!-- Preview container -->
                            <div class="preview-container">
                                <img id="previewImage" class="preview-image" alt="Preview">
                                <div class="change-image-overlay">
                                    <span class="change-image-text">Klik untuk ganti foto</span>
                                </div>
                            </div>
                            
                            <input type="file" id="fileInput" name="file" accept="image/*" required>
                        </div>
                        <span class="error-message" id="error-file">Gambar harus diupload</span>
                    </div>

                    <!-- Tipe Media -->
                    <div class="form-group">
                        <label for="tipe_media">Tipe Media<span class="required">*</span></label>
                        <select id="tipe_media" name="tipe_media" required>
                            <option value="foto">Foto</option>
                            <option value="video">Video</option>
                        </select>
                    </div>

                    <!-- Tanggal Terbit -->
                    <div class="form-group">
                        <label for="tanggal_upload">Tanggal Terbit<span class="required">*</span></label>
                        <input type="date" id="tanggal_upload" name="tanggal_upload" required>
                        <span class="error-message" id="error-tanggal">Tanggal harus diisi</span>
                    </div>

                    <!-- Author -->
                    <div class="form-group">
                        <label for="uploaded_by">Author<span class="required">*</span></label>
                        <input type="text" id="uploaded_by" name="uploaded_by" placeholder="Masukkan nama author" required>
                        <span class="error-message" id="error-author">Author harus diisi</span>
                    </div>
                </div>

                <div class="loading" id="loadingIndicator">
                    <div class="spinner"></div>
                    <p style="margin-top: 12px; color: #64748b;">Sedang mengunggah...</p>
                </div>

                <div class="form-buttons">
                    <button type="submit" class="btn-submit" id="submitBtn">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script>
        // File upload preview - FIXED VERSION
        const fileInput = document.getElementById('fileInput');
        const uploadArea = document.getElementById('uploadArea');
        const previewImage = document.getElementById('previewImage');

        // Handle file input change
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                handleFileUpload(file);
            }
        });

        // Handle file upload
        function handleFileUpload(file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                showError('error-file', 'File harus berupa gambar');
                return;
            }

            // Validate file size (5MB)
            if (file.size > 5 * 1024 * 1024) {
                showError('error-file', 'Ukuran file maksimal 5MB');
                return;
            }

            // Read and display image
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                uploadArea.classList.add('has-image');
                hideError('error-file');
            };
            reader.readAsDataURL(file);
        }

        // Click to upload or change image
        uploadArea.addEventListener('click', function(e) {
            // Prevent triggering file input if clicking on the file input itself
            if (e.target === fileInput) return;
            
            // If image is already uploaded, show confirmation
            if (uploadArea.classList.contains('has-image')) {
                if (confirm('Ganti gambar?')) {
                    fileInput.click();
                }
            } else {
                fileInput.click();
            }
        });

        // Drag and drop functionality
        uploadArea.addEventListener('dragover', function(e) {
            e.preventDefault();
            if (!uploadArea.classList.contains('has-image')) {
                uploadArea.classList.add('dragover');
            }
        });

        uploadArea.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
        });

        uploadArea.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleFileUpload(files[0]);
            }
        });

        // Form validation
        const form = document.getElementById('tambahGaleriForm');
        const submitBtn = document.getElementById('submitBtn');
        const loadingIndicator = document.getElementById('loadingIndicator');

        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Clear previous errors
            document.querySelectorAll('.form-group').forEach(group => {
                group.classList.remove('error');
            });
            document.querySelectorAll('.error-message').forEach(msg => {
                msg.classList.remove('show');
            });

            let isValid = true;

            // Validate judul
            const judul = document.getElementById('judul').value.trim();
            if (!judul) {
                showError('error-judul', 'Judul harus diisi');
                isValid = false;
            }

            // Validate deskripsi
            const deskripsi = document.getElementById('deskripsi').value.trim();
            if (!deskripsi) {
                showError('error-deskripsi', 'Deskripsi harus diisi');
                isValid = false;
            }

            // Validate file (WAJIB untuk tambah)
            if (!fileInput.files || fileInput.files.length === 0) {
                showError('error-file', 'Gambar harus diupload');
                isValid = false;
            }

            // Validate tanggal
            const tanggal = document.getElementById('tanggal_upload').value;
            if (!tanggal) {
                showError('error-tanggal', 'Tanggal harus diisi');
                isValid = false;
            }

            // Validate author
            const author = document.getElementById('uploaded_by').value.trim();
            if (!author) {
                showError('error-author', 'Author harus diisi');
                isValid = false;
            }

            if (isValid) {
                // Show loading
                submitBtn.disabled = true;
                loadingIndicator.classList.add('show');
                
                // Submit form
                form.submit();
            }
        });

        function showError(errorId, message) {
            const errorElement = document.getElementById(errorId);
            const formGroup = errorElement.closest('.form-group');
            
            errorElement.textContent = message;
            errorElement.classList.add('show');
            formGroup.classList.add('error');
        }

        function hideError(errorId) {
            const errorElement = document.getElementById(errorId);
            const formGroup = errorElement.closest('.form-group');
            
            errorElement.classList.remove('show');
            formGroup.classList.remove('error');
        }

        // Set default date to today
        document.getElementById('tanggal_upload').valueAsDate = new Date();
    </script>
</body>
</html>