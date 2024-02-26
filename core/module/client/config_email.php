<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/phpmailer/src/Exception.php';
require 'phpmailer/phpmailer/src/PHPMailer.php';
require 'phpmailer/phpmailer/src/SMTP.php';

// Función para enviar correo electrónico
function enviarCorreo($email, $asunto, $contenido) {
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
        $mail->addAddress($email);

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body    = $contenido;

        // Enviar el correo
        $mail->send();
        return true; // Indicar que el correo se envió correctamente
    } catch (Exception $e) {
        return false; // Indicar que hubo un error al enviar el correo
    }
}


?>
