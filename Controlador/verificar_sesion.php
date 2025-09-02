<?php
session_start();

function verificarSesion() {
    if (!isset($_SESSION['id_usuario'])) {
        header("Location: index.php");
        exit();
    }
}

function obtenerNombreUsuario($conexion, $usuario_id) {
    $sql = "SELECT p.usu_nombre_usuario 
            FROM usuario u
            INNER JOIN personas p ON u.id_personas = p.id_personas
            WHERE u.id_usuario = ?";
    
    $stmt = $conexion->prepare($sql);
    $stmt->bind_param("i", $usuario_id);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        return $fila['usu_nombre_usuario'];
    } else {
        return "Usuario Desconocido";
    }
}

// === Si se llamó este archivo directamente por fetch ===
// Respuesta para AJAX (fetch)
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo isset($_SESSION['id_usuario']) ? '1' : '0';
    exit();
}

?>
