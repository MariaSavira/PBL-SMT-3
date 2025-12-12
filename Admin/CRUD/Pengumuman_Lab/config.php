<?php
// Config Database PostgreSQL
define('DB_HOST', 'localhost');
define('DB_PORT', '5432');
define('DB_NAME', 'lab_ba');
define('DB_USER', 'postgres');
define('DB_PASS', '12345');

// Koneksi Database
function getDBConnection() {
    try {
        $dsn = "pgsql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME;
        $conn = new PDO($dsn, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $conn;
    } catch(PDOException $e) {
        die("Koneksi gagal: " . $e->getMessage());
    }
}

// Fungsi untuk format tanggal Indonesia
function formatTanggalIndo($tanggal) {
    $bulan = array(
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    );
    
    $split = explode('-', $tanggal);
    return $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}

// Fungsi untuk sanitasi input
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Fungsi untuk get logged in user
function getLoggedInUser() {
    if (isset($_SESSION['username'])) {
        return $_SESSION['username'];
    } elseif (isset($_SESSION['nama'])) {
        return $_SESSION['nama'];
    } elseif (isset($_SESSION['user_id'])) {
        return 'User ID: ' . $_SESSION['user_id'];
    }
    return 'Admin';
}

// Session start
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>