<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHPMailer_Listo/src/PHPMailer.php';
require '../PHPMailer_Listo/src/SMTP.php';
require '../PHPMailer_Listo/src/Exception.php';

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    header("Location: index.php");
    exit();
}

$nombre = htmlspecialchars(trim($_POST['nombre']));
$email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
$mensaje = htmlspecialchars(trim($_POST['mensaje']));

if (!$nombre || !$email || !$mensaje) {
    echo "<script>alert('Por favor completa todos los campos correctamente.'); window.history.back();</script>";
    exit();
}

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'estebaalejandromoraavila@gmail.com';
    $mail->Password = 'pock zeae qxgn hxzg';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom($email, $nombre);
    $mail->addAddress('estebaalejandromoraavila@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = 'Nuevo mensaje de ' . $nombre;
    $mail->Body = "<p><strong>Nombre:</strong> $nombre</p>
                   <p><strong>Email:</strong> $email</p>
                   <p><strong>Mensaje:</strong><br>$mensaje</p>";

    if ($mail->send()) {
        echo "<script>alert('¡Mensaje enviado exitosamente!'); window.location.href='../index.php';</script>";
    } else {
        echo "<script>alert('No se pudo enviar el mensaje.'); window.history.back();</script>";
    }

} catch (Exception $e) {
    error_log("Error al enviar correo: {$mail->ErrorInfo}");
    echo "<script>alert('Ocurrió un error al enviar el mensaje.'); window.history.back();</script>";
}
?>


