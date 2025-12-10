<?php
require 'koneksi.php';

// Ambil parameter filter dari URL (sama seperti di index.php)
$search = isset($_GET['q']) ? trim($_GET['q']) : "";
$filterJenis = isset($_GET['jenis']) ? $_GET['jenis'] : "";

// Ambil daftar jenis untuk validasi
$jenis_options = [];
$res_jenis = pg_query($conn, "SELECT DISTINCT jenis FROM publikasi ORDER BY jenis ASC");

if ($res_jenis) {
    while ($row = pg_fetch_assoc($res_jenis)) {
        $jenis_options[] = $row['jenis'];
    }
}

// Query untuk mengambil data publikasi dengan filter yang sama
$sql = "SELECT * FROM publikasi WHERE 1=1";

// Terapkan filter search
if ($search !== "") {
    $s = pg_escape_string($conn, $search);
    $sql .= " AND (judul ILIKE '%$s%' OR author::text ILIKE '%$s%')";
}

// Terapkan filter jenis
if ($filterJenis !== "") {
    if (in_array($filterJenis, $jenis_options)) {
        $fj = pg_escape_string($conn, $filterJenis);
        $sql .= " AND jenis = '$fj'";
    }
}

$sql .= " ORDER BY id_publikasi DESC";

$result = pg_query($conn, $sql);

if (!$result) {
    die("Error dalam query: " . pg_last_error($conn));
}

// Set header untuk download Excel (format CSV yang kompatibel dengan Excel)
$filename = "Publikasi_" . date('Y-m-d_His') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

// Buka output stream
$output = fopen('php://output', 'w');

// Tulis BOM untuk UTF-8 agar Excel mengenali encoding dengan benar
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Tulis header kolom
fputcsv($output, [
    'ID Publikasi',
    'Judul',
    'Jenis',
    'Author',
    'Riset',
    'Tanggal Terbit',
    'Status'
]);

// Tulis data
while ($row = pg_fetch_assoc($result)) {
    // Proses author (dari JSON array ke string)
    $authors = json_decode($row['author'], true);
    $authorText = is_array($authors) ? implode(", ", $authors) : $row['author'];
    
    // Proses riset (dari JSON array ke string)
    $risetText = '';
    if (!empty($row['riset'])) {
        $risetDecoded = json_decode($row['riset'], true);
        if (json_last_error() === JSON_ERROR_NONE) {
            $risetText = is_array($risetDecoded) ? implode(", ", $risetDecoded) : $risetDecoded;
        } else {
            $risetText = $row['riset'];
        }
    }
    
    // Proses status
    $statusRaw = $row['status'];
    if (
        $statusRaw === 't' || $statusRaw === 'true' ||
        $statusRaw === '1' || $statusRaw === 1 ||
        $statusRaw === true  || $statusRaw === 'Aktif'
    ) {
        $statusLabel = 'Aktif';
    } else {
        $statusLabel = 'Draft';
    }
    
    fputcsv($output, [
        $row['id_publikasi'],
        $row['judul'],
        $row['jenis'],
        $authorText,
        $risetText,
        $row['tanggal_terbit'],
        $statusLabel
    ]);
}

// Tutup stream
fclose($output);

// Tutup koneksi database
pg_close($conn);
exit;
?>