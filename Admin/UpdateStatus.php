<?php
session_start();

// KONEKSI PDO (yang BENAR)
require __DIR__ . '/../Koneksi/KoneksiValia.php';

// PHPMailer 
require __DIR__ . '/../../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../../PHPMailer/src/SMTP.php';
require __DIR__ . '/../../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//  DATA POST 
$id      = $_POST['id'] ?? '';
$status  = $_POST['status'] ?? '';
$catatan = $_POST['catatan'] ?? '';

if ($id === '' || $status === '') {
    echo "DATA_KURANG";
    exit;
}

// Nama admin dari session
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Jika status pending → approved_by kosong
$approved_by_value = ($status === 'pending') ? null : $admin_name;


// UPDATE DATABASE 
$update = $db->prepare("
    UPDATE peminjaman_lab
    SET
        status = :status,
        approved_by = :approved_by,
        catatan_admin = :catatan
    WHERE id_peminjaman = :id
");

$update->execute([
    ':status'      => $status,
    ':approved_by' => $approved_by_value,
    ':catatan'     => $catatan,
    ':id'          => $id
]);


// AMBIL DATA UNTUK EMAIL 
$stmt = $db->prepare("SELECT * FROM peminjaman_lab WHERE id_peminjaman = :id");
$stmt->execute([':id' => $id]);
$data = $stmt->fetch();

if (!$data) {
    echo "OK (data updated, email not sent – data peminjam tidak ditemukan)";
    exit;
}


// SIAPKAN DATA EMAIL
$nama        = $data['nama_peminjam'];
$email       = $data['email'];
$instansi    = $data['instansi'];
$tglAjuan    = $data['tanggal_pengajuan'];
$tglPakai    = $data['tanggal_pakai'];
$keperluan   = $data['keperluan'];
$status_lab  = ucfirst($data['status']);


//  KIRIM EMAIL 
$mail = new PHPMailer(true);

try {
    $mail->SMTPDebug = 2;
$mail->Debugoutput = 'html';

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'rakhmatariyantodummymail@gmail.com'; 
    $mail->Password   = 'sezb qgne mzzn rzus'; // APP PASSWORD YANG BENAR
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('rakhmatariyantodummymail@gmail.com', 'Laboratorium Business Analytics');
    $mail->addAddress($email, $nama);

    $mail->isHTML(true);
    $mail->Subject = "Status Peminjaman Laboratorium: $status_lab";

    $mail->Body = "
        <div style='font-family:Arial; line-height:1.6'>
            <h3>Status Peminjaman Anda: <strong>$status_lab</strong></h3>
            <p><strong>Nama:</strong> $nama</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Instansi:</strong> $instansi</p>
            <p><strong>Tanggal Pengajuan:</strong> $tglAjuan</p>
            <p><strong>Tanggal Pakai:</strong> $tglPakai</p>
            <p><strong>Keperluan:</strong> $keperluan</p>
            <br>
            <p><strong>Catatan Admin:</strong><br> $catatan</p>
        </div>
    ";

    $mail->send();
    echo "OK";

} catch (Exception $e) {
    echo "OK (Data tersimpan, tapi email gagal: {$mail->ErrorInfo})";
}