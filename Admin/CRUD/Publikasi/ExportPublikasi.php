<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require __DIR__ . '../../../Koneksi/KoneksiSasa.php';

$search     = trim($_GET['q'] ?? '');
$filterJenis = trim($_GET['jenis'] ?? '');

$conditions = [];
$params     = [];
$i = 1;

if ($filterJenis !== '') {
    $conditions[] = "p.jenis = $" . $i;
    $params[] = $filterJenis;
    $i++;
}

if ($search !== '') {
    $conditions[] = "(p.judul ILIKE $" . $i . " OR p.author::text ILIKE $" . $i . ")";
    $params[] = '%' . $search . '%';
    $i++;
}

$whereSql = $conditions ? ("WHERE " . implode(" AND ", $conditions)) : "";

$sql = "
    SELECT
        p.id_publikasi,
        p.judul,
        p.jenis,
        p.author,
        br.nama_bidang_riset AS riset_nama,
        p.tanggal_terbit,
        p.status
    FROM publikasi p
    LEFT JOIN bidangriset br
        ON br.id_riset = p.id_riset
    $whereSql
    ORDER BY p.id_publikasi DESC
";

$result = qparams($sql, $params);
if (!$result) {
    http_response_code(500);
    exit("Error query export.");
}

$filename = "Publikasi_" . date('Y-m-d_His') . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');

echo "\xEF\xBB\xBF";

$output = fopen('php://output', 'w');

fputcsv($output, [
    'ID Publikasi',
    'Judul',
    'Jenis',
    'Author',
    'Riset',
    'Tanggal Terbit',
    'Status'
]);

while ($row = pg_fetch_assoc($result)) {

    $authors = json_decode($row['author'] ?? '[]', true);
    $authorText = is_array($authors) ? implode(", ", $authors) : ($row['author'] ?? '');

    $risetText = $row['riset_nama'] ?? '-';

    $statusRaw = $row['status'] ?? '';
    $statusLabel = ($statusRaw === 't' || $statusRaw === true || $statusRaw === 'true' || $statusRaw === '1' || $statusRaw === 1)
        ? 'Aktif'
        : 'Draft';

    fputcsv($output, [
        $row['id_publikasi'],
        $row['judul'],
        $row['jenis'],
        $authorText,
        $risetText,
        $row['tanggal_terbit'],
        $statusLabel
    ]);
}

fclose($output);
pg_free_result($result);
exit;
