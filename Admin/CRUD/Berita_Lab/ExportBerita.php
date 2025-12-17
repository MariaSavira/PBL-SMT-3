<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require_once 'config.php'; 

$filename = "export_berita_" . date('Ymd_His') . ".csv";

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="' . $filename . '"');
header('Pragma: no-cache');
header('Expires: 0');

$out = fopen('php://output', 'w');
if (!$out) {
    exit('Gagal membuat file export.');
}

fwrite($out, "\xEF\xBB\xBF");

$delimiter = ';';

fputcsv($out, [
    'ID',
    'Judul',
    'Isi',
    'Gambar',
    'Tanggal',
    'Uploader ID',
    'Nama Uploader',
    'Status'
], $delimiter);

try {
    $sql = "
        SELECT
            b.id_berita,
            b.judul,
            b.isi,
            b.gambar,
            b.tanggal,
            b.uploaded_by,
            COALESCE(a.nama, 'Unknown') AS nama_uploader,
            b.status
        FROM berita b
        LEFT JOIN anggotalab a ON b.uploaded_by = a.id_anggota
        ORDER BY b.id_berita DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($rows as $r) {
        $isi = (string)($r['isi'] ?? '');
        $isi = preg_replace("/\r\n|\r|\n/", " ", $isi);

        $tanggal = '';
        if (!empty($r['tanggal'])) {
            $tanggal = date('Y-m-d', strtotime($r['tanggal']));
        }

        fputcsv($out, [
            $r['id_berita'] ?? '',
            $r['judul'] ?? '',
            $isi,
            $r['gambar'] ?? '',
            $tanggal,
            $r['uploaded_by'] ?? '',
            $r['nama_uploader'] ?? 'Unknown',
            $r['status'] ?? ''
        ], $delimiter);
    }

    fclose($out);
    exit;
} catch (Throwable $e) {

    fclose($out);
    http_response_code(500);
    echo "Gagal export: " . $e->getMessage();
    exit;
}
?>