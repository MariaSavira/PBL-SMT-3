<?php
require_once '/config.php';

echo "<h2>Testing Database Connection</h2>";

try {
    $conn = getDBConnection();
    echo "<p style='color: green;'>✓ Koneksi database berhasil!</p>";
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM pengumuman");
    $result = $stmt->fetch();
    
    echo "<p>Total pengumuman: " . $result['total'] . "</p>";
    
    $stmt = $conn->query("SELECT * FROM pengumuman LIMIT 5");
    $pengumuman = $stmt->fetchAll();
    
    echo "<h3>Data Pengumuman:</h3>";
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Isi</th><th>Uploader</th><th>Status</th><th>Tanggal</th></tr>";
    
    foreach ($pengumuman as $row) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['id_pengumuman']) . "</td>";
        echo "<td>" . htmlspecialchars(substr($row['isi'], 0, 50)) . "...</td>";
        echo "<td>" . htmlspecialchars($row['uploader']) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>" . htmlspecialchars($row['tanggal_terbit']) . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
} catch(PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
}
?>