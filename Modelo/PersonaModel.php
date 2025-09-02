<?php
require_once __DIR__ . '/conexion.php';

class PersonasModel {
    public static function obtenerPersonas($filtro = null, $rol = null, $search = null, $page = 1, $perPage = 10) {
        global $pdo;
        $offset = ($page - 1) * $perPage;

        $query = "SELECT DISTINCT p.*, u.usu_nombre_usuario, u.id_rol, r.tipo_rol 
                  FROM personas p 
                  LEFT JOIN usuario u ON p.id_personas = u.id_personas 
                  LEFT JOIN rol r ON u.id_rol = r.id_rol";
        $params = [];

        $whereClauses = [];
        if ($filtro === 'clientes') {
            $whereClauses[] = "u.id_rol = ?";
            $params[] = 3;
        } elseif ($rol && $rol !== 'todos') {
            $whereClauses[] = "u.id_rol = ?";
            $params[] = $rol;
        }

        if ($search) {
            $search = "%$search%";
            $whereClauses[] = "(p.reg_nombre LIKE ? OR p.reg_correo LIKE ? OR u.usu_nombre_usuario LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        // Usar marcadores posicionales para LIMIT y OFFSET
        $query .= " LIMIT ? OFFSET ?";
        $params[] = (int)$perPage;
        $params[] = (int)$offset;

        $stmt = $pdo->prepare($query);

        // Vincular todos los parámetros
        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function obtenerTotalPersonas($filtro = null, $rol = null, $search = null) {
        global $pdo;
        $query = "SELECT COUNT(DISTINCT p.id_personas) as total 
                  FROM personas p 
                  LEFT JOIN usuario u ON p.id_personas = u.id_personas";
        $params = [];

        $whereClauses = [];
        if ($filtro === 'clientes') {
            $whereClauses[] = "u.id_rol = ?";
            $params[] = 3;
        } elseif ($rol && $rol !== 'todos') {
            $whereClauses[] = "u.id_rol = ?";
            $params[] = $rol;
        }

        if ($search) {
            $search = "%$search%";
            $whereClauses[] = "(p.reg_nombre LIKE ? OR p.reg_correo LIKE ? OR u.usu_nombre_usuario LIKE ?)";
            $params[] = $search;
            $params[] = $search;
            $params[] = $search;
        }

        if (!empty($whereClauses)) {
            $query .= " WHERE " . implode(" AND ", $whereClauses);
        }

        $stmt = $pdo->prepare($query);
        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }

        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function obtenerPersonaPorId($id) {
        global $pdo;
        $stmt = $pdo->prepare("SELECT p.*, u.id_rol, r.tipo_rol 
                              FROM personas p 
                              LEFT JOIN usuario u ON p.id_personas = u.id_personas 
                              LEFT JOIN rol r ON u.id_rol = r.id_rol 
                              WHERE p.id_personas = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerRoles() {
        global $pdo;
        $stmt = $pdo->query("SELECT id_rol, tipo_rol FROM rol");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function actualizarPersona($id, $datos) {
        global $pdo;
        try {
            $pdo->beginTransaction();

            // Solo actualizamos el rol si está presente
            if (isset($datos['id_rol'])) {
                $stmtRol = $pdo->prepare("UPDATE usuario SET id_rol = ? WHERE id_personas = ?");
                $stmtRol->execute([$datos['id_rol'], $id]);
            }

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw new Exception("Error al actualizar persona: " . $e->getMessage());
        }
    }
}
?>