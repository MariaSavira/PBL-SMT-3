<?php
$conn = pg_connect("host=localhost dbname=lab_ba user=postgres password=29082006");
if (!$conn) {
    die("Koneksi gagal: " . pg_last_error());
}

$id = $_POST['id'] ?? '';
$status = $_POST['status'] ?? '';

if ($id && $status) {
    
    $query = "UPDATE peminjaman_lab 
              SET status = $1 
              WHERE id_peminjaman = $1";

    $result = pg_query_params($conn, $query, [$status, $id]);

    if ($result) {
        echo "OK";
    } else {
        echo "ERROR: " . pg_last_error($conn);
    }
}
?>
