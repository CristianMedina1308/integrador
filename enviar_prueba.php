<?php
require 'includes/PHPMailer/PHPMailer.php';
require 'includes/PHPMailer/SMTP.php';
require 'includes/PHPMailer/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // DEPURACIÓN
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = 'html';

    // CONFIGURACIÓN SMTP
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'mecristian14@gmail.com';         // 👈 Reemplaza por el tuyo
    $mail->Password = 'xxxxxxxxx';      // 👈 Usa una contraseña de app si es Gmail
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // DESTINATARIO
    $mail->setFrom('mecristian14@gmail.com', 'Tienda de Maquillaje'); // 👈 Igual que el Username
    $mail->addAddress('mecristian14@gmail.com', 'Cliente de Prueba'); // 👈 Puedes usar el mismo para probar

    // CONTENIDO
    $mail->isHTML(true);
    $mail->Subject = 'Prueba de envío de correo';
    $mail->Body    = '<h3>Hola, este es un mensaje de prueba.</h3><p>Si lo ves, ¡el correo funciona!</p>';

    // ENVIAR
    $mail->send();
    echo '✅ ¡Correo enviado exitosamente!';
} catch (Exception $e) {
    echo '❌ Error al enviar el correo: ', $mail->ErrorInfo;
}
