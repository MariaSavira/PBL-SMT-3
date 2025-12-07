<?php
session_start();
require __DIR__ . '/CRUD/koneksi.php';

$conn = pg_connect("host=localhost port=5432 dbname=lab_ba user=postgres password=29082006");

$id      = $_POST['id'] ?? '';
$status  = $_POST['status'] ?? '';
$catatan = $_POST['catatan'] ?? '';

if ($id === '' || $status === '') {
    echo "DATA_KURANG";
    exit;
}

// Nama admin dari session (buat approved_by)
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Jika pending → approved_by kosong
$approved_by_value = ($status === 'pending') ? null : $admin_name;

$query = "
    UPDATE peminjaman_lab 
    SET status = $1,
        approved_by = $2,
        catatan_admin = $3
    WHERE id_peminjaman = $4
";

$result = pg_query_params($conn, $query, [$status, $approved_by_value, $catatan, $id]);

echo $result ? "OK" : "ERROR: " . pg_last_error($conn);
