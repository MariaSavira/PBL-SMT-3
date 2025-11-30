<?php
// submit_peminjaman.php

// Koneksi DB
$connStr = "host=localhost port=5432 dbname=lab_ba user=postgres password=29082006 options='--client_encoding=UTF8'";
$conn = pg_connect($connStr);

if (!$conn) {
    die("Koneksi gagal: " . pg_last_error());
}

// Ambil dan validasi input (sederhana)
$nama = isset($_POST['nama_peminjam']) ? trim($_POST['nama_peminjam']) : '';
$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$instansi = isset($_POST['instansi']) ? trim($_POST['instansi']) : '';
$tanggal_pakai = isset($_POST['tanggal_pakai']) ? trim($_POST['tanggal_pakai']) : '';
$keperluan = isset($_POST['keperluan']) ? trim($_POST['keperluan']) : '';

// Validasi dasar
$errors = [];
if ($nama === '') $errors[] = "Nama wajib diisi.";
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email tidak valid.";
if ($instansi === '') $errors[] = "Instansi wajib diisi.";
if ($tanggal_pakai === '') $errors[] = "Tanggal digunakan wajib diisi.";

if (!empty($errors)) {
    // Simple error output. Kamu bisa redirect kembali dengan pesan.
    foreach ($errors as $err) {
        echo "<p>$err</p>";
    }
    exit;
}

// Default values untuk kolom tambahan
$status = 'pending';
$approved_by = null;
$catatan_admin = null;

// Gunakan parameterized query untuk menghindari SQL injection
$sql = "INSERT INTO peminjaman_lab
    (nama_peminjam, email, instansi, tanggal_pengajuan, tanggal_pakai, keperluan, status, approved_by, catatan_admin)
    VALUES ($1, $2, $3, NOW(), $4, $5, $6, $7, $8)";

$params = [
    $nama,
    $email,
    $instansi,
    $tanggal_pakai,
    $keperluan,
    $status,
    $approved_by,
    $catatan_admin
];

$result = pg_query_params($conn, $sql, $params);

if ($result) {
    // Redirect kembali ke form dengan query string status => kamu bisa tangani tampil notifikasi di client
    header("Location: FormPeminjamanLab.html?status=success");
    exit;
} else {
    echo "Gagal menyimpan data: " . pg_last_error($conn);
}

pg_close($conn);
?>
