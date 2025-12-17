<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require_once __DIR__ . '/config.php';
$conn = getDBConnection();

$search = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$sort   = $_GET['sort'] ?? 'default';

$where = [];
$params = [];

if ($status !== '') {
    $where[] = "status = :status";
    $params[':status'] = $status;
}

if ($search !== '') {
    $where[] = "(isi ILIKE :q OR uploader ILIKE :q)";
    $params[':q'] = '%' . $search . '%';
}

$whereSql = $where ? ("WHERE " . implode(" AND ", $where)) : "";

switch ($sort) {
    case 'latest': $orderBy = "ORDER BY tanggal_terbit DESC, id_pengumuman DESC"; break;
    case 'oldest': $orderBy = "ORDER BY tanggal_terbit ASC, id_pengumuman ASC"; break;
    default:       $orderBy = "ORDER BY id_pengumuman DESC"; break;
}

$sql = "
    SELECT id_pengumuman, isi, tanggal_terbit, uploader, status
    FROM pengumuman
    {$whereSql}
    {$orderBy}
";
$stmt = $conn->prepare($sql);
$stmt->execute($params);

$filename = "data_pengumuman_" . date('Ymd_His') . ".csv";
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="'.$filename.'";');
echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');
fputcsv($out, ['ID', 'ISI', 'TANGGAL_TERBIT', 'UPLOADER', 'STATUS']);

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    fputcsv($out, [
        $row['id_pengumuman'],
        $row['isi'],
        $row['tanggal_terbit'],
        $row['uploader'],
        $row['status'],
    ]);
}
fclose($out);
exit;
