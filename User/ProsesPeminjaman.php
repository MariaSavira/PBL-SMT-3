<?php
require '../admin/CRUD/koneksi.php';

header('Content-Type: application/json');

try {
    $nama_peminjam     = $_POST['nama_peminjam'];
    $email             = $_POST['email'];
    $instansi          = $_POST['instansi'];
    $tanggal_pengajuan = date('Y-m-d');
    $tanggal_pakai     = $_POST['tanggal_pakai'];
    $keperluan         = $_POST['keperluan'];

    $sql = "INSERT INTO peminjaman_lab 
            (nama_peminjam, email, instansi, tanggal_pengajuan, tanggal_pakai, keperluan)
            VALUES
            (:nama_peminjam, :email, :instansi, :tanggal_pengajuan, :tanggal_pakai, :keperluan)";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':nama_peminjam' => $nama_peminjam,
        ':email'         => $email,
        ':instansi'      => $instansi,
        ':tanggal_pengajuan' => $tanggal_pengajuan,
        ':tanggal_pakai' => $tanggal_pakai,
        ':keperluan'     => $keperluan
    ]);

    echo json_encode(['status' => 'success']);
} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
