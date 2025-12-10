<?php
// api_galeri.php - API untuk mengambil data galeri
// Letakkan file ini di: Admin/CRUD/Galeri_Lab/api_galeri.php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Allow dari folder User

require_once 'config.php';

try {
    // Ambil semua data galeri
    $stmt = $pdo->query("
        SELECT 
            id_galeri,
            judul,
            deskripsi,
            file_path,
            tipe_media,
            tanggal_upload,
            uploaded_by
        FROM galeri 
        ORDER BY tanggal_upload DESC
    ");
    
    $galeri_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format data untuk frontend
    $result = [];
    foreach ($galeri_items as $item) {
        $result[] = [
            'id' => $item['id_galeri'],
            'judul' => $item['judul'],
            'deskripsi' => $item['deskripsi'],
            // IMPORTANT: Sesuaikan path image dengan struktur folder Anda
            'image' => '../Assets/Image/Galeri-Berita/' . $item['file_path'],
            'tipe' => $item['tipe_media'],
            'tanggal' => date('d M Y', strtotime($item['tanggal_upload'])),
            'author' => $item['uploaded_by']
        ];
    }
    
    echo json_encode([
        'success' => true,
        'data' => $result,
        'total' => count($result)
    ]);
    
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error: ' . $e->getMessage()
    ]);
}