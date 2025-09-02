<?php
session_start();
require_once __DIR__. '/../Modelo/RegistrarseModel.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre     = trim($_POST['nombre']);
    $correo     = trim($_POST['correo']);
    $contrasena = trim($_POST['contrasena']);
    $direccion  = trim($_POST['direccion']);
    $ciudad     = trim($_POST['ciudad']);
    $telefono   = trim($_POST['telefono']);
    $usuario    = trim($_POST['usuario']);
    $confirma   = trim($_POST['confirmar_contrasena']);

    // 1. Validar campos vacíos
    if (
        empty($nombre) || empty($correo) || empty($contrasena) || 
        empty($direccion) || empty($ciudad) || empty($telefono) || 
        empty($usuario) || empty($confirma)
    ) {
        $_SESSION['error'] = "Todos los campos son obligatorios.";
        header("Location: /../Vista/html/registrarse.php");
        exit();
    }

    // 2. Validar contraseñas coincidan
    if ($contrasena !== $confirma) {
        $_SESSION['error'] = "Las contraseñas no coinciden.";
        header("Location: /../Vista/html/registrarse.php");
        exit();
    }

    // 3. Validar que el usuario o correo no estén registrados
    $modelo = new RegistrarseModel();
    if ($modelo->usuarioOCorreoExiste($correo, $usuario)) {
        $_SESSION['error'] = "El usuario o correo ya están registrados.";
        header("Location: /../Vista/html/registrarse.php");
        exit();
    }

    // 4. Encriptar la contraseña
    $hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // 5. Registrar usuario
    if ($modelo->registrarUsuario($nombre, $correo, $hash, $direccion, $ciudad, $telefono, $usuario)) {
        $_SESSION['exito'] = "Registro exitoso. Ya puedes iniciar sesión.";
        header("Location: /melo8-main/Vista/html/registro-exitoso.php");
        exit();
    } else {
        $_SESSION['error'] = "Error al registrar el usuario.";
        header("Location: /../Vista/html/registrarse.php");
        exit();
    }
}
