<?php
session_start();
require_once __DIR__ . '/../Admin/Koneksi/KoneksiSasa.php';

$base_url = dirname($_SERVER['PHP_SELF']);

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

try {
    $conn = get_pg_connection();
} catch (Throwable $e) {
    error_log('DB connection error in authenticate.php: ' . $e->getMessage());
    header('Location: login.php?error=' . urlencode('Gagal koneksi ke database.'));
    exit;
}

if ($username === '' || $password === '') {
    header('Location: login.php?error=' . urlencode('Username dan password harus diisi.'));
    exit;
}


$sql = 'SELECT id_anggota, username, password_hash, nama, foto, jabatan FROM anggotalab WHERE username = $1 LIMIT 1';
$result = pg_query_params($conn, $sql, array($username));

if (!$result) {

    header('Location: login.php?error=' . urlencode('Terjadi kesalahan pada server.'));
    exit;
}

if (pg_num_rows($result) === 0) {
    header('Location: login.php?error=' . urlencode('Username atau password salah.'));
    exit;
}

$user = pg_fetch_assoc($result);
$hash = $user['password_hash'];

if (password_verify($password, $hash)) {

    session_regenerate_id(true);
    $_SESSION['id_anggota'] = $user['id_anggota'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['nama'] = $user['nama'];
    $_SESSION['foto'] = $user['foto'];
    $_SESSION['jabatan'] = $user['jabatan'];

    header("Location: " . $base_url . "/../Admin/Dashboard.php?status=success&msg=" . urlencode("Login berhasil!"));
    exit;

} else {
    header('Location: Login.php?error=' . urlencode('Username atau password salah.'));
    exit;
}
