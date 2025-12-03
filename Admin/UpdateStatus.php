<?php

require __DIR__ . '/CRUD/koneksi.php';

// Jika koneksi.php belum membuat $conn:
$conn = pg_connect("host=localhost port=5432 dbname=lab_ba user=postgres password=29082006");

// Ambil data dari fetch
$id     = $_POST['id'] ?? '';
$status = $_POST['status'] ?? '';

if ($id === '' || $status === '') {
    echo "DATA_KURANG";
    exit;
}

$query = "UPDATE peminjaman_lab SET status = $1 WHERE id_peminjaman = $2";
$result = pg_query_params($conn, $query, [$status, $id]);

if ($result) {
    echo "OK";
} else {
    echo "ERROR: " . pg_last_error($conn);
}
