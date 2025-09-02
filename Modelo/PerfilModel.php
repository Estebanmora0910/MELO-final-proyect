<?php
require_once __DIR__ . '/conexion.php';

class PerfilModel {
    public static function obtenerPerfil($usuario_id) {
        global $pdo;
        try {
            if (!$usuario_id) {
                error_log("Error: id_usuario está vacío en obtenerPerfil.");
                return ["error" => "ID de usuario no proporcionado."];
            }
            $sql = "SELECT id_personas FROM usuario WHERE id_usuario = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario_id]);
            $row = $stmt->fetch();

            if (!$row) {
                error_log("No se encontró id_personas para id_usuario: $usuario_id");
                return ["error" => "No se encontró el perfil asociado"];
            }

            $id_personas = $row['id_personas'];

            $sql = "SELECT reg_nombre, reg_correo, reg_telefono, reg_nombre_usuario 
                    FROM personas WHERE id_personas = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_personas]);
            $result = $stmt->fetch();
            if (!$result) {
                error_log("No se encontraron datos en personas para id_personas: $id_personas");
                return ["error" => "No se encontraron datos del perfil"];
            }
            error_log("Perfil obtenido para id_usuario: $usuario_id");
            return $result;
        } catch (PDOException $e) {
            error_log("Error al obtener perfil: " . $e->getMessage());
            return ["error" => "Error al obtener perfil: " . $e->getMessage()];
        }
    }

    public static function obtenerIdPersonas($usuario_id) {
        global $pdo;
        try {
            if (!$usuario_id) {
                error_log("Error: id_usuario está vacío en obtenerIdPersonas.");
                return null;
            }
            $sql = "SELECT id_personas FROM usuario WHERE id_usuario = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario_id]);
            $row = $stmt->fetch();
            if (!$row) {
                error_log("No se encontró id_personas para id_usuario: $usuario_id");
                return null;
            }
            error_log("id_personas obtenido: " . $row['id_personas'] . " para id_usuario: $usuario_id");
            return $row['id_personas'];
        } catch (PDOException $e) {
            error_log("Error al obtener id_personas: " . $e->getMessage());
            return null;
        }
    }

    public static function obtenerContrasenaActual($id_usuario) {
        global $pdo;
        try {
            if (!$id_usuario) {
                error_log("Error: id_usuario está vacío en obtenerContrasenaActual.");
                return null;
            }
            $sql = "SELECT usu_contrasena FROM usuario WHERE id_usuario = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_usuario]);
            $row = $stmt->fetch();
            if (!$row) {
                error_log("No se encontró contraseña para id_usuario: $id_usuario");
                return null;
            }
            error_log("Contraseña obtenida para id_usuario: $id_usuario");
            return $row['usu_contrasena'];
        } catch (PDOException $e) {
            error_log("Error al obtener contraseña actual: " . $e->getMessage());
            return null;
        }
    }

    public static function actualizarPerfil($id_personas, $datos) {
        global $pdo;
        try {
            if (!$id_personas || empty($datos)) {
                error_log("Error: id_personas o datos vacíos en actualizarPerfil.");
                throw new Exception("ID de persona o datos no proporcionados.");
            }
            $sets = [];
            $params = [];
            foreach ($datos as $key => $val) {
                $sets[] = "$key = :$key";
                $params[":$key"] = $val;
            }
            if (empty($sets)) {
                error_log("No se proporcionaron datos para actualizar en id_personas: $id_personas");
                throw new Exception("No se proporcionaron datos para actualizar.");
            }

            $sql = "UPDATE personas SET " . implode(', ', $sets) . " WHERE id_personas = :id";
            $params[':id'] = $id_personas;
            error_log("SQL Query: $sql | Params: " . print_r($params, true));
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            error_log("Perfil actualizado para id_personas: $id_personas");
        } catch (PDOException $e) {
            error_log("Error al actualizar perfil: " . $e->getMessage() . " | Query: $sql | Params: " . print_r($params, true));
            throw new Exception("Error al actualizar perfil: " . $e->getMessage());
        }
    }

    public static function actualizarContrasena($id_usuario, $contrasena) {
        global $pdo;
        try {
            if (!$id_usuario || !$contrasena) {
                error_log("Error: id_usuario o contraseña vacíos en actualizarContrasena.");
                throw new Exception("ID de usuario o contraseña no proporcionados.");
            }
            $sql = "UPDATE usuario SET usu_contrasena = :contrasena WHERE id_usuario = :id";
            $stmt = $pdo->prepare($sql);
            $params = [':contrasena' => $contrasena, ':id' => $id_usuario];
            error_log("SQL Query: $sql | Params: " . print_r($params, true));
            $stmt->execute($params);
            error_log("Contraseña actualizada para id_usuario: $id_usuario");
        } catch (PDOException $e) {
            error_log("Error al actualizar contraseña: " . $e->getMessage());
            throw new Exception("Error al actualizar contraseña: " . $e->getMessage());
        }
    }

    public static function obtenerPedidos($usuario_id) {
        global $pdo;
        try {
            if (!$usuario_id) {
                error_log("Error: id_usuario está vacío en obtenerPedidos.");
                return ["error" => "ID de usuario no proporcionado."];
            }
            $sql = "SELECT id_pedido, ped_fecha, ped_valor_total FROM pedido WHERE id_usuario = ? ORDER BY ped_fecha DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario_id]);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Pedidos obtenidos para id_usuario: $usuario_id, count: " . count($orders));
            return $orders;
        } catch (PDOException $e) {
            error_log("Error al obtener pedidos: " . $e->getMessage());
            return ["error" => "Error al obtener pedidos: " . $e->getMessage()];
        }
    }

    public static function obtenerDetallesPedido($usuario_id, $pedido_id) {
        global $pdo;
        try {
            if (!$usuario_id || !$pedido_id) {
                error_log("Error: id_usuario o id_pedido están vacíos en obtenerDetallesPedido.");
                return ["error" => "ID de usuario o pedido no proporcionado."];
            }
            $sql = "SELECT p.id_pedido, p.ped_fecha, p.ped_valor_total, dp.pro_nombre, dp.det_cantidad, dp.det_precio_unitario 
                    FROM pedido p 
                    JOIN detalle_pedido dp ON p.id_pedido = dp.id_pedido 
                    WHERE p.id_usuario = ? AND p.id_pedido = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario_id, $pedido_id]);
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$result) {
                error_log("No se encontraron detalles para id_pedido: $pedido_id, id_usuario: $usuario_id");
                return ["error" => "No se encontraron detalles del pedido."];
            }
            $order = $result[0];
            $detalles = array_map(function($row) {
                return [
                    'pro_nombre' => $row['pro_nombre'],
                    'det_cantidad' => $row['det_cantidad'],
                    'det_precio_unitario' => $row['det_precio_unitario']
                ];
            }, $result);
            error_log("Detalles del pedido obtenidos para id_pedido: $pedido_id, id_usuario: $usuario_id");
            return [
                'id_pedido' => $order['id_pedido'],
                'ped_fecha' => $order['ped_fecha'],
                'ped_valor_total' => $order['ped_valor_total'],
                'detalles' => $detalles
            ];
        } catch (PDOException $e) {
            error_log("Error al obtener detalles del pedido: " . $e->getMessage());
            return ["error" => "Error al obtener detalles del pedido: " . $e->getMessage()];
        }
    }
}
?>