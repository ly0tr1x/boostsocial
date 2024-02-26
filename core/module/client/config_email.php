<?php
// Incluye los archivos necesarios de PHPMailer
require 'lib/phpmailer/src/PHPMailer.php';
require 'lib/phpmailer/src/SMTP.php';
require 'lib/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

function enviar_correo($destinatario, $asunto, $contenido) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com'; // Cambia esto al host SMTP correspondiente
        $mail->SMTPAuth = true;
        $mail->Username = 'soporte@boostsocialsmm.com'; // Cambia esto al nombre de usuario de tu cuenta de correo electrónico
        $mail->Password = '13042022@Jj'; // Cambia esto a tu contraseña de correo electrónico
        $mail->SMTPSecure = 'tls';
        $mail->Port = 465;

        // Destinatario y remitente
        $mail->setFrom('soporte@boostsocialsmm.com', 'Social Boost');
        $mail->addAddress($destinatario);

        // Contenido del correo electrónico
        $mail->isHTML(true);
        $mail->Subject = $asunto;
        $mail->Body = $contenido;

        // Enviar correo electrónico
        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
