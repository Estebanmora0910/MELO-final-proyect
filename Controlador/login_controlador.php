<?php
session_start();
require_once '../Modelo/conexion.php';
require_once '../Modelo/LoginModel.php';

// Habilitar errores para depuración (desactivar en producción)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (empty($usuario) || empty($password)) {
        header("Location: ../Vista/html/login.php?mensaje=Por favor completa todos los campos.");
        exit();
    }

    $modelo = new LoginModel();
    $datos = $modelo->obtenerUsuarioPorNombre($usuario);

    // Bloque de depuración (comentar en producción)
    /*
    echo "Usuario ingresado: " . htmlspecialchars($usuario) . "<br>";
    echo "Contraseña ingresada: " . htmlspecialchars($password) . "<br>";
    echo "Datos del usuario: " . print_r($datos, true) . "<br>";
    echo "En base de datos: " . ($datos['usu_contrasena'] ?? 'No encontrado') . "<br>";
    echo "¿Coincide?: " . (isset($datos['usu_contrasena']) && password_verify($password, $datos['usu_contrasena']) ? 'Sí' : 'No') . "<br>";
    exit();
    */

    if (!$datos || !password_verify($password, $datos['usu_contrasena'])) {
        header("Location: /melo8-main/Vista/html/login.php?mensaje=Usuario o contraseña incorrectos.");
        exit();
    }

    // Autenticación correcta: guardar datos en la sesión
    $_SESSION['id_usuario'] = $datos['id_usuario'];
    $_SESSION['usuario'] = $datos['usu_nombre_usuario'];
    $_SESSION['rol'] = $datos['id_rol'];
    $_SESSION['nombre'] = $datos['reg_nombre'] ?? '';

    // Redirigir según el rol
    switch ($_SESSION['rol']) {
        case 1: // Administrador
            header("Location: /melo8-main/Vista/html/administrador.php");
            break;
        case 3: // Cliente
            header("Location: /melo8-main/Vista/html/catalogo.php");
            break;
        case 2: // Vendedor (opcional, si necesitas una vista específica)
            header("Location: /melo8-main/Vista/html/vendedor.php"); // Ajusta esta ruta si existe una vista para vendedores
            break;
        default:
            // En caso de un rol no reconocido, redirigir a una página por defecto o mostrar error
            header("Location: /melo8-main/Vista/html/login.php?mensaje=Rol no reconocido.");
            break;
    }
    exit();
}
