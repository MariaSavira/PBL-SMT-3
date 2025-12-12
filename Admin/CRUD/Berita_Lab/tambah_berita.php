<?php
// admin/tambah_berita.php
session_start();
require_once 'config.php';

$user_name = $_SESSION['user_name'] ?? '1';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Berita</title>
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

        .back-button {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #3b82f6;
            color: white;
            padding: 10px 20px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: 500;
            margin-bottom: 20px;
            transition: all 0.3s;
        }

        .back-button:hover {
            background: #2563eb;
            transform: translateX(-5px);
        }

        .form-container {
            max-width: 900px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: #2c3e50;
            font-size: 32px;
            margin-bottom: 30px;
        }

        .form-card {
            background: white;
            border-radius: 16px;
            padding: 40px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            color: #2c3e50;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group textarea {
            width: 100%;
            padding: 14px 18px;
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            font-size: 15px;
            color: #2c3e50;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-group textarea {
            min-height: 200px;
            resize: vertical;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .date-input-wrapper {
            position: relative;
        }

        .date-icon {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #3b82f6;
            pointer-events: none;
        }

        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 60px 20px;
            text-align: center;
            background: #f8fafc;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
        }

        .upload-area:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .upload-area.has-image {
            padding: 0;
            border: none;
        }

        .upload-icon {
            width: 80px;
            height: 80px;
            background: #3b82f6;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
        }

        .upload-area p {
            color: #64748b;
            font-size: 15px;
            margin-top: 10px;
        }

        .upload-area input[type="file"] {
            display: none;
        }

        .image-preview {
            width: 100%;
            max-height: 400px;
            border-radius: 12px;
            object-fit: cover;
            display: none;
        }

        .image-preview.show {
            display: block;
        }

        .change-image-btn {
            position: absolute;
            bottom: 20px;
            right: 20px;
            background: #3b82f6;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
            transition: all 0.3s;
            width: 60px;
            height: 60px;
            display: none;
            align-items: center;
            justify-content: center;
        }

        .change-image-btn.show {
            display: flex;
        }

        .change-image-btn:hover {
            transform: scale(1.1);
        }

        .submit-btn {
            background: #3b82f6;
            color: white;
            border: none;
            padding: 14px 40px;
            border-radius: 10px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            width: 100%;
            margin-top: 30px;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        }

        .submit-btn:disabled {
            background: #94a3b8;
            cursor: not-allowed;
            transform: none;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            body.sidebar-expanded .main-content {
                margin-left: 0;
            }

            .form-card {
                padding: 25px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }
        }


        .form-group select {
    width: 100%;
    padding: 14px 18px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-size: 15px;
    color: #2c3e50;
    background: white;
    cursor: pointer;
    transition: all 0.3s;
    font-family: 'Poppins', sans-serif;
}

.form-group select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.form-group select option {
    padding: 10px;
} 

.alert {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    animation: slideDown 0.3s ease;
    font-weight: 500;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border-left: 4px solid #ef4444;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border-left: 4px solid #10b981;
}

