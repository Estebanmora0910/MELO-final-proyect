<?php
require_once '../Modelo/conexion.php';

$usuario = $_POST['usuario'] ?? '';
$nuevaContrasena = $_POST['nueva_contrasena'] ?? '';

if (empty($usuario) || empty($nuevaContrasena)) {
    echo json_encode(['error' => 'Faltan datos']);
    exit;
}

try {
    // Buscar usuario y su persona relacionada
    $stmt = $pdo->prepare("SELECT id_usuario, id_personas FROM usuario WHERE usu_nombre_usuario = ?");
    $stmt->execute([$usuario]);
    $usuarioEncontrado = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuarioEncontrado) {
        echo json_encode(['error' => 'Usuario no encontrado']);
        exit;
    }

    $id_usuario = $usuarioEncontrado['id_usuario'];
    $id_personas = $usuarioEncontrado['id_personas'];

    // Actualizar en tabla usuario
    $stmt1 = $pdo->prepare("UPDATE usuario SET usu_contrasena = ? WHERE id_usuario = ?");
    $stmt1->execute([$nuevaContrasena, $id_usuario]);

    // Actualizar en tabla personas (si existe la columna reg_contrasena)
    $stmt2 = $pdo->prepare("UPDATE personas SET reg_contrasena = ? WHERE id_personas = ?");
    $stmt2->execute([$nuevaContrasena, $id_personas]);

    echo json_encode(['mensaje' => 'Contraseña actualizada correctamente']);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error al actualizar: ' . $e->getMessage()]);
}
