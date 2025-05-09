<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'includes/PHPMailer/src/Exception.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';

function enviarFacturaPorCorreo($correoDestino, $nombreUsuario, $archivoFacturaPDF, $nombreAdjunto) {
  $mail = new PHPMailer(true);

  try {
    // Configuración del servidor SMTP (puedes usar Gmail o tu hosting)
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';             // Cambia esto si usas otro proveedor
    $mail->SMTPAuth = true;
    $mail->Username = 'mecristian14@gmail.com';     // Tu correo
    $mail->Password = 'xxxx';      // Contraseña de app o SMTP
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    // Remitente y destinatario
    $mail->setFrom('mecristian14@gmail.com', 'Tienda de Maquillaje');
    $mail->addAddress($correoDestino, $nombreUsuario);

    // Asunto y cuerpo del correo
    $mail->isHTML(true);
    $mail->Subject = '📄 Factura de tu pedido';
    $mail->Body = "Hola <strong>$nombreUsuario</strong>,<br><br>Gracias por tu compra. Adjuntamos la factura en PDF.<br><br>¡Esperamos que disfrutes nuestros productos!";

    // Adjuntar PDF
    $mail->addAttachment($archivoFacturaPDF, $nombreAdjunto);

    $mail->send();
    return true;
  } catch (Exception $e) {
    return false;
  }
}
