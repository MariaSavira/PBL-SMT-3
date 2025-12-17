<?php
require_once __DIR__ . '../../../Cek_Autentikasi.php';
require __DIR__ . '../../../Koneksi/KoneksiSasa.php';

$result = q("
    SELECT 
        id_peminjaman, 
        nama_peminjam, 
        email, 
        instansi, 
        tanggal_pengajuan, 
        tanggal_pakai, 
        keperluan
    FROM peminjaman_lab
");
if (!$result) {
    die("Query error.");
}

$filename = "data_peminjaman_lab_" . date('Ymd_His') . ".csv";

header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="'. $filename .'";');

echo "\xEF\xBB\xBF";

$out = fopen('php://output', 'w');

fputcsv($out, ['ID Peminjaman','Nama Peminjam','Email','Instansi','Tanggal Pengajuan','Tanggal Pakai','Keperluan']);

while ($row = pg_fetch_assoc($result)) {
    fputcsv($out, [
        $row['id_peminjaman'],
        $row['nama_peminjam'],
        $row['email'],
        $row['instansi'],
        $row['tanggal_pengajuan'],
        $row['tanggal_pakai'],
        $row['keperluan']
    ]);
}

fclose($out);
pg_free_result($result);
exit;
