<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../Modelo/PerfilModel.php';

$response = ['success' => false];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST Request received: " . print_r($_POST, true));
    $field = $_POST['field'] ?? '';
    $id_usuario = $_POST['id_usuario'] ?? '';

    if (empty($field)) {
        $response['error'] = "El campo 'field' es requerido.";
        error_log($response['error']);
        echo json_encode($response);
        exit;
    }

    if (empty($id_usuario) || !isset($_SESSION['id_usuario']) || $id_usuario != $_SESSION['id_usuario']) {
        $response['error'] = "Usuario no autenticado o ID inválido.";
        error_log($response['error']);
        echo json_encode($response);
        exit;
    }

    try {
        $id_personas = PerfilModel::obtenerIdPersonas($id_usuario);
        if (!$id_personas) {
            throw new Exception("No se encontró el perfil asociado para id_usuario: $id_usuario.");
        }

        if ($field === 'contrasena') {
            $old_password = $_POST['old_password'] ?? '';
            $new_password = $_POST['new_password'] ?? '';
            $confirm_password = $_POST['confirm_password'] ?? '';

            if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
                throw new Exception("Todos los campos de contraseña son requeridos.");
            }

            if ($new_password !== $confirm_password) {
                throw new Exception("La nueva contraseña y la confirmación no coinciden.");
            }

            $current_password = PerfilModel::obtenerContrasenaActual($id_usuario);
            if (!$current_password) {
                throw new Exception("No se pudo obtener la contraseña actual.");
            }

            if (!password_verify($old_password, $current_password)) {
                throw new Exception("La contraseña anterior es incorrecta.");
            }

            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            PerfilModel::actualizarContrasena($id_usuario, $hashed);
        } else {
            $value = $_POST['value'] ?? '';
            if (empty($value)) {
                throw new Exception("El valor no puede estar vacío.");
            }
            $allowed_fields = ['reg_nombre', 'reg_telefono', 'reg_correo'];
            if (!in_array($field, $allowed_fields)) {
                throw new Exception("Campo no permitido: $field.");
            }
            $data = [$field => $value];
            PerfilModel::actualizarPerfil($id_personas, $data);
        }

        $response['success'] = true;
        error_log("Perfil actualizado correctamente para id_usuario: $id_usuario, campo: $field");
    } catch (Exception $e) {
        $response['error'] = $e->getMessage();
        error_log("Error en PerfilController: " . $e->getMessage());
    }
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (!isset($_SESSION['id_usuario'])) {
        $response['error'] = "Usuario no autenticado.";
        error_log($response['error']);
    } else {
        if (isset($_GET['action']) && $_GET['action'] === 'get_orders') {
            try {
                $orders = PerfilModel::obtenerPedidos($_SESSION['id_usuario']);
                if (isset($orders['error'])) {
                    $response['error'] = $orders['error'];
                    error_log("Error al obtener pedidos: " . $orders['error']);
                } else {
                    $response['success'] = true;
                    $response['orders'] = $orders;
                    error_log("Pedidos cargados correctamente para id_usuario: " . $_SESSION['id_usuario']);
                }
            } catch (Exception $e) {
                $response['error'] = "Error al obtener pedidos: " . $e->getMessage();
                error_log("Error en PerfilController: " . $e->getMessage());
            }
        } elseif (isset($_GET['action']) && $_GET['action'] === 'get_order_details' && isset($_GET['pedido_id'])) {
            try {
                $order_details = PerfilModel::obtenerDetallesPedido($_SESSION['id_usuario'], $_GET['pedido_id']);
                if (isset($order_details['error'])) {
                    $response['error'] = $order_details['error'];
                    error_log("Error al obtener detalles del pedido: " . $order_details['error']);
                } else {
                    $response['success'] = true;
                    $response = array_merge($response, $order_details);
                    error_log("Detalles del pedido cargados correctamente para id_pedido: " . $_GET['pedido_id']);
                }
            } catch (Exception $e) {
                $response['error'] = "Error al obtener detalles del pedido: " . $e->getMessage();
                error_log("Error en PerfilController: " . $e->getMessage());
            }
        } else {
            $perfil = PerfilModel::obtenerPerfil($_SESSION['id_usuario']);
            if (isset($perfil['error'])) {
                $response['error'] = $perfil['error'];
                error_log("Error al obtener perfil: " . $perfil['error']);
            } else {
                $response['success'] = true;
                $response['data'] = $perfil;
                error_log("Perfil cargado correctamente para id_usuario: " . $_SESSION['id_usuario']);
            }
        }
    }
}

echo json_encode($response);
exit;
?>