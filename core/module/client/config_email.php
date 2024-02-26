<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer\phpmailer\src\Exception.php';
require 'phpmailer\phpmailer\src\PHPMailer.php';
require 'phpmailer\phpmailer\src\SMTP.php';

// Crear una instancia de PHPMailer
$mail = new PHPMailer(true);

try {
    // Configurar el servidor SMTP
    $mail->isSMTP();
    $mail->Host       = 'smtp.hostinger.com'; // Cambia esto por el servidor SMTP que estés utilizando
    $mail->SMTPAuth   = true;
    $mail->Username   = 'soporte@boostsocialsmm.com'; // Cambia esto por tu dirección de correo electrónico
    $mail->Password   = '13042022@Jj'; // Cambia esto por tu contraseña de correo electrónico
    $mail->SMTPSecure = 'ssl';
    $mail->Port       = 465;

    // Configurar el remitente y destinatario
    $mail->setFrom('soporte@boostsocialsmm.com', 'Social Boost');
    $mail->addAddress($email, 'Destinatario');

    // Contenido del correo
    $mail->isHTML(true);
    $mail->Subject = 'Correo de prueba usando PHPMailer';
    $mail->Body    = 'Este es un correo de prueba enviado usando PHPMailer.';

    // Enviar el correo
    $mail->send();
    echo 'El correo ha sido enviado correctamente.';
} catch (Exception $e) {
    echo "Hubo un error al enviar el correo: {$mail->ErrorInfo}";
}


?>
