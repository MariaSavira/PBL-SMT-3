<?php
require '../admin/CRUD/koneksi.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/SMTP.php';

header('Content-Type: application/json');

try {
    // Ambil data form
    $nama_peminjam     = $_POST['nama_peminjam'];
    $email             = $_POST['email'];
    $instansi          = $_POST['instansi'];
    $tanggal_pengajuan = date('Y-m-d');
    $tanggal_pakai     = $_POST['tanggal_pakai'];
    $keperluan         = $_POST['keperluan'];

    // Insert ke database
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

    // ============================================
    //  KIRIM EMAIL KE ADMIN
    // ============================================
    $mail = new PHPMailer(true);

    try {
        // Setting SMTP
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'nabilaputrivaliandra29@gmail.com';
        $mail->Password   = 'ylyn qkic lyvu fzla'; // APP PASSWORD
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Pengirim & penerima
        $mail->setFrom('nabilaputrivaliandra29@gmail.com', 'Notifikasi Peminjaman Lab');
        $mail->addAddress('nabilaputrivaliandra29@gmail.com'); // admin email

        // Isi email
        $mail->isHTML(true);
        $mail->Subject = "Peminjaman Baru dari $nama_peminjam";
        $mail->Body = "
            <h3>Ada pengajuan peminjaman laboratorium baru!</h3>
            <p><strong>Nama:</strong> $nama_peminjam</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Instansi:</strong> $instansi</p>
            <p><strong>Tanggal Pakai:</strong> $tanggal_pakai</p>
            <p><strong>Keperluan:</strong> $keperluan</p>
        ";

        $mail->send();

    } catch (Exception $e) {
        // Kalau email gagal, tetap lanjut (tidak batalkan peminjaman)
        // Tapi kalau mau lihat error: uncomment baris ini
        // echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    echo json_encode(['status' => 'success']);

} catch (PDOException $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
