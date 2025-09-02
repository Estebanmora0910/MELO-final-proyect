<?php
require_once __DIR__ . '/../Modelo/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['token'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($token) || empty($password) || empty($password_confirm)) {
        header('Location: ../Vista/html/restablecer_contrasena.php?token=' . urlencode($token) . '&mensaje=Todos los campos son obligatorios.');
        exit;
    }

    if ($password !== $password_confirm) {
        header('Location: ../Vista/html/restablecer_contrasena.php?token=' . urlencode($token) . '&mensaje=Las contraseñas no coinciden.');
        exit;
    }

    // Verificar que el token exista y no esté expirado
    $stmt = $pdo->prepare("SELECT id_usuario FROM reset_password WHERE token = ? AND expiracion >= NOW()");
    $stmt->execute([$token]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Token inválido o expirado.");
    }

    // Hashear la nueva contraseña
    $password_hashed = password_hash($password, PASSWORD_DEFAULT);

    // Actualizar contraseña en usuario
    $stmt = $pdo->prepare("UPDATE usuario SET usu_contrasena = ? WHERE id_usuario = ?");
    $stmt->execute([$password_hashed, $usuario['id_usuario']]);

    // Opcional: también actualizar en personas si deseas guardar la contraseña allí
    // $stmt = $pdo->prepare("UPDATE personas SET usu_contrasena = ? WHERE id_personas = (SELECT id_personas FROM usuario WHERE id_usuario = ?)");
    // $stmt->execute([$password_hashed, $usuario['id_usuario']]);

    // Eliminar el token
    $stmt = $pdo->prepare("DELETE FROM reset_password WHERE id_usuario = ?");
    $stmt->execute([$usuario['id_usuario']]);

    header('Location: ../Vista/html/login.php?mensaje=Contraseña actualizada correctamente.');
    exit;
}
