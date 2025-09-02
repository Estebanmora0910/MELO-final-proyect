<?php
require_once __DIR__ . '/conexion.php';

class InventoryModel {
    public static function obtenerInventarioFiltrado($categoria = '', $search = '', $limit = 10, $offset = 0) {
        global $pdo;
        $sql = "SELECT i.id_inventario, p.pro_nombre, c.tipo_categoria AS categoria, i.inv_disponibilidad, i.fecha_ingreso, p.activo
                FROM inventario i
                JOIN producto p ON i.id_producto = p.id_producto
                JOIN categoria c ON p.id_categoria = c.id_categoria
                WHERE p.activo = 1";
        $params = [];

        if (!empty($categoria)) {
            $sql .= " AND c.tipo_categoria = :categoria";
            $params[':categoria'] = $categoria;
        }
        if (!empty($search)) {
            $sql .= " AND p.pro_nombre LIKE :search";
            $params[':search'] = "%$search%";
        }

        $sql .= " ORDER BY i.id_inventario DESC LIMIT :limit OFFSET :offset";
        $params[':limit'] = (int)$limit;
        $params[':offset'] = (int)$offset;

        $stmt = $pdo->prepare($sql);
        foreach ($params as $param => $value) {
            $type = ($param === ':limit' || $param === ':offset') ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($param, $value, $type);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function contarInventarioFiltrado($categoria = '', $search = '') {
        global $pdo;
        $sql = "SELECT COUNT(*) as total
                FROM inventario i
                JOIN producto p ON i.id_producto = p.id_producto
                JOIN categoria c ON p.id_categoria = c.id_categoria
                WHERE p.activo = 1";
        $params = [];

        if (!empty($categoria)) {
            $sql .= " AND c.tipo_categoria = :categoria";
            $params[':categoria'] = $categoria;
        }
        if (!empty($search)) {
            $sql .= " AND p.pro_nombre LIKE :search";
            $params[':search'] = "%$search%";
        }

        $stmt = $pdo->prepare($sql);
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function sincronizarInventario() {
        global $pdo;
        try {
            $stmt = $pdo->prepare("
                UPDATE inventario i
                JOIN (
                    SELECT id_producto, 
                            COALESCE(SUM(CASE WHEN tipo_movimiento = 'entrada' THEN cantidad ELSE 0 END), 0) -
                            COALESCE(SUM(CASE WHEN tipo_movimiento = 'salida' THEN cantidad ELSE 0 END), 0) AS stock
                    FROM movimientos
                    GROUP BY id_producto
                ) m ON i.id_producto = m.id_producto
                SET i.inv_disponibilidad = m.stock
            ");
            $stmt->execute();
        } catch (PDOException $e) {
            throw new Exception("Error al sincronizar inventario: " . $e->getMessage());
        }
    }
}
?>