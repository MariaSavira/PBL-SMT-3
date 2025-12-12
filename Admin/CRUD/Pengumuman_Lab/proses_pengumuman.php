<?php
require_once 'config.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    $conn = getDBConnection();
    
    switch ($action) {
        case 'tambah':
            // Validasi input
            if (empty($_POST['isi'])) {
                throw new Exception('Isi pengumuman harus diisi');
            }
            if (empty($_POST['tanggal_terbit'])) {
                throw new Exception('Tanggal terbit harus diisi');
            }
            
            // Get logged in user
            $uploader = getLoggedInUser();
            
            // Insert data
            $sql = "INSERT INTO pengumuman (isi, tanggal_terbit, uploader, status) 
                    VALUES (:isi, :tanggal_terbit, :uploader, :status)";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':isi' => $_POST['isi'],
                ':tanggal_terbit' => $_POST['tanggal_terbit'],
                ':uploader' => $uploader,
                ':status' => $_POST['status'] ?? 'Aktif'
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Pengumuman berhasil ditambahkan'
            ]);
            break;
            
        case 'edit':
            // Validasi input
            if (empty($_POST['id_pengumuman'])) {
                throw new Exception('ID pengumuman tidak ditemukan');
            }
            if (empty($_POST['isi'])) {
                throw new Exception('Isi pengumuman harus diisi');
            }
            
            // Update data
            $sql = "UPDATE pengumuman SET 
                    isi = :isi, 
                    tanggal_terbit = :tanggal_terbit, 
                    status = :status
                    WHERE id_pengumuman = :id";
            
            $stmt = $conn->prepare($sql);
            $stmt->execute([
                ':isi' => $_POST['isi'],
                ':tanggal_terbit' => $_POST['tanggal_terbit'],
                ':status' => $_POST['status'] ?? 'Aktif',
                ':id' => $_POST['id_pengumuman']
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Pengumuman berhasil diupdate'
            ]);
            break;
            
        case 'hapus':
            if (empty($_POST['id'])) {
                throw new Exception('ID pengumuman tidak ditemukan');
            }
            
            // Hapus data
            $stmt = $conn->prepare("DELETE FROM pengumuman WHERE id_pengumuman = :id");
            $stmt->execute([':id' => $_POST['id']]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Pengumuman berhasil dihapus'
            ]);
            break;
            
        case 'get_data':
            $id = $_GET['id'] ?? null;
            if ($id) {
                // Get single data
                $stmt = $conn->prepare("SELECT * FROM pengumuman WHERE id_pengumuman = :id");
                $stmt->execute([':id' => $id]);
                $data = $stmt->fetch();
                
                if ($data) {
                    echo json_encode([
                        'success' => true,
                        'data' => $data
                    ]);
                } else {
                    throw new Exception('Data tidak ditemukan');
                }
            } else {
                // Get all data
                $stmt = $conn->query("SELECT * FROM pengumuman ORDER BY tanggal_terbit DESC");
                $data = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'data' => $data
                ]);
            }
            break;
            
        case 'hapus_multiple':
            if (empty($_POST['ids']) || !is_array($_POST['ids'])) {
                throw new Exception('Pilih data yang akan dihapus');
            }
            
            $ids = array_map('intval', $_POST['ids']);
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            
            // Hapus data
            $stmt = $conn->prepare("DELETE FROM pengumuman WHERE id_pengumuman IN ($placeholders)");
            $stmt->execute($ids);
            
            echo json_encode([
                'success' => true,
                'message' => count($ids) . ' pengumuman berhasil dihapus'
            ]);
            break;
            
        default:
            throw new Exception('Aksi tidak valid');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
?>