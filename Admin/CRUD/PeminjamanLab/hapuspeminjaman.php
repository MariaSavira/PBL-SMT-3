<?php
$conn = pg_connect("host=localhost port=5432 dbname=lab_ba user=postgres password=12345");

if (!$conn) {
    echo "Koneksi database gagal!";
    exit;
}

if (!isset($_POST['ids']) || empty($_POST['ids'])) {
    echo "Tidak ada data yang dipilih";
    exit;
}

$ids = $_POST['ids']; 

$placeholders = [];
$params = [];

foreach ($ids as $i => $id) {
    $placeholders[] = '$' . ($i + 1);
    $params[] = $id;
}

$sql = "DELETE FROM peminjaman_lab WHERE id_peminjaman IN (" . implode(",", $placeholders) . ")";

$result = pg_query_params($conn, $sql, $params);

if ($result) {
    echo "Berhasil menghapus " . count($ids) . " data.";
} else {
    echo "Gagal menghapus: " . pg_last_error($conn);
}
