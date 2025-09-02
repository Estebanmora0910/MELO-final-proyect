<?php
require_once 'conexion.php';

class AdminModel {
    private $db;

    public function __construct() {
        global $pdo;
        $this->db = $pdo;
    }

    // Obtener todos los usuarios con sus roles
    public function getUsersWithRoles() {
        $query = "SELECT p.id_personas, p.reg_nombre, r.tipo_rol, 
                        CASE WHEN u.id_rol IS NOT NULL THEN 'Activo' ELSE 'Inactivo' END AS estado
                FROM personas p
                LEFT JOIN usuario u ON p.id_personas = u.id_personas
                LEFT JOIN rol r ON u.id_rol = r.id_rol
                ORDER BY p.reg_nombre";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Actualizar rol de usuario
    public function updateUserRole($id_personas, $id_rol) {
        $query = "UPDATE usuario SET id_rol = :id_rol WHERE id_personas = :id_personas";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id_rol', $id_rol, PDO::PARAM_INT);
        $stmt->bindParam(':id_personas', $id_personas, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // Obtener todos los roles disponibles
    public function getRoles() {
        $query = "SELECT id_rol, tipo_rol FROM rol";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>