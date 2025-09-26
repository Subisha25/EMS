<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../../vendor/autoload.php';

function sendLeaveMail($to, $subject, $body) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Use your mail server
        $mail->SMTPAuth = true;
        $mail->Username = 'pcstech2021@gmail.com'; // Your email
        $mail->Password = 'ndkx mmtq mhtz fxug'; // Use app password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('pcstech2021@gmail.com', 'EMS System');
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $body;

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
