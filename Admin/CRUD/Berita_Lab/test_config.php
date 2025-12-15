<?php
// admin/test_config.php
// Test koneksi database
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require_once 'config.php';

echo "<!DOCTYPE html>
<html lang='id'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Test Koneksi Database</title>
    <link href='https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 500px;
            width: 100%;
        }
        h2 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .success {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #28a745;
            margin-bottom: 20px;
        }
        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #dc3545;
            margin-bottom: 20px;
        }
        .info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-top: 20px;
        }
        .info p {
            margin: 10px 0;
            color: #666;
        }
        .info strong {
            color: #333;
        }
    </style>
</head>
<body>
    <div class='container'>";

try {
    // Test query
    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetchColumn();
    
    echo "<h2>✅ Test Koneksi Database</h2>";
    echo "<div class='success'>
            <strong>Koneksi Berhasil!</strong><br>
            Database terhubung dengan baik.
          </div>";
    
    echo "<div class='info'>
            <p><strong>Database:</strong> $dbname</p>
            <p><strong>Host:</strong> $host</p>
            <p><strong>Port:</strong> $port</p>
            <p><strong>PostgreSQL Version:</strong> $version</p>
          </div>";
    
    // Check tabel berita
    $check = $pdo->query("SELECT EXISTS (
        SELECT FROM information_schema.tables 
        WHERE table_name = 'berita'
    )");
    
    if ($check->fetchColumn()) {
        $count = $pdo->query("SELECT COUNT(*) FROM berita")->fetchColumn();
        echo "<div class='info' style='margin-top: 20px;'>
                <p><strong>Tabel 'berita':</strong> ✓ Ditemukan</p>
                <p><strong>Jumlah data:</strong> $count baris</p>
              </div>";
    } else {
        echo "<div class='error' style='margin-top: 20px;'>
                <strong>Perhatian!</strong><br>
                Tabel 'berita' belum dibuat. Silakan buat tabel terlebih dahulu.
              </div>";
    }
    
} catch(PDOException $e) {
    echo "<h2>❌ Test Koneksi Database</h2>";
    echo "<div class='error'>
            <strong>Koneksi Gagal!</strong><br>
            " . $e->getMessage() . "
          </div>";
}

echo "</div>
</body>
</html>";
?>