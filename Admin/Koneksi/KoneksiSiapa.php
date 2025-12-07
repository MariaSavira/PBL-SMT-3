<?php
// config.php
$host = "localhost";
$port = "5432";
$dbname = "lab_ba";
$user = "postgres";   // sesuaikan bila username postgresql lain
$pass = "12345";

try {
    // buat PDO Postgres
    $db = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // hentikan dengan pesan jelas untuk debug (jangan tampilkan di production)
    die("Koneksi database gagal: " . $e->getMessage());
}