<?php
require_once __DIR__ . '/conexion.php';
require_once __DIR__ . '/InventoryModel.php';

class PaymentModel {
    /**
     * Guarda un pedido y registra movimientos de salida (reserva).
     */
    public static function guardarPedido($id_usuario, $carrito) {
        global $pdo;
        try {
            $pdo->beginTransaction();

            $total = 0;
            foreach ($carrito as $producto) {
                $total += $producto['precio'] * $producto['cantidad'];
            }

            $stmt = $pdo->prepare("
                INSERT INTO pedido (id_usuario, ped_fecha_compra, estado, metodo_pago, total)
                VALUES (:id_usuario, CURDATE(), 'pendiente', 'Aún no elegido', :total)
            ");
            $stmt->execute(['id_usuario' => $id_usuario, 'total' => $total]);
            $id_pedido = $pdo->lastInsertId();

            foreach ($carrito as $producto) {
                $stmt_check = $pdo->prepare("
                    SELECT i.inv_disponibilidad, p.pro_valor 
                    FROM inventario i JOIN producto p ON i.id_producto = p.id_producto
                    WHERE p.id_producto = :id_producto AND p.activo = 1
                ");
                $stmt_check->execute(['id_producto' => $producto['id']]);
                $result = $stmt_check->fetch(PDO::FETCH_ASSOC);

                if (!$result || $result['inv_disponibilidad'] < $producto['cantidad']) {
                    throw new Exception("Producto {$producto['nombre']} no disponible o cantidad insuficiente");
                }
                if ($result['pro_valor'] != $producto['precio']) {
                    throw new Exception("El precio de {$producto['nombre']} ha cambiado");
                }

                $stmt_detalle = $pdo->prepare("
                    INSERT INTO detalle_pedido (id_pedido, id_producto, det_precio_unitario, det_cantidad)
                    VALUES (:id_pedido, :id_producto, :precio, :cantidad)
                ");
                $stmt_detalle->execute([
                    'id_pedido' => $id_pedido,
                    'id_producto' => $producto['id'],
                    'precio' => $producto['precio'],
                    'cantidad' => $producto['cantidad']
                ]);

                // Registrar movimiento de salida (reserva)
                $stmt_mov = $pdo->prepare("
                    INSERT INTO movimientos (id_producto, tipo_movimiento, cantidad, fecha_movimiento, detalle)
                    VALUES (:id_producto, 'salida', :cantidad, NOW(), :detalle)
                ");
                $stmt_mov->execute([
                    'id_producto' => $producto['id'],
                    'cantidad' => $producto['cantidad'],
                    'detalle' => "Reserva por pedido pendiente #{$id_pedido}"
                ]);
            }

            $pdo->commit();
            InventoryModel::sincronizarInventario(); // Sincroniza después de cambios
            return ['success' => true, 'message' => 'Pedido guardado correctamente', 'id_pedido' => $id_pedido];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Error al guardar pedido: ' . $e->getMessage()];
        }
    }

    /**
     * Carga los pedidos de un usuario autenticado.
     * @param int $id_usuario ID del usuario
     * @return array Lista de pedidos
     */
    public static function cargarPedidos($id_usuario) {
        global $pdo;
        try {
            $stmt = $pdo->prepare("
                SELECT p.id_pedido, p.ped_fecha_compra, p.estado, p.total, p.metodo_pago,
                       dp.id_producto, pr.pro_nombre, dp.det_cantidad, dp.det_precio_unitario,
                       (dp.det_cantidad * dp.det_precio_unitario) AS total_producto
                FROM pedido p
                LEFT JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                LEFT JOIN producto pr ON dp.id_producto = pr.id_producto
                WHERE p.id_usuario = :id_usuario
                ORDER BY p.ped_fecha_compra DESC, p.id_pedido, dp.id_producto
            ");
            $stmt->execute(['id_usuario' => $id_usuario]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Agrupar productos por pedido
            $pedidos = [];
            foreach ($result as $row) {
                $id_pedido = $row['id_pedido'];
                if (!isset($pedidos[$id_pedido])) {
                    $pedidos[$id_pedido] = [
                        'id_pedido' => $id_pedido,
                        'ped_fecha_compra' => $row['ped_fecha_compra'],
                        'estado' => $row['estado'],
                        'total' => $row['total'],
                        'metodo_pago' => $row['metodo_pago'],
                        'productos' => []
                    ];
                }
                if ($row['id_producto']) {
                    $pedidos[$id_pedido]['productos'][] = [
                        'pro_nombre' => $row['pro_nombre'],
                        'det_cantidad' => $row['det_cantidad'],
                        'det_precio_unitario' => $row['det_precio_unitario'],
                        'total_producto' => $row['total_producto']
                    ];
                }
            }
            return array_values($pedidos);
        } catch (PDOException $e) {
            return ['error' => 'Error al cargar pedidos: ' . $e->getMessage()];
        }
    }

    /**
     * Procesa el pago de un pedido.
     * @param int $id_pedido ID del pedido
     * @param string $metodo_pago Método de pago (Nequi o Contraentrega)
     * @return array Resultado con success y message
     */
    public static function procesarPago($id_pedido, $metodo_pago) {
        global $pdo;
        try {
            $pdo->beginTransaction();

            // Obtener detalles del pedido
            $stmt = $pdo->prepare("
                SELECT id_usuario, total 
                FROM pedido 
                WHERE id_pedido = :id_pedido
            ");
            $stmt->execute(['id_pedido' => $id_pedido]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pedido) {
                throw new Exception('Pedido no encontrado');
            }

            // Actualizar estado del pedido
            $stmt = $pdo->prepare("
                UPDATE pedido 
                SET estado = 'pagado', metodo_pago = :metodo_pago
                WHERE id_pedido = :id_pedido
            ");
            $stmt->execute([
                'metodo_pago' => $metodo_pago,
                'id_pedido' => $id_pedido
            ]);

            // Actualizar detalles de los movimientos de reserva a compra realizada
            $old_detalle = 'Reserva por pedido pendiente #' . $id_pedido;
            $new_detalle = 'Compra realizada por usuario ' . $pedido['id_usuario'];
            $stmt_update = $pdo->prepare("
                UPDATE movimientos 
                SET detalle = :new_detalle
                WHERE detalle = :old_detalle
            ");
            $stmt_update->execute([
                'new_detalle' => $new_detalle,
                'old_detalle' => $old_detalle
            ]);

            $pdo->commit();
            return ['success' => true, 'message' => 'Pago procesado correctamente'];
        } catch (PDOException $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }

    /**
     * Elimina un pedido pendiente y registra movimientos de entrada para restaurar stock.
     */
    public static function eliminarPedido($id_pedido, $id_usuario) {
        global $pdo;
        try {
            $pdo->beginTransaction();

            $stmt = $pdo->prepare("SELECT estado FROM pedido WHERE id_pedido = :id_pedido AND id_usuario = :id_usuario");
            $stmt->execute(['id_pedido' => $id_pedido, 'id_usuario' => $id_usuario]);
            $pedido = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$pedido || $pedido['estado'] !== 'pendiente') {
                throw new Exception('Solo se pueden eliminar pedidos pendientes');
            }

            $stmt = $pdo->prepare("SELECT id_producto, det_cantidad FROM detalle_pedido WHERE id_pedido = :id_pedido");
            $stmt->execute(['id_pedido' => $id_pedido]);
            $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($productos as $producto) {
                // Registrar movimiento de entrada (restaurar)
                $stmt_mov = $pdo->prepare("
                    INSERT INTO movimientos (id_producto, tipo_movimiento, cantidad, fecha_movimiento, detalle)
                    VALUES (:id_producto, 'entrada', :cantidad, NOW(), :detalle)
                ");
                $stmt_mov->execute([
                    'id_producto' => $producto['id_producto'],
                    'cantidad' => $producto['det_cantidad'],
                    'detalle' => "Restauración por eliminación de pedido #{$id_pedido}"
                ]);
            }

            $stmt = $pdo->prepare("DELETE FROM detalle_pedido WHERE id_pedido = :id_pedido");
            $stmt->execute(['id_pedido' => $id_pedido]);

            $stmt = $pdo->prepare("DELETE FROM pedido WHERE id_pedido = :id_pedido");
            $stmt->execute(['id_pedido' => $id_pedido]);

            $pdo->commit();
            InventoryModel::sincronizarInventario(); // Sincroniza después de cambios
            return ['success' => true, 'message' => 'Pedido eliminado correctamente'];
        } catch (Exception $e) {
            $pdo->rollBack();
            return ['success' => false, 'message' => 'Error al eliminar pedido: ' . $e->getMessage()];
        }
    }
}
?>