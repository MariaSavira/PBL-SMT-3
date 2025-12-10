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
        // Ambil satu berita by ID dengan JOIN ke anggota_lab
        $stmt = $pdo->prepare("
            SELECT b.*, a.nama as nama_author 
            FROM berita b
            LEFT JOIN anggotalab a ON b.uploaded_by = a.id_anggota
            WHERE b.id_berita = ? AND b.status = 'publish'
        ");
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
        // Ambil semua berita yang dipublish dengan JOIN ke anggota_lab
        $query = "
            SELECT b.*, a.nama as nama_author 
            FROM berita b
            LEFT JOIN anggotalab a ON b.uploaded_by = a.id_anggota
            WHERE b.status = 'publish' 
            ORDER BY b.tanggal DESC
        ";
        
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