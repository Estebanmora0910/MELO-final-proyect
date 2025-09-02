<?php
require_once __DIR__ . '/conexion.php';

class CatalogModel {
    public static function obtenerProductosDisponibles() {
        global $pdo;
        $sql = "SELECT p.id_producto, p.pro_nombre, p.pro_descripcion, p.pro_valor, c.tipo_categoria AS categoria,
                       i.inv_disponibilidad
                FROM producto p
                JOIN categoria c ON p.id_categoria = c.id_categoria
                JOIN inventario i ON p.id_producto = i.id_producto
                WHERE p.activo = 1 AND i.inv_disponibilidad > 0
                ORDER BY p.pro_nombre ASC";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Nuevo método para obtener un producto por ID
    public static function obtenerProductoPorId($id_producto) {
        global $pdo;
        $sql = "SELECT p.id_producto, p.pro_nombre, p.pro_descripcion, p.pro_valor, c.tipo_categoria AS categoria,
                       i.inv_disponibilidad
                FROM producto p
                JOIN categoria c ON p.id_categoria = c.id_categoria
                JOIN inventario i ON p.id_producto = i.id_producto
                WHERE p.id_producto = :id_producto AND p.activo = 1";
        
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':id_producto', $id_producto, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}