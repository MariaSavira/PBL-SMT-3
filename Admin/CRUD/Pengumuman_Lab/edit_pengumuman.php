<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Berita</title>
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
        }

        .main-container {
            margin-left: 80px;
            padding: 20px;
            transition: margin-left 0.3s ease;
            max-width: 1200px;
        }

        .main-container.sidebar-open {
            margin-left: 280px;
        }

        .header-back {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-bottom: 25px;
        }

        .btn-back {
            width: 45px;
            height: 45px;
            background: white;
            border: none;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .btn-back:hover {
            background: #f1f5f9;
        }

        .header-back h1 {
            font-size: 28px;
            color: #1e293b;
            font-weight: 600;
        }

        .form-container {
            background: white;
            padding: 35px;
            border-radius: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            font-weight: 500;
            color: #334155;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 14px 18px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s;
            font-family: 'Poppins', sans-serif;
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #3b82f6;
            background: #f8fafc;
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

        .upload-area {
            border: 2px dashed #cbd5e1;
            border-radius: 10px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background: #f8fafc;
        }

        .upload-area:hover {
            border-color: #3b82f6;
            background: #eff6ff;
        }

        .upload-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 15px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: #94a3b8;
        }

        .upload-text h3 {
            color: #1e293b;
            font-size: 16px;
            margin-bottom: 8px;
        }

        .upload-text p {
            color: #94a3b8;
            font-size: 13px;
        }

        .file-input {
            display: none;
        }

        .preview-container {
            margin-top: 20px;
            display: none;
        }

        .preview-image {
            width: 100%;
            max-height: 400px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
        }

        .btn-remove-image {
            width: 100%;
            padding: 10px;
            background: #fee2e2;
            color: #dc2626;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-remove-image:hover {
            background: #fecaca;
        }

        .form-actions {
            display: flex;
            gap: 15px;
            justify-content: flex-end;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 2px solid #f1f5f9;
        }

        .btn {
            padding: 14px 35px;
            border: none;
            border-radius: 10px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .btn-cancel {
            background: #f1f5f9;
            color: #475569;
        }

        .btn-cancel:hover {
            background: #e2e8f0;
        }

        .btn-submit {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
        }

        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
        }

        .btn-submit:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }

        .error-message {
            color: #dc2626;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .error-message.show {
            display: block;
        }

        .input-error {
            border-color: #dc2626 !important;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        .loading-overlay.active {
            display: flex;
        }

        .loading-content {
            background: white;
            padding: 30px 50px;
            border-radius: 15px;
            text-align: center;
        }

        .spinner {
            border: 4px solid #f3f4f6;
            border-top: 4px solid #3b82f6;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        @media (max-width: 768px) {
            .main-container {
                margin-left: 0;
                padding: 15px;
            }

            .form-row {
                grid-template-columns: 1fr;
            }

            .form-actions {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <div id="sidebar"></div>

    <div class="main-container" id="mainContainer">
        <div class="header-back">
            <button class="btn-back" onclick="window.location.href='pengumuman.php'">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h1>Edit Pengumuman</h1>
        </div>

        <form id="pengumumanForm" class="form-container" enctype="multipart/form-data">
            <input type="hidden" id="id_pengumuman" name="id_pengumuman">
            
            <div class="form-group">
                <label for="tanggal_terbit">Tanggal Terbit <span style="color: #dc2626;">*</span></label>
                <input type="date" id="tanggal_terbit" name="tanggal_terbit" required>
                <span class="error-message" id="tanggalError">Tanggal terbit harus diisi</span>
            </div>

            <div class="form-group">
                <label for="isi">Isi Pengumuman <span style="color: #dc2626;">*</span></label>
                <textarea id="isi" name="isi" placeholder="Masukkan Isi Pengumuman" required></textarea>
                <span class="error-message" id="isiError">Isi pengumuman harus diisi</span>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="Aktif">Aktif</option>
                    <option value="Nonaktif">Nonaktif</option>
                </select>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-cancel" onclick="window.location.href='pengumuman.php'">
                    <i class="fas fa-times"></i> Batal
                </button>
                <button type="submit" class="btn btn-submit" id="submitBtn">
                    <i class="fas fa-save"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-content">
            <div class="spinner"></div>
            <p>Memuat data...</p>
        </div>
    </div>

    <script src="../../../Assets/Javascript/Admin/Sidebar.js"></script>
    <script>
        // Get ID from URL
        const urlParams = new URLSearchParams(window.location.search);
        const pengumumanId = urlParams.get('id');

        if (!pengumumanId) {
            alert('ID pengumuman tidak ditemukan');
            window.location.href = 'pengumuman.php';
        }

        // Load data
        async function loadData() {
            const loadingOverlay = document.getElementById('loadingOverlay');
            loadingOverlay.classList.add('active');
            
            try {
                const response = await fetch(`proses_pengumuman.php?action=get_data&id=${pengumumanId}`);
                const result = await response.json();
                
                if (result.success && result.data) {
                    const data = result.data;
                    
                    document.getElementById('id_pengumuman').value = data.id_pengumuman;
                    document.getElementById('tanggal_terbit').value = data.tanggal_terbit;
                    document.getElementById('isi').value = data.isi;
                    document.getElementById('status').value = data.status;
                } else {
                    alert('Data tidak ditemukan');
                    window.location.href = 'pengumuman.php';
                }
            } catch (error) {
                alert('Error: ' + error.message);
                window.location.href = 'pengumuman.php';
            } finally {
                loadingOverlay.classList.remove('active');
            }
        }

        // Load on page ready
        loadData();

        function validateForm() {
            let isValid = true;
            
            document.querySelectorAll('.error-message').forEach(el => el.classList.remove('show'));
            document.querySelectorAll('input, textarea').forEach(el => el.classList.remove('input-error'));
            
            const tanggal = document.getElementById('tanggal_terbit');
            if (!tanggal.value) {
                document.getElementById('tanggalError').classList.add('show');
                tanggal.classList.add('input-error');
                isValid = false;
            }
            
            const isi = document.getElementById('isi');
            if (!isi.value.trim()) {
                document.getElementById('isiError').classList.add('show');
                isi.classList.add('input-error');
                isValid = false;
            }
            
            return isValid;
        }

        document.getElementById('pengumumanForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!validateForm()) {
                return;
            }
            
            const formData = new FormData(this);
            formData.append('action', 'edit');
            
            const loadingOverlay = document.getElementById('loadingOverlay');
            const submitBtn = document.getElementById('submitBtn');
            
            loadingOverlay.classList.add('active');
            loadingOverlay.querySelector('p').textContent = 'Menyimpan perubahan...';
            submitBtn.disabled = true;
            
            try {
                const response = await fetch('proses_pengumuman.php', {
                    method: 'POST',
                    body: formData
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert('Pengumuman berhasil diupdate!');
                    window.location.href = 'pengumuman.php';
                } else {
                    alert('Error: ' + result.message);
                }
            } catch (error) {
                alert('Terjadi kesalahan: ' + error.message);
            } finally {
                loadingOverlay.classList.remove('active');
                submitBtn.disabled = false;
            }
        });

        // Load data on page load
        loadData();
    </script>
</body>
</html>