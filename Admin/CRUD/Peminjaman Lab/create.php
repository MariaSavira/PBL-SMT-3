<?php
// Koneksi PostgreSQL
error_reporting(E_ALL);
ini_set('display_errors', 1);
$connStr = "host=localhost port=5432 dbname=lab_ba user=postgres password=29082006 options='--client_encoding=UTF8'";
$conn = pg_connect($connStr);

if (!$conn) {
    die("Koneksi gagal");
}

// Ambil data POST
$nama = $_POST['nama_peminjam'];
$email = $_POST['email'];
$instansi = $_POST['instansi'];
$tanggal_pakai = $_POST['tanggal_pakai'];
$keperluan = $_POST['keperluan'];

// Query insert
$query = "INSERT INTO peminjaman_lab (nama_peminjam, email, instansi, tanggal_pakai, keperluan, status) 
          VALUES ('$nama', '$email', '$instansi', '$tanggal_pakai', '$keperluan', 'Menunggu')";

$result = pg_query($conn, $query);

if ($result) {
    echo "success";
} else {
    echo "failed";
}

pg_close($conn);
?>
