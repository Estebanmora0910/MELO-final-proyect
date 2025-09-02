<?php
require_once '../Modelo/conexion.php';

header('Content-Type: application/json');

// Verifica que haya datos enviados por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Método no permitido"]);
    exit;
}

// Recibe los datos
$usuario = $_POST['usuario'] ?? '';
$nueva_contrasena = $_POST['nueva_contrasena'] ?? '';

// Validación básica
if (empty($usuario) || empty($nueva_contrasena)) {
    echo json_encode(["error" => "Todos los campos son obligatorios"]);
    exit;
}

// Verifica si el usuario existe
$sql = "SELECT id_usuario FROM usuario WHERE usu_nombre_usuario = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$usuario]);
$usuarioEncontrado = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuarioEncontrado) {
    echo json_encode(["error" => "El usuario no existe"]);
    exit;
}

// Actualiza la contraseña
$update = "UPDATE usuario SET usu_contrasena = ? WHERE usu_nombre_usuario = ?";
$stmt = $pdo->prepare($update);
if ($stmt->execute([$nueva_contrasena, $usuario])) {
    echo json_encode(["success" => "Contraseña actualizada correctamente"]);
} else {
    echo json_encode(["error" => "No se pudo actualizar la contraseña"]);
}