.alert svg {
    flex-shrink: 0;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes slideUp {
    to {
        opacity: 0;
        transform: translateY(-20px);
    }
} 
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div id="sidebar"></div>
    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    
    <div class="main-content" id="mainContent">
        <a href="berita.php" class="back-button">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M19 12H5M12 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>

        <div class="form-container">
            <h1>Tambah Berita</h1>
<?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10"/>
                <path d="M15 9l-6 6M9 9l6 6"/>
            </svg>
            <span><?= htmlspecialchars($_SESSION['error']) ?></span>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M20 6L9 17l-5-5"/>
            </svg>
            <span><?= htmlspecialchars($_SESSION['success']) ?></span>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <form action="proses_berita.php" method="POST" enctype="multipart/form-data" id="formBerita">
                <input type="hidden" name="action" value="tambah">
                <input type="hidden" name="uploaded_by" value="<?= htmlspecialchars($user_name) ?>">

                <div class="form-card">
                                    <div class="form-group">
                    <label for="judul">Judul</label>
                    <input type="text" id="judul" name="judul" placeholder="Masukkan judul" 
                        value="<?= htmlspecialchars($_SESSION['form_data']['judul'] ?? '') ?>" required>
                </div>

                <div class="form-group">
                    <label for="tanggal">Tanggal Terbit</label>
                    <div class="date-input-wrapper">
                        <input type="date" id="tanggal" name="tanggal" 
                            value="<?= $_SESSION['form_data']['tanggal'] ?? date('Y-m-d') ?>" required>
                        <div class="date-icon">
                            <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <rect x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                                <path d="M16 2v4M8 2v4M3 10h18"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="author">Author</label>
                    <input type="text" id="author" name="author" placeholder="Masukkan Author" 
                        value="<?= htmlspecialchars($_SESSION['form_data']['author'] ?? $user_name) ?>">
                </div>

                <div class="form-group">
                    <label for="status">Status Publikasi</label>
                    <select id="status" name="status" required>
                        <option value="publish" <?= (($_SESSION['form_data']['status'] ?? 'publish') == 'publish') ? 'selected' : '' ?>>Publish (Aktif)</option>
                        <option value="draft" <?= (($_SESSION['form_data']['status'] ?? 'publish') == 'draft') ? 'selected' : '' ?>>Draft (Belum Dipublikasi)</option>
                    </select>
                </div>
                <?php 
                // Clear form data after display
                unset($_SESSION['form_data']); 
                ?>
                    <div class="form-group">
                        <label for="gambar">
                            Gambar 
                            <span style="color: #ef4444; font-weight: 600;">*</span>
                            <span style="color: #64748b; font-size: 13px; font-weight: 400;">(Wajib diisi)</span>
                        </label>
                        <div class="upload-area" id="uploadArea" onclick="document.getElementById('gambar').click()">
                            <div class="upload-content">
                                <div class="upload-icon">
                                    <svg width="40" height="40" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                        <circle cx="12" cy="13" r="4"/>
                                    </svg>
                                </div>
                                <p><strong>Tambahkan Foto</strong></p>
                                <p style="font-size: 13px; color: #94a3b8;">Format: JPG, PNG, GIF (Max 5MB)</p>
                            </div>
                            <img id="imagePreview" class="image-preview" alt="Preview">
                            <button type="button" class="change-image-btn" id="changeImageBtn" onclick="document.getElementById('gambar').click(); event.stopPropagation();">
                                <svg width="24" height="24" fill="none" stroke="white" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                    <circle cx="12" cy="13" r="4"/>
                                </svg>
                            </button>
                        </div>
                        <input type="file" id="gambar" name="gambar" accept="image/*" onchange="previewImage(this)" required>
                    </div>
                    <div class="form-group">
                        <label for="isi">Isi Berita</label>
                        <textarea id="isi" name="isi" placeholder="Masukkan Isi Berita" required><?= htmlspecialchars($_SESSION['form_data']['isi'] ?? '') ?></textarea>
                    </div>

                    <?php 
                    // Clear form data after display
                    unset($_SESSION['form_data']); 
                    ?>
                    <button type="submit" class="submit-btn" id="submitBtn">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../../Assets/Javascript/Admin/berita.js"></script>
    <script>
        // Set today's date as default
        document.getElementById('tanggal').valueAsDate = new Date();

        // Image preview
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            const uploadArea = document.getElementById('uploadArea');
            const changeBtn = document.getElementById('changeImageBtn');
            const uploadContent = uploadArea.querySelector('.upload-content');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.add('show');
                    uploadArea.classList.add('has-image');
                    changeBtn.classList.add('show');
                    if (uploadContent) {
                        uploadContent.style.display = 'none';
                    }
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        // Form validation
        document.getElementById('formBerita').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.disabled = true;
            submitBtn.textContent = 'Menyimpan...';
        });

      // Sidebar toggle observer
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

// Character counter untuk textarea
document.addEventListener('DOMContentLoaded', function() {
    const isiTextarea = document.getElementById('isi');
    if (isiTextarea) {
        isiTextarea.addEventListener('input', function() {
            const maxLength = 10000;
            const currentLength = this.value.length;
            
            // Buat counter jika belum ada
            let counter = document.getElementById('isiCounter');
            if (!counter) {
                counter = document.createElement('div');
                counter.id = 'isiCounter';
                counter.style.cssText = 'text-align: right; color: #64748b; font-size: 13px; margin-top: 5px;';
                this.parentElement.appendChild(counter);
            }
            
            counter.textContent = `${currentLength.toLocaleString()} / ${maxLength.toLocaleString()} karakter`;
        });
    }
});
    </script>

    <script>
// Auto hide alert after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.animation = 'slideUp 1s ease';
            setTimeout(() => alert.remove(), 1000);
        }, 5000);
    });
});
</script>

</body>
</html>