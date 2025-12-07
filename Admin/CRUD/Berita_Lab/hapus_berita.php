<?php
// admin/hapus_berita.php
session_start();
require_once 'config.php';

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