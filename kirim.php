<?php
// kirim.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// SESUAIKAN PATH DENGAN STRUKTUR PUNYA KAMU
require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

// supaya JS / browser mudah baca respon
header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'METHOD_NOT_ALLOWED';
    exit;
}

// ambil data dari form
$nama     = isset($_POST['nama'])     ? trim($_POST['nama'])     : '';
$instansi = isset($_POST['instansi']) ? trim($_POST['instansi']) : '';
$alasan   = isset($_POST['alasan'])   ? trim($_POST['alasan'])   : '';
$pesan    = isset($_POST['pesan'])    ? trim($_POST['pesan'])    : '';

// validasi sederhana
// di sini aku bikin instansi BOLEH KOSONG
if ($nama === '' || $alasan === '' || $alasan === '-- Pilih Alasan --' || $pesan === '') {
    echo 'VALIDATION_ERROR';
    exit;
}

$mail = new PHPMailer(true);

try {
    // ========== KONFIGURASI SMTP (GMAIL) ==========
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    // GANTI DENGAN EMAIL GMAIL KAMU
    $mail->Username   = 'cwawaaa123@gmail.com';
    // GANTI DENGAN APP PASSWORD (BUKAN password biasa)
    $mail->Password   = 'vbrw xvrt qzpo ydna';

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // ========== PENGIRIM & PENERIMA ==========
    $mail->setFrom('kussyafira@gmail.com', 'Form Kontak Lab BA');
    $mail->addAddress('cwawaaa123@gmail.com', 'Admin Lab BA');

    // kalau nanti nambah field email pengirim di form, bisa pakai:
    // $mail->addReplyTo($_POST['email'], $nama);

    // ========== ATTACHMENT (PDF) ==========
    // NAME HARUS SAMA DENGAN name="dokumen" DI FORM
    if (!empty($_FILES['dokumen']['tmp_name']) && $_FILES['dokumen']['error'] === UPLOAD_ERR_OK) {
        $mail->addAttachment($_FILES['dokumen']['tmp_name'], $_FILES['dokumen']['name']);
    }

    // ========== ISI EMAIL ==========
    $mail->isHTML(true);
    // kalau instansi kosong, isi dengan "-"
        $instansiFinal = ($instansi === '') ? '-' : $instansi;

        // format subject: [Tipe Email] - [Instansi] - [Nama]
        $mail->Subject = htmlspecialchars($alasan) . ' - ' . htmlspecialchars($instansiFinal) . ' - ' . htmlspecialchars($nama);


    $body  = '<h3>Pesan Baru dari Form Kontak</h3>';
    $body .= '<p><strong>Nama:</strong> ' . htmlspecialchars($nama) . '</p>';
    $body .= '<p><strong>Instansi:</strong> ' . htmlspecialchars($instansi) . '</p>';
    $body .= '<p><strong>Alasan:</strong> ' . htmlspecialchars($alasan) . '</p>';
    $body .= '<p><strong>Pesan:</strong><br>' . nl2br(htmlspecialchars($pesan)) . '</p>';
    $body .= '<p><em>Email ini dikirim otomatis dari website PBL-SMT-3.</em></p>';

    $mail->Body = $body;

    // kirim email
    $mail->send();
    // kalau berhasil
    header('Location: User/KontakKami.html?status=success');
    exit;
} catch (Exception $e) {
    // kalau gagal
    header('Location: User/KontakKami.html?status=error');
    exit;
}

