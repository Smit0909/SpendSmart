<?php

session_start();

if (!isset($_SESSION['to_email'])) {
    header('Location: login.php');
    exit();
}

$to_email = $_SESSION['to_email'];
$otp = rand(100000, 999999);

$_SESSION['otp'] = $otp;

//email logic
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

$mail = new PHPMailer(true);

try {

    $mail->SMTPDebug = SMTP::DEBUG_SERVER;
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sender_mail';
    $mail->Password   = 'secret';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port       = 465;

    //Recipients
    $mail->setFrom('sender_mail', 'SpendSmart Admin');
    $mail->addAddress($to_email);

    //Content
    $mail->isHTML(true);
    $mail->Subject = 'Your OTP Code';
    $mail->Body    = 'Your OTP code is: <b>' . $otp . '</b>';

    $mail->send();
    $_SESSION['isOTPSent'] = TRUE;
    header('Location: otpVerification.php');
    exit();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

