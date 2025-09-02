<?php
require_once __DIR__ . '/conexion.php';

class MovimientosModel {
    public static function obtenerMovimientos($tipo_movimiento = '', $search = '', $categoria = '', $limit = 10, $offset = 0) {
        global $pdo;

        $sql = "SELECT 
                    m.id_movimiento,
                    p.pro_nombre AS producto,
                    m.tipo_movimiento,
                    m.cantidad,
                    m.fecha_movimiento,
                    m.detalle
                FROM movimientos m
                INNER JOIN producto p ON m.id_producto = p.id_producto
                INNER JOIN categoria c ON p.id_categoria = c.id_categoria
                WHERE 1=1";

        $params = [];
        if (!empty($tipo_movimiento)) {
            $sql .= " AND m.tipo_movimiento = :tipo_movimiento";
            $params[':tipo_movimiento'] = $tipo_movimiento;
        }
        if (!empty($search)) {
            $sql .= " AND p.pro_nombre LIKE :search";
            $params[':search'] = "%$search%";
        }
        if (!empty($categoria)) {
            $sql .= " AND c.tipo_categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        $sql .= " ORDER BY m.fecha_movimiento DESC LIMIT :limit OFFSET :offset";
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

    public static function contarMovimientos($tipo_movimiento = '', $search = '', $categoria = '') {
        global $pdo;

        $sql = "SELECT COUNT(*) as total
                FROM movimientos m
                INNER JOIN producto p ON m.id_producto = p.id_producto
                INNER JOIN categoria c ON p.id_categoria = c.id_categoria
                WHERE 1=1";

        $params = [];
        if (!empty($tipo_movimiento)) {
            $sql .= " AND m.tipo_movimiento = :tipo_movimiento";
            $params[':tipo_movimiento'] = $tipo_movimiento;
        }
        if (!empty($search)) {
            $sql .= " AND p.pro_nombre LIKE :search";
            $params[':search'] = "%$search%";
        }
        if (!empty($categoria)) {
            $sql .= " AND c.tipo_categoria = :categoria";
            $params[':categoria'] = $categoria;
        }

        $stmt = $pdo->prepare($sql);
        foreach ($params as $param => $value) {
            $stmt->bindValue($param, $value, PDO::PARAM_STR);
        }
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    }

    public static function obtenerCategorias() {
        global $pdo;
        $sql = "SELECT tipo_categoria FROM categoria";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>