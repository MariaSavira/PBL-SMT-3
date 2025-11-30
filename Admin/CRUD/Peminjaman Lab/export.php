<?php
// Koneksi PostgreSQL (sesuaikan)
$conn = pg_connect("host=localhost dbname=lab_ba user=postgres password=29082006");
if (!$conn) {
    die("Koneksi gagal: " . pg_last_error());
}

// Query (sama seperti yang kamu punya)
$result = pg_query($conn, "
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
    die("Query error: " . pg_last_error());
}

// Nama file
$filename = "data_peminjaman_lab_" . date('Ymd_His') . ".csv";

// Header supaya browser mendownload file
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename="'. $filename .'";');

// Tambahkan BOM UTF-8 supaya Excel Windows dapat menampilkan karakter UTF-8 dengan benar
echo "\xEF\xBB\xBF";

// Buka output stream
$out = fopen('php://output', 'w');

// Tulis header kolom CSV
fputcsv($out, ['ID Peminjaman','Nama Peminjam','Email','Instansi','Tanggal Pengajuan','Tanggal Pakai','Keperluan']);

// Tulis tiap baris dari DB
while ($row = pg_fetch_assoc($result)) {
    // Jika perlu ubah format tanggal, misal: YYYY-MM-DD -> dd/mm/YYYY
    // $row['tanggal_pengajuan'] = date('d/m/Y', strtotime($row['tanggal_pengajuan']));
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
pg_close($conn);
exit;
