<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/SMTP.php';

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'nabilaputrivaliandra29@gmail.com';
    $mail->Password = 'ylyn qkic lyvu fzla'; // app password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('nabilaputrivaliandra29@gmail.com', 'TEST SMTP');
    $mail->addAddress('nabilaputrivaliandra29@gmail.com');

    $mail->Subject = 'TEST SMTP';
    $mail->Body = 'Kalau email ini masuk, SMTP kamu berfungsi.';

    $mail->send();
    echo "EMAIL TERKIRIM!";
} catch (Exception $e) {
    echo "SMTP ERROR: <br><br>" . $mail->ErrorInfo;
}
