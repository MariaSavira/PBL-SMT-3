<?php
require 'koneksi.php';

$search = isset($_GET['q']) ? trim($_GET['q']) : "";
$filterKategori = isset($_GET['kategori']) ? (int)$_GET['kategori'] : 0;

$sql = "
    SELECT 
        k.id_karya, 
        k.judul, 
        k.deskripsi, 
        k.link, 
        k.uploaded_at, 
        k.uploaded_by,
        c.nama_kategori
    FROM 
        karya k
    LEFT JOIN 
        kategori_karya c ON k.id_kategori = c.id_kategori
    WHERE 
        1=1
";

// Terapkan filter search
if ($search !== "") {
    $s = pg_escape_string($conn, $search);
    // Perbaikan: Konversi uploaded_by ke teks sebelum ILIKE
    $sql .= " AND (k.judul ILIKE '%$s%' OR k.deskripsi ILIKE '%$s%' OR c.nama_kategori ILIKE '%$s%' OR k.uploaded_by::text ILIKE '%$s%')";
}

// Terapkan filter kategori
if ($filterKategori > 0) {
    $sql .= " AND k.id_kategori = $filterKategori";
}

$sql .= " ORDER BY k.uploaded_at DESC";

$result = pg_query($conn, $sql);

if (!$result) {
    die("Error dalam query: " . pg_last_error($conn));
}

// Set header untuk download Excel (format CSV yang kompatibel dengan Excel)
$filename = "Export_Karya_" . date('Y-m-d_His') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Buka output stream
$output = fopen('php://output', 'w');

// Tulis BOM untuk UTF-8 agar Excel mengenali encoding dengan benar
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// 1. HEADER LEBIH JELAS DAN RINGKAS
fputcsv($output, [
    'ID',
    'Judul Karya',
    'Deskripsi Singkat',
    'Tautan Eksternal',
    'Kategori',
    'ID Pengunggah',
    'Tanggal Waktu Upload'
]);

// Tulis data
while ($row = pg_fetch_assoc($result)) {
    
    // 2. PEMOTONGAN TEKS DESKRIPSI (Untuk kerapian kolom)
    $deskripsi_display = $row['deskripsi'];
    if (strlen($deskripsi_display) > 200) {
        // Potong deskripsi agar tidak terlalu panjang di sel Excel
        $deskripsi_display = substr($deskripsi_display, 0, 200) . '...';
    }
    
    // 3. FORMAT TANGGAL LEBIH RAPI (Menghilangkan zona waktu/milidetik)
    // Asumsi 'uploaded_at' adalah timestamp/datetime
    $tanggal_upload = date('Y-m-d H:i:s', strtotime($row['uploaded_at']));

    fputcsv($output, [
        $row['id_karya'],
        $row['judul'],
        $deskripsi_display, // Menggunakan deskripsi yang sudah dipotong
        $row['link'],
        $row['nama_kategori'] ?? 'Tidak Berkategori',
        $row['uploaded_by'] ?? 'Anonim',
        $tanggal_upload // Menggunakan format tanggal yang lebih bersih
    ]);
}

// Tutup stream
fclose($output);

// Tutup koneksi database
pg_close($conn);
exit;
?>