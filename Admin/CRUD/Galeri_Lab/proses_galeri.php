<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require_once 'config.php';

    // Set upload directory
    $upload_dir = '../../../Assets/Image/Galeri-Berita/';

    // Create directory if not exists
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Function to handle file upload
    function uploadFile($file, $upload_dir) {
        $errors = [];
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $max_size = 5 * 1024 * 1024; // 5MB

        // Check if file is uploaded
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Error upload file. Kode error: " . $file['error'];
            return ['success' => false, 'errors' => $errors];
        }

        // Validate file type
        $file_type = mime_content_type($file['tmp_name']);
        if (!in_array($file_type, $allowed_types)) {
            $errors[] = "Tipe file tidak diizinkan. Hanya JPG, PNG, dan GIF yang diperbolehkan.";
            return ['success' => false, 'errors' => $errors];
        }

        // Validate file size
        if ($file['size'] > $max_size) {
            $errors[] = "Ukuran file terlalu besar. Maksimal 5MB.";
            return ['success' => false, 'errors' => $errors];
        }

        // Generate unique filename
        $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $new_filename = uniqid() . '_' . time() . '.' . $file_extension;
        $target_path = $upload_dir . $new_filename;

        // Upload file
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            return ['success' => true, 'filename' => $new_filename];
        } else {
            $errors[] = "Gagal mengupload file.";
            return ['success' => false, 'errors' => $errors];
        }
    }

    // Function to delete file
    function deleteFile($filename, $upload_dir) {
        $file_path = $upload_dir . $filename;
        if (file_exists($file_path)) {
            return unlink($file_path);
        }
        return true;
    }

    // Handle different actions
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    try {
        switch ($action) {
            case 'tambah':
                // Validate required fields
                if (empty($_POST['judul']) || empty($_POST['deskripsi']) || 
                    empty($_POST['tanggal_upload']) || empty($_POST['uploaded_by'])) {
                    throw new Exception("Semua field wajib diisi!");
                }

                // Validate file upload
                if (!isset($_FILES['file']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
                    throw new Exception("File gambar wajib diupload!");
                }

                // Upload file
                $upload_result = uploadFile($_FILES['file'], $upload_dir);
                
                if (!$upload_result['success']) {
                    throw new Exception(implode(", ", $upload_result['errors']));
                }

                // Insert to database
                $stmt = $pdo->prepare("
                    INSERT INTO galeri (judul, deskripsi, file_path, tipe_media, tanggal_upload, uploaded_by, id_berita) 
                    VALUES (?, ?, ?, ?, ?, ?, NULL)
                ");
                
                $stmt->execute([
                    clean_input($_POST['judul']),
                    clean_input($_POST['deskripsi']),
                    $upload_result['filename'],
                    clean_input($_POST['tipe_media']),
                    $_POST['tanggal_upload'],
                    clean_input($_POST['uploaded_by'])
                ]);

                $_SESSION['success_message'] = "Galeri berhasil ditambahkan!";
                header("Location: galeri.php");
                exit;

            case 'edit':
                // Validate required fields
                if (empty($_POST['id_galeri']) || empty($_POST['judul']) || 
                    empty($_POST['deskripsi']) || empty($_POST['tanggal_upload']) || 
                    empty($_POST['uploaded_by'])) {
                    throw new Exception("Semua field wajib diisi!");
                }

                $id_galeri = intval($_POST['id_galeri']);
                $old_file_path = $_POST['old_file_path'];
                $new_filename = $old_file_path; // Keep old filename by default

                // Check if new file is uploaded
                if (isset($_FILES['file']) && $_FILES['file']['error'] !== UPLOAD_ERR_NO_FILE) {
                    // Upload new file
                    $upload_result = uploadFile($_FILES['file'], $upload_dir);
                    
                    if (!$upload_result['success']) {
                        throw new Exception(implode(", ", $upload_result['errors']));
                    }

                    $new_filename = $upload_result['filename'];

                    // Delete old file if upload successful
                    deleteFile($old_file_path, $upload_dir);
                }

                // Update database
                $stmt = $pdo->prepare("
                    UPDATE galeri 
                    SET judul = ?, deskripsi = ?, file_path = ?, tipe_media = ?, 
                        tanggal_upload = ?, uploaded_by = ?
                    WHERE id_galeri = ?
                ");
                
                $stmt->execute([
                    clean_input($_POST['judul']),
                    clean_input($_POST['deskripsi']),
                    $new_filename,
                    clean_input($_POST['tipe_media']),
                    $_POST['tanggal_upload'],
                    clean_input($_POST['uploaded_by']),
                    $id_galeri
                ]);

                $_SESSION['success_message'] = "Galeri berhasil diupdate!";
                header("Location: galeri.php");
                exit;

            case 'delete':
                // Get IDs from POST
                $ids = isset($_POST['ids']) ? json_decode($_POST['ids'], true) : [];
                
                if (empty($ids)) {
                    throw new Exception("Tidak ada data yang dipilih untuk dihapus!");
                }

                $deleted_count = 0;
                
                foreach ($ids as $id) {
                    $id = intval($id);
                    
                    // Get file path before delete
                    $stmt = $pdo->prepare("SELECT file_path FROM galeri WHERE id_galeri = ?");
                    $stmt->execute([$id]);
                    $result = $stmt->fetch();
                    
                    if ($result) {
                        // Delete from database
                        $stmt = $pdo->prepare("DELETE FROM galeri WHERE id_galeri = ?");
                        $stmt->execute([$id]);
                        
                        // Delete file
                        deleteFile($result['file_path'], $upload_dir);
                        
                        $deleted_count++;
                    }
                }

                $_SESSION['success_message'] = "$deleted_count galeri berhasil dihapus!";
                
                // Return JSON response for AJAX
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    echo json_encode(['success' => true, 'message' => "$deleted_count galeri berhasil dihapus!"]);
                    exit;
                }
                
                header("Location: galeri.php");
                exit;

            default:
                throw new Exception("Aksi tidak valid!");
        }

    } catch (Exception $e) {
        $_SESSION['error_message'] = $e->getMessage();
        
        // Return JSON response for AJAX
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && 
            strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            http_response_code(400);
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
        
        // Redirect back based on action
        if ($action === 'tambah') {
            header("Location: tambah_galeri.php");
        } elseif ($action === 'edit' && isset($_POST['id_galeri'])) {
            header("Location: edit_galeri.php?id=" . $_POST['id_galeri']);
        } else {
            header("Location: galeri.php");
        }
        exit;
    }
?>