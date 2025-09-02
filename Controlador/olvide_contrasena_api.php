<?php
require_once __DIR__ . '/../Modelo/conexion.php';

// Cargar PHPMailer manualmente
require_once __DIR__ . '/../PHPMailer_Listo/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer_Listo/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer_Listo/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $correo = trim($_POST['correo'] ?? '');

    if (empty($correo)) {
        header('Location: ../Vista/html/olvide_contrasena.php?mensaje=Debe ingresar su correo.');
        exit;
    }

    // Buscar el usuario por correo en personas usando la relación correcta
    $stmt = $pdo->prepare("
        SELECT u.id_usuario, p.reg_nombre
        FROM usuario u
        INNER JOIN personas p ON p.id_personas = u.id_personas
        WHERE p.reg_correo = ?
    ");
    $stmt->execute([$correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        header('Location: ../Vista/html/olvide_contrasena.php?mensaje=Correo no registrado.');
        exit;
    }

    // Generar token seguro
    $token = bin2hex(random_bytes(16));
    $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // Guardar token en BD (reset_password), si ya existe actualiza
    $stmt = $pdo->prepare("
        INSERT INTO reset_password (id_usuario, token, expiracion)
        VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE token = ?, expiracion = ?
    ");
    $stmt->execute([$usuario['id_usuario'], $token, $expiracion, $token, $expiracion]);

    // Configurar PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'estebaalejandromoraavila@gmail.com'; // tu correo
        $mail->Password = 'pock zeae qxgn hxzg';  // contraseña de aplicación
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('estebaalejandromoraavila@gmail.com', 'Jabones DR');
        $mail->addAddress($correo, $usuario['reg_nombre']);

        $mail->isHTML(true);
        $mail->Subject = 'Recuperar Contraseña - Jabones DR';
        $mail->Body = "
            <p>Hola {$usuario['reg_nombre']},</p>
            <p>Haz clic en el siguiente enlace para cambiar tu contraseña:</p>
            <p><a href='http://localhost/melo8-main/Vista/html/restablecer_contrasena.php?token={$token}'>Restablecer Contraseña</a></p>
            <p>Este enlace expirará en 1 hora.</p>
        ";

        $mail->send();
        header('Location: ../Vista/html/olvide_contrasena.php?mensaje=Se ha enviado un enlace a tu correo.');
    } catch (Exception $e) {
        header('Location: ../Vista/html/olvide_contrasena.php?mensaje=Error al enviar el correo: ' . $mail->ErrorInfo);
    }
}
