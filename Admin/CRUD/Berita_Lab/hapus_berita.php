<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require_once 'config.php';

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
                
                $stmt = $pdo->prepare("SELECT gambar FROM berita WHERE id_berita = ?");
                $stmt->execute([$id]);
                $berita = $stmt->fetch();
                
                if ($berita) {
                    $stmt = $pdo->prepare("DELETE FROM berita WHERE id_berita = ?");
                    $stmt->execute([$id]);
                    
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

    $id = $_GET['id'] ?? 0;

    if ($id > 0) {
        try {

            $stmt = $pdo->prepare("SELECT gambar FROM berita WHERE id_berita = ?");
            $stmt->execute([$id]);
            $berita = $stmt->fetch();
            
            if ($berita) {

                $stmt = $pdo->prepare("DELETE FROM berita WHERE id_berita = ?");
                $stmt->execute([$id]);
                
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