<?php
// config.php
$host = "localhost";
$port = "5432";
$dbname = "lab_ba";
$user = "postgres";   // sesuaikan bila username postgresql lain
$pass = "12345";

    $conn = pg_connect($connStr);

    if (!$conn) {
        throw new RuntimeException("Koneksi PostgreSQL gagal: " . pg_last_error());
    }

    return $conn;
}

function qparams(string $sql, array $params) {
    $conn = get_pg_connection();
    $res  = pg_query_params($conn, $sql, $params);
    if ($res === false) {
        throw new RuntimeException("Query gagal: " . pg_last_error($conn));
    }
    return $res;
}

function q(string $sql) {
    $conn = get_pg_connection();
    $res  = pg_query($conn, $sql);
    if ($res === false) {
        throw new RuntimeException("Query gagal: " . pg_last_error($conn));
    }
    return $res;
}
