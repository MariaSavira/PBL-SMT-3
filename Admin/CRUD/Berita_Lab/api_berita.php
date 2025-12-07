<?php
// admin/api_berita.php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    // Ambil parameter
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;
    
    if ($id > 0) {
        // Ambil satu berita by ID
        $stmt = $pdo->prepare("SELECT * FROM berita WHERE id_berita = ? AND status = 'publish'");
        $stmt->execute([$id]);
        $berita = $stmt->fetch();
        
        if ($berita) {
            // Format tanggal
            $berita['tanggal_formatted'] = date('d F Y, H:i', strtotime($berita['tanggal']));
            echo json_encode([
                'success' => true,
                'data' => $berita
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Berita tidak ditemukan'
            ]);
        }
    } else {
        // Ambil semua berita yang dipublish
        $query = "SELECT * FROM berita WHERE status = 'publish' ORDER BY tanggal DESC";
        
        if ($limit > 0) {
            $query .= " LIMIT " . $limit;
        }
        
        $stmt = $pdo->query($query);
        $berita_list = $stmt->fetchAll();
        
        // Format tanggal untuk setiap berita
        foreach ($berita_list as &$berita) {
            $berita['tanggal_formatted'] = date('d F Y, H:i', strtotime($berita['tanggal']));
        }
        
        echo json_encode([
            'success' => true,
            'data' => $berita_list,
            'total' => count($berita_list)
        ]);
    }
    
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Database error: ' . $e->getMessage()
    ]);
}
?>