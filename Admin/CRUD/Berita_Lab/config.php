<?php
    $host = 'localhost';
    $dbname = 'lab_ba';
    $username = 'postgres';
    $password = '12345';
    $port = '5432';

    try {
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        die("Koneksi database gagal: " . $e->getMessage());
    }

    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    function uploadGambar($file, $folder ='../../../Assets/Image/Galeri-Berita/') {
        if (!file_exists($folder)) {
            mkdir($folder, 0777, true);
        }
        
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $file['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($ext, $allowed)) {
            return ['success' => false, 'message' => 'Format file tidak diizinkan'];
        }
        
        if ($file['size'] > 5000000) { 
            return ['success' => false, 'message' => 'Ukuran file terlalu besar (max 5MB)'];
        }
        
        $check = getimagesize($file['tmp_name']);
        if ($check === false) {
            return ['success' => false, 'message' => 'File bukan gambar yang valid'];
        }
        
        $newname = time() . '_' . uniqid() . '.' . $ext;
        $destination = $folder . $newname;
        
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => true, 'filename' => $newname, 'path' => $destination];
        }
        
        return ['success' => false, 'message' => 'Gagal upload file'];
    }

    function hapusGambar($filename, $folder = '../../../Assets/Image/Galeri-Berita/') {
        $filepath = $folder . $filename;
        if (file_exists($filepath)) {
            unlink($filepath);
        }
    }
?>