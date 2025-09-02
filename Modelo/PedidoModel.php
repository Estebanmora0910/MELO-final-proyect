<?php
require_once __DIR__ . '/conexion.php';

class PedidoModel {
    public static function obtenerDetallesPorCliente($id_cliente) {
        global $pdo;

        // Validar el ID del cliente
        if (!filter_var($id_cliente, FILTER_VALIDATE_INT) || $id_cliente <= 0) {
            error_log("ID de cliente inválido: " . $id_cliente);
            return ['error' => 'ID de cliente inválido'];
        }

        try {
            // Obtener datos del cliente
            $stmt_cliente = $pdo->prepare("
                SELECT c.id_cliente, p.reg_nombre AS nombre, p.reg_correo AS correo, p.reg_ciudad AS ciudad, 
                    u.usu_nombre_usuario AS usuario, c.cli_numero_pedidos AS numero_pedidos
                FROM cliente c
                JOIN usuario u ON c.id_usuario = u.id_usuario
                JOIN personas p ON u.id_personas = p.id_personas
                WHERE c.id_cliente = :id_cliente AND u.id_rol = (SELECT id_rol FROM rol WHERE tipo_rol = 'Cliente')
            ");
            $stmt_cliente->bindValue(':id_cliente', $id_cliente, PDO::PARAM_INT);
            $stmt_cliente->execute();
            $cliente = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

            if (!$cliente) {
                error_log("Cliente no encontrado para id_cliente: " . $id_cliente);
                return ['error' => 'Cliente no encontrado'];
            }

            // Obtener pedidos del cliente
            $stmt_pedidos = $pdo->prepare("
                SELECT 
                    p.id_pedido, 
                    p.ped_fecha_compra AS fecha, 
                    p.estado, 
                    p.metodo_pago,
                    p.total AS valor_total,
                    dp.id_producto,
                    pr.pro_nombre,
                    dp.det_cantidad,
                    dp.det_precio_unitario,
                    (dp.det_cantidad * dp.det_precio_unitario) AS total_producto
                FROM pedido p
                LEFT JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido
                LEFT JOIN producto pr ON dp.id_producto = pr.id_producto
                JOIN cliente c ON p.id_usuario = c.id_usuario
                WHERE c.id_cliente = :id_cliente
                ORDER BY p.ped_fecha_compra DESC, p.id_pedido, dp.id_producto
            ");
            $stmt_pedidos->bindValue(':id_cliente', $id_cliente, PDO::PARAM_INT);
            $stmt_pedidos->execute();
            $result = $stmt_pedidos->fetchAll(PDO::FETCH_ASSOC);

            // Agrupar productos por pedido
            $pedidos = [];
            foreach ($result as $row) {
                $id_pedido = $row['id_pedido'];
                if (!isset($pedidos[$id_pedido])) {
                    $pedidos[$id_pedido] = [
                        'id_pedido' => $id_pedido,
                        'fecha' => $row['fecha'],
                        'estado' => $row['estado'],
                        'metodo_pago' => $row['metodo_pago'],
                        'valor_total' => $row['valor_total'],
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
            $pedidos = array_values($pedidos);

            if (empty($pedidos)) {
                $pedidos = ['error' => 'Este cliente no tiene pedidos registrados'];
            }

            return [
                'id_cliente' => $id_cliente,
                'cliente' => $cliente,
                'pedidos' => $pedidos
            ];
        } catch (PDOException $e) {
            error_log("Error en obtenerDetallesPorCliente: " . $e->getMessage() . " | Cliente ID: " . $id_cliente);
            return ['error' => 'Error al cargar los datos: ' . $e->getMessage()];
        }
    }
}
?>