<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';


header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'METHOD_NOT_ALLOWED';
    exit;
}


$nama     = isset($_POST['nama'])     ? trim($_POST['nama'])     : '';
$instansi = isset($_POST['instansi']) ? trim($_POST['instansi']) : '';
$alasan   = isset($_POST['alasan'])   ? trim($_POST['alasan'])   : '';
$pesan    = isset($_POST['pesan'])    ? trim($_POST['pesan'])    : '';



if ($nama === '' || $alasan === '' || $alasan === '-- Pilih Alasan --' || $pesan === '') {
    echo 'VALIDATION_ERROR';
    exit;
}

$mail = new PHPMailer(true);

try {

    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'rakhmatariyantodummymail@gmail.com';
    $mail->Password   = 'prqg pkpc adbf mvwr';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->setFrom('mariasavira2006@gmail.com', 'Form Kontak Lab BA');
    $mail->addAddress('rakhmatariyantodummymail@gmail.com', 'Admin Lab BA');

    if (!empty($_FILES['dokumen']['tmp_name']) && $_FILES['dokumen']['error'] === UPLOAD_ERR_OK) {
        $mail->addAttachment($_FILES['dokumen']['tmp_name'], $_FILES['dokumen']['name']);
    }

    $mail->isHTML(true);

    $instansiFinal = ($instansi === '') ? '-' : $instansi;

    $mail->Subject = htmlspecialchars($alasan) . ' - ' . htmlspecialchars($instansiFinal) . ' - ' . htmlspecialchars($nama);

    $body  = '<h3>Pesan Baru dari Form Kontak</h3>';
    $body .= '<p><strong>Nama:</strong> ' . htmlspecialchars($nama) . '</p>';
    $body .= '<p><strong>Instansi:</strong> ' . htmlspecialchars($instansi) . '</p>';
    $body .= '<p><strong>Alasan:</strong> ' . htmlspecialchars($alasan) . '</p>';
    $body .= '<p><strong>Pesan:</strong><br>' . nl2br(htmlspecialchars($pesan)) . '</p>';
    $body .= '<p><em>Email ini dikirim otomatis dari website PBL-SMT-3.</em></p>';

    $mail->Body = $body;

    $mail->send();

    header('Location: User/KontakKami.php?status=success');
    exit;
} catch (Exception $e) {

    header('Location: User/KontakKami.php?status=error');
    exit;
}
