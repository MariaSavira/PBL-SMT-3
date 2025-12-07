<?php
session_start();

// koneksi
require __DIR__ . '/CRUD/koneksi.php';

// ===== PHPMailer (manual, sesuai struktur projectmu) =====
require __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require __DIR__ . '/../PHPMailer/src/SMTP.php';
require __DIR__ . '/../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


$conn = pg_connect("host=localhost port=5432 dbname=lab_ba user=postgres password=29082006");

$id      = $_POST['id'] ?? '';
$status  = $_POST['status'] ?? '';
$catatan = $_POST['catatan'] ?? '';

if ($id === '' || $status === '') {
    echo "DATA_KURANG";
    exit;
}

// Ambil nama admin dari session
$admin_name = $_SESSION['admin_name'] ?? 'Admin';

// Jika pending → approved_by kosong
$approved_by_value = ($status === 'pending') ? null : $admin_name;

// Update database
$query = "
    UPDATE peminjaman_lab 
    SET status = $1,
        approved_by = $2,
        catatan_admin = $3
    WHERE id_peminjaman = $4
";
$result = pg_query_params($conn, $query, [$status, $approved_by_value, $catatan, $id]);

if (!$result) {
    echo "ERROR: " . pg_last_error($conn);
    exit;
}

// Ambil data peminjam untuk email
$q_data = pg_query_params($conn, "SELECT * FROM peminjaman_lab WHERE id_peminjaman = $1", [$id]);
$data = pg_fetch_assoc($q_data);

if ($data) {
    $nama = $data['nama_peminjam'];
    $email = $data['email'];
    $instansi = $data['instansi'];
    $tgl_pengajuan = $data['tanggal_pengajuan'];
    $tgl_pakai = $data['tanggal_pakai'];
    $keperluan = $data['keperluan'];
    $status_lab = $data['status'];

    // === KIRIM EMAIL ===
    $mail = new PHPMailer(true);
    try {
        // Konfigurasi SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'nabilaputrivaliandra29@gmail.com'; // ubah ke email pengirim
        $mail->Password = 'ylyn qkic lyvu fzla'; // gunakan app password (bukan password asli)
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Pengirim dan penerima
        $mail->setFrom('nabilaputrivaliandra29@gmail.com', 'Laboratorium Business Analytics');
        $mail->addAddress($email, $nama);

        // Konten email
        $mail->isHTML(true);
        $mail->Subject = "Pemberitahuan Peminjaman Laboratorium - $status_lab";

        $mail->Body = "
        <div style='font-family:Arial, sans-serif; line-height:1.6;'>
            <p><strong>Nama :</strong> $nama</p>
            <p><strong>Email :</strong> $email</p>
            <p><strong>Instansi :</strong> $instansi</p>
            <p><strong>Tgl Pengajuan :</strong> $tgl_pengajuan</p>
            <p><strong>Tgl Pakai :</strong> $tgl_pakai</p>
            <p><strong>Keperluan :</strong> $keperluan</p>
            <p><strong>Status :</strong> $status_lab</p>
            <br>
            <p><strong>Catatan Admin:</strong><br>$catatan</p>
        </div>
        ";

        $mail->send();
        echo "OK";
    } catch (Exception $e) {
        echo "OK (Data tersimpan, tapi email gagal dikirim): {$mail->ErrorInfo}";
    }
} else {
    echo "OK (Data diperbarui, tapi data email tidak ditemukan)";
}
?>
