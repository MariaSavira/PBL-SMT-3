<?php
$host = "localhost";
$port = "5432";
$dbname = "lab_ba";
$user = "postgres";
$pass = "12345";

try {
    $db = new PDO("pgsql:host=$host;port=$port;dbname=$dbname", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {

    die("Koneksi database gagal: " . $e->getMessage());
}