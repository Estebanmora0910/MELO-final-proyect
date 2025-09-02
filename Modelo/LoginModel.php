<?php
require_once 'conexion.php';

class LoginModel {
    private $pdo;

    public function __construct() {
        $this->pdo = $GLOBALS['pdo'];
    }

    public function obtenerUsuarioPorNombre($nombre_usuario) {
    $stmt = $this->pdo->prepare("
        SELECT u.id_usuario, u.usu_nombre_usuario, u.usu_contrasena, u.id_rol, p.reg_nombre
        FROM usuario u
        LEFT JOIN personas p ON u.id_personas = p.id_personas
        WHERE u.usu_nombre_usuario = ?
    ");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $this->pdo->errorInfo());
    }
    $stmt->execute([$nombre_usuario]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
}
