<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/InventoryModel.php'; // Añadir esta línea

class EditarProductoModel {
    public static function obtenerProductoPorId($id) {
        global $pdo;
        $sql = "SELECT p.*, i.inv_disponibilidad
                FROM producto p
                JOIN inventario i ON p.id_producto = i.id_producto
                WHERE i.id_inventario = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function obtenerCategorias() {
        global $pdo;
        $sql = "SELECT * FROM categoria";
        return $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function actualizarProductoCompleto($data) {
        global $pdo;
        try {
            $pdo->beginTransaction();

            // Actualizar campos básicos (sin disponibilidad)
            $sqlProducto = "UPDATE producto 
                            SET pro_nombre = :nombre, 
                                pro_descripcion = :descripcion, 
                                pro_valor = :valor, 
                                id_categoria = :categoria 
                            WHERE id_producto = :id_original";
            $stmtProducto = $pdo->prepare($sqlProducto);
            $stmtProducto->execute([
                ':nombre' => $data['pro_nombre'],
                ':descripcion' => $data['pro_descripcion'],
                ':valor' => $data['pro_valor'],
                ':categoria' => $data['id_categoria'],
                ':id_original' => $data['id_original']
            ]);

            $pdo->commit();
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }

    public static function ajustarDisponibilidad($id_producto, $tipo, $cantidad, $detalle) {
        global $pdo;
        try {
            $pdo->beginTransaction();

            // Validar si es salida y hay suficiente stock
            if ($tipo === 'salida') {
                $stmt_check = $pdo->prepare("SELECT inv_disponibilidad FROM inventario WHERE id_producto = :id");
                $stmt_check->execute([':id' => $id_producto]);
                $disponible = $stmt_check->fetchColumn();
                if ($disponible < $cantidad) {
                    throw new Exception("No hay suficiente stock para restar $cantidad unidades.");
                }
            }

            // Registrar movimiento
            $stmt_mov = $pdo->prepare("
                INSERT INTO movimientos (id_producto, tipo_movimiento, cantidad, fecha_movimiento, detalle)
                VALUES (:id_producto, :tipo, :cantidad, NOW(), :detalle)
            ");
            $stmt_mov->execute([
                ':id_producto' => $id_producto,
                ':tipo' => $tipo,
                ':cantidad' => $cantidad,
                ':detalle' => $detalle
            ]);

            $pdo->commit();
            InventoryModel::sincronizarInventario(); // Sincroniza después
        } catch (Exception $e) {
            $pdo->rollBack();
            throw $e;
        }
    }
}
?>