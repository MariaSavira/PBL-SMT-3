<?php
    require_once __DIR__ . '../../../Cek_Autentikasi.php';
    require_once 'config.php';

    echo "<!DOCTYPE html>";
    echo "<html lang='id'>";
    echo "<head>";
    echo "<meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
    echo "<title>Test Koneksi Database</title>";
    echo "<link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap' rel='stylesheet'>";
    echo "<style>body { font-family: 'Poppins', sans-serif; padding: 20px; }</style>";
    echo "</head>";
    echo "<body>";

    echo "<h2>Test Koneksi Database PostgreSQL</h2>";

    try {
        $stmt = $pdo->query("SELECT current_database(), current_user, version()");
        $result = $stmt->fetch();
        
        echo "<p style='color: green;'><strong>✓ Koneksi Berhasil!</strong></p>";
        echo "<ul>";
        echo "<li><strong>Database:</strong> " . $result['current_database'] . "</li>";
        echo "<li><strong>User:</strong> " . $result['current_user'] . "</li>";
        echo "<li><strong>Version:</strong> " . $result['version'] . "</li>";
        echo "</ul>";
        
        // Cek tabel galeri
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM galeri");
        $count = $stmt->fetch();
        echo "<p><strong>Jumlah data di tabel galeri:</strong> " . $count['count'] . "</p>";
        
    } catch(PDOException $e) {
        echo "<p style='color: red;'><strong>✗ Koneksi Gagal!</strong></p>";
        echo "<p>Error: " . $e->getMessage() . "</p>";
    }

    echo "</body>";
    echo "</html>";
?>