<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require 'koneksi.php';

error_reporting(E_ALL & ~E_DEPRECATED);
ini_set('display_errors', 1);

// ========================
// Helpers: cek table/column exist
// ========================
function table_exists($conn, $table) {
    $sql = "SELECT 1 FROM information_schema.tables WHERE table_schema='public' AND table_name=$1 LIMIT 1";
    $res = pg_query_params($conn, $sql, [$table]);
    return $res && pg_num_rows($res) > 0;
}

function column_exists($conn, $table, $column) {
    $sql = "SELECT 1
            FROM information_schema.columns
            WHERE table_schema='public' AND table_name=$1 AND column_name=$2
            LIMIT 1";
    $res = pg_query_params($conn, $sql, [$table, $column]);
    return $res && pg_num_rows($res) > 0;
}

// ========================
// Input
// ========================
$search   = trim($_GET['search'] ?? '');
$kategori = (int)($_GET['kategori'] ?? 0);
$sort     = $_GET['sort'] ?? 'default';

// filename
$filename = "export_karya_" . date('Ymd_His') . ".csv";

// header download
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'w');
if (!$out) exit('Gagal membuat file export.');

// UTF-8 BOM biar Excel aman
fwrite($out, "\xEF\xBB\xBF");

// delimiter ; biar Excel Indonesia aman
$delimiter = ';';

// header kolom
fputcsv($out, ['ID','Judul','Deskripsi','Kategori','Link','Uploader','Tanggal'], $delimiter);

// ========================
// Tentukan sumber kategori
// ========================
$hasKategoriTable = table_exists($conn, 'kategori_karya');
$hasKategoriCol   = column_exists($conn, 'karya', 'kategori');    // misal kolom text "kategori"
$hasIdKategoriCol = column_exists($conn, 'karya', 'id_kategori'); // misal ada id_kategori

// base FROM + SELECT kategori
if ($hasKategoriTable && $hasIdKategoriCol) {
    $sqlBase = "
        FROM karya k
        LEFT JOIN kategori_karya c ON k.id_kategori = c.id_kategori
    ";
    $kategoriSelect = "COALESCE(c.nama_kategori,'-') AS kategori_label";
    $kategoriSearchExpr = "COALESCE(c.nama_kategori,'')";
} else if ($hasKategoriCol) {
    $sqlBase = "FROM karya k";
    $kategoriSelect = "COALESCE(k.kategori,'-') AS kategori_label";
    $kategoriSearchExpr = "COALESCE(k.kategori,'')";
} else if ($hasIdKategoriCol) {
    $sqlBase = "FROM karya k";
    $kategoriSelect = "COALESCE(k.id_kategori::text,'-') AS kategori_label";
    $kategoriSearchExpr = "COALESCE(k.id_kategori::text,'')";
} else {
    $sqlBase = "FROM karya k";
    $kategoriSelect = "'-'::text AS kategori_label";
    $kategoriSearchExpr = "''";
}

// ========================
// WHERE + params (pg_query_params)
// ========================
$where = [];
$params = [];
$idx = 1;

if ($search !== '') {
    $where[] = "("
        . "k.judul ILIKE $" . $idx
        . " OR k.deskripsi ILIKE $" . ($idx+1)
        . " OR " . $kategoriSearchExpr . " ILIKE $" . ($idx+2)
        . " OR COALESCE(k.uploaded_by::text,'') ILIKE $" . ($idx+3)
        . " OR COALESCE(k.link,'') ILIKE $" . ($idx+4)
        . ")";
    $like = "%{$search}%";
    $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like; $params[] = $like;
    $idx += 5;
}

// filter kategori hanya kalau memang ada kolom id_kategori
if ($kategori > 0 && $hasIdKategoriCol) {
    $where[] = "k.id_kategori = $" . $idx;
    $params[] = $kategori;
    $idx++;
}

$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

// ========================
// SORT (whitelist)
// ========================
switch ($sort) {
    case 'latest': $orderBy = "ORDER BY k.uploaded_at DESC"; break;
    case 'oldest': $orderBy = "ORDER BY k.uploaded_at ASC"; break;
    case 'az':     $orderBy = "ORDER BY LOWER(k.judul) ASC"; break;
    case 'za':     $orderBy = "ORDER BY LOWER(k.judul) DESC"; break;
    default:       $orderBy = "ORDER BY k.id_karya DESC"; break;
}

// ========================
// Query
// ========================
$sql = "
    SELECT
        k.id_karya,
        k.judul,
        k.deskripsi,
        {$kategoriSelect},
        COALESCE(k.link,'') AS link,
        COALESCE(k.uploaded_by::text,'') AS uploaded_by,
        k.uploaded_at
    {$sqlBase}
    {$whereSql}
    {$orderBy}
";

$res = pg_query_params($conn, $sql, $params);
if (!$res) {
    fclose($out);
    exit("Gagal export: " . pg_last_error($conn));
}

// ========================
// Output rows
// ========================
while ($r = pg_fetch_assoc($res)) {
    $judul = (string)($r['judul'] ?? '');
    $desc  = (string)($r['deskripsi'] ?? '');
    $desc  = preg_replace("/\r\n|\r|\n/", " ", $desc);

    // FIX tanggal supaya Excel gak jadi ######
    $tanggal = '';
    if (!empty($r['uploaded_at'])) $tanggal = date('Y-m-d', strtotime($r['uploaded_at']));

    fputcsv($out, [
        $r['id_karya'] ?? '',
        $judul,
        $desc,
        $r['kategori_label'] ?? '-',
        $r['link'] ?? '',
        $r['uploaded_by'] ?? '',
        $tanggal
    ], $delimiter);
}

fclose($out);
exit;
?>
