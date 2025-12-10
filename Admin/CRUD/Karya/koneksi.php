<?php
$host = "localhost";
$port = "5432";
$dbname = "lab_ba";     
$user = "postgres";     
$pass = "12345";        

$conn = pg_connect("host=$host port=$port dbname=$dbname user=$user password=$pass");

if (!$conn) {
    die("Koneksi ke PostgreSQL gagal.");
}
?>
