<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer\phpmailer\src\Exception.php';
require 'phpmailer\phpmailer\src\PHPMailer.php';
require 'phpmailer\phpmailer\src\SMTP.php';

function enviarCorreo($destinatario, $asunto, $contenido) {
    $mail = new PHPMailer(true);

    try {
        // Configura el servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'soporte@boostsocialsmm.com';
        $mail->Password = '13042022@Jj';
        $mail->SMTPSecure = 'tls'; // Puedes usar 'tls' o 'ssl'
        $mail->Port = 465; // El puerto SMTP

        // Configura el remitente y el destinatario
        $mail->setFrom('soporte@boostsocialsmm.com', 'Social Boost');
        $mail->addAddress($destinatario);

        // Agrega el asunto y el cuerpo del mensaje
        $mail->Subject = $asunto;
        $mail->Body    = $contenido;

        // Envía el correo
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
