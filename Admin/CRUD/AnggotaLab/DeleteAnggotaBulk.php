<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require_once __DIR__ . '../../../Koneksi/KoneksiSasa.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Method Not Allowed";
    exit;
}

$ids = $_POST['ids'] ?? [];

if (!is_array($ids) || count($ids) === 0) {
    http_response_code(400);
    echo "Tidak ada ID yang dikirim.";
    exit;
}

$conn = get_pg_connection();

$ids = array_map('intval', $ids);

$placeholder = [];
foreach ($ids as $i => $id) {
    $placeholder[] = '$' . ($i + 1);
}

$sql = "DELETE FROM anggotalab WHERE id_anggota IN (" . implode(',', $placeholder) . ")";

$res = pg_query_params($conn, $sql, $ids);

if (!$res) {
    http_response_code(500);
    echo "Gagal menghapus: " . pg_last_error($conn);
    exit;
}

pg_query($conn, "REFRESH MATERIALIZED VIEW mv_anggota_keahlian");

echo "Berhasil menghapus " . count($ids) . " data.";
