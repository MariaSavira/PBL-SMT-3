<?php
session_start();

require __DIR__ . '../Koneksi/KoneksiValia.php';

require __DIR__ . '../../PHPMailer/src/PHPMailer.php';
require __DIR__ . '../../PHPMailer/src/SMTP.php';
require __DIR__ . '../../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$id      = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$status  = strtolower(trim($_POST['status'] ?? ''));
$catatan = trim($_POST['catatan'] ?? '');

if ($id <= 0 || $status === '') {
    echo "ERROR:DATA_KURANG";
    exit;
}

$allowedStatus = ['pending', 'disetujui', 'ditolak'];
if (!in_array($status, $allowedStatus, true)) {
    echo "ERROR:STATUS_TIDAK_VALID";
    exit;
}

$admin_id = isset($_SESSION['id_anggota']) ? (int)$_SESSION['id_anggota'] : 0;

if ($admin_id <= 0) {
    echo "ERROR:SESSION_HILANG";
    exit;
}

$approved_by_value = ($status === 'pending') ? null : $admin_id;

try {
    $update = $db->prepare("
        UPDATE peminjaman_lab
        SET
            status        = :status,
            approved_by   = :approved_by,
            catatan_admin = :catatan
        WHERE id_peminjaman = :id
    ");

    $update->execute([
        ':status'      => $status,
        ':approved_by' => $approved_by_value,
        ':catatan'     => $catatan,
        ':id'          => $id
    ]);
} catch (Throwable $e) {
    echo "ERROR:DB_UPDATE_GAGAL";
    exit;
}

$stmt = $db->prepare("SELECT * FROM peminjaman_lab WHERE id_peminjaman = :id");
$stmt->execute([':id' => $id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$data) {
    echo "OK (data updated, email not sent – data peminjam tidak ditemukan)";
    exit;
}

$nama      = $data['nama_peminjam'] ?? '';
$email     = $data['email'] ?? '';
$instansi  = $data['instansi'] ?? '';
$tglAjuan  = $data['tanggal_pengajuan'] ?? '';
$tglPakai  = $data['tanggal_pakai'] ?? '';
$keperluan = $data['keperluan'] ?? '';
$statusLab = ucfirst($data['status'] ?? $status);

if (trim($email) === '') {
    echo "OK (Data tersimpan, email tidak dikirim karena email kosong)";
    exit;
}

$mail = new PHPMailer(true);

try {
    
    $mail->SMTPDebug = 0;

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'rakhmatariyantodummymail@gmail.com';
    $mail->Password   = 'sezb qgne mzzn rzus'; 
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->setFrom('rakhmatariyantodummymail@gmail.com', 'Laboratorium Business Analytics');
    $mail->addAddress($email, $nama);
    $mail->isHTML(true);
    $mail->Subject = "Status Peminjaman Laboratorium: $statusLab";
    
    $catatanHtml = nl2br(htmlspecialchars($catatan, ENT_QUOTES, 'UTF-8'));

    $mail->Body = "
        <div style='font-family:Arial; line-height:1.6'>
            <h3>Status Peminjaman Anda: <strong>{$statusLab}</strong></h3>
            <p><strong>Nama:</strong> {$nama}</p>
            <p><strong>Email:</strong> {$email}</p>
            <p><strong>Instansi:</strong> {$instansi}</p>
            <p><strong>Tanggal Pengajuan:</strong> {$tglAjuan}</p>
            <p><strong>Tanggal Pakai:</strong> {$tglPakai}</p>
            <p><strong>Keperluan:</strong> {$keperluan}</p>
            <br>
            <p><strong>Catatan Admin:</strong><br> {$catatanHtml}</p>
        </div>
    ";

    $mail->send();
    echo "OK";
} catch (Exception $e) {
    
    echo "OK (Data tersimpan, tapi email gagal: {$mail->ErrorInfo})";
}
