<?php
// admin/hapus_berita.php
session_start();
require_once 'config.php';

// Handle bulk delete (multiple IDs via POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ids'])) {
    $ids = $_POST['ids'] ?? [];
    
    if (empty($ids)) {
        $_SESSION['error'] = 'Tidak ada data yang dipilih!';
        header('Location: berita.php');
        exit;
    }
    
    try {
        $deleted = 0;
        
        foreach ($ids as $id) {
            $id = (int)$id;
            
            // Get gambar filename first
            $stmt = $pdo->prepare("SELECT gambar FROM berita WHERE id_berita = ?");
            $stmt->execute([$id]);
            $berita = $stmt->fetch();
            
            if ($berita) {
                // Delete from database
                $stmt = $pdo->prepare("DELETE FROM berita WHERE id_berita = ?");
                $stmt->execute([$id]);
                
                // Delete image file
                if ($berita['gambar']) {
                    hapusGambar($berita['gambar']);
                }
                
                $deleted++;
            }
        }
        
        $_SESSION['success'] = "$deleted berita berhasil dihapus!";
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus berita: ' . $e->getMessage();
    }
    
    header('Location: berita.php');
    exit;
}

// Handle single delete (single ID via GET)
$id = $_GET['id'] ?? 0;

if ($id > 0) {
    try {
        // Get gambar filename first
        $stmt = $pdo->prepare("SELECT gambar FROM berita WHERE id_berita = ?");
        $stmt->execute([$id]);
        $berita = $stmt->fetch();
        
        if ($berita) {
            // Delete from database
            $stmt = $pdo->prepare("DELETE FROM berita WHERE id_berita = ?");
            $stmt->execute([$id]);
            
            // Delete image file
            if ($berita['gambar']) {
                hapusGambar($berita['gambar']);
            }
            
            $_SESSION['success'] = 'Berita berhasil dihapus!';
        } else {
            $_SESSION['error'] = 'Berita tidak ditemukan!';
        }
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus berita: ' . $e->getMessage();
    }
}

header('Location: berita.php');
exit;
?>