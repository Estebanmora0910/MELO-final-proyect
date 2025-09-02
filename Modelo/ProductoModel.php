<?php
// ProductoModel.php actualizado basado en el dump de la BD
require_once __DIR__ . '/conexion.php';

class ProductoModel {
    public static function obtenerCategorias() {
        global $pdo;
        $sql = "SELECT * FROM categoria";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Método para obtener el último número de id_producto (ej. de 'PROD001' -> 1)
    public static function obtenerUltimoIdProducto() {
        global $pdo;
        $sql = "SELECT id_producto FROM producto ORDER BY id_producto DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $ultimoId = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ultimoId) {
            // Extraer el número del id_producto (por ejemplo, 'PROD001' -> 1)
            $numero = (int) substr($ultimoId['id_producto'], 4); // Quita 'PROD' y convierte a entero
            return $numero;
        }
        return 0; // Si no hay productos, retorna 0 para que el próximo sea PROD001
    }

    // Nuevo método para obtener el último id_inventario (int secuencial)
    public static function obtenerUltimoIdInventario() {
        global $pdo;
        $sql = "SELECT id_inventario FROM inventario ORDER BY id_inventario DESC LIMIT 1";
        $stmt = $pdo->query($sql);
        $ultimoId = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($ultimoId) {
            return (int) $ultimoId['id_inventario'];
        }
        return 0; // Si no hay inventarios, empieza en 1
    }

    // Método para crear producto con inventario, ajustado a la estructura de la BD (sin inv_cantidad_entrada, etc.)
    public static function crearProductoConInventario($producto, $inventario) {
        global $pdo;
        try {
            $pdo->beginTransaction();

            // Insertar en la tabla producto (asumiendo estructura basada en vistas del dump)
            $sql = "INSERT INTO producto (id_producto, pro_nombre, pro_descripcion, pro_valor, id_categoria, activo) 
                    VALUES (:id_producto, :pro_nombre, :pro_descripcion, :pro_valor, :id_categoria, 1)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_producto' => $producto['id_producto'],
                ':pro_nombre' => $producto['pro_nombre'],
                ':pro_descripcion' => $producto['pro_descripcion'],
                ':pro_valor' => $producto['pro_valor'],
                ':id_categoria' => $producto['id_categoria']
            ]);

            // Generar id_inventario secuencial (int)
            $ultimoIdInventario = self::obtenerUltimoIdInventario();
            $id_inventario = $ultimoIdInventario + 1;

            // Insertar en la tabla inventario (ajustado a campos del dump: sin inv_cantidad_entrada, solo inv_disponibilidad)
            $sql = "INSERT INTO inventario (id_inventario, id_producto, inv_disponibilidad, fecha_ingreso) 
                    VALUES (:id_inventario, :id_producto, :inv_disponibilidad, :fecha_ingreso)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_inventario' => $id_inventario,
                ':id_producto' => $producto['id_producto'],
                ':inv_disponibilidad' => $inventario['inv_disponibilidad'],
                ':fecha_ingreso' => $inventario['fecha_ingreso']
            ]);

            // Opcional: Insertar un movimiento inicial de 'entrada' para consistencia
            $sql_mov = "INSERT INTO movimientos (id_producto, tipo_movimiento, cantidad, fecha_movimiento, detalle) 
                        VALUES (:id_producto, 'entrada', :cantidad, NOW(), 'Ingreso inicial de producto')";
            $stmt_mov = $pdo->prepare($sql_mov);
            $stmt_mov->execute([
                ':id_producto' => $producto['id_producto'],
                ':cantidad' => $inventario['inv_disponibilidad']
            ]);

            $pdo->commit();
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw new Exception("Error al crear producto e inventario: " . $e->getMessage());
        }
    }

    // Método para obtener detalles del producto (usado en AJAX)
    public static function obtenerDetallesProducto($id_producto) {
        global $pdo;
        $sql = "SELECT p.*, c.tipo_categoria 
                FROM producto p 
                JOIN categoria c ON p.id_categoria = c.id_categoria 
                WHERE p.id_producto = :id_producto AND p.activo = 1";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id_producto' => $id_producto]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>