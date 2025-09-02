<?php
session_start();
require_once __DIR__ . '/../Modelo/PaymentModel.php';
require_once __DIR__ . '/../Modelo/conexion.php';

header('Content-Type: application/json');

$action = isset($_GET['action']) ? $_GET['action'] : '';

switch ($action) {
    case 'cargar':
        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(['error' => 'Debes iniciar sesión']);
            exit;
        }
        $pedidos = PaymentModel::cargarPedidos($_SESSION['id_usuario']);
        echo json_encode($pedidos);
        break;

    case 'guardar':
        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['carrito']) || !is_array($data['carrito']) || empty($data['carrito'])) {
            echo json_encode(['success' => false, 'message' => 'El carrito está vacío o es inválido']);
            exit;
        }
        $result = PaymentModel::guardarPedido($_SESSION['id_usuario'], $data['carrito']);
        echo json_encode($result);
        break;

    case 'procesar_pago':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id_pedido']) || !isset($data['metodo_pago'])) {
            echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
            exit;
        }
        $metodo_pago = in_array($data['metodo_pago'], ['Nequi', 'Contraentrega']) ? $data['metodo_pago'] : null;
        if (!$metodo_pago) {
            echo json_encode(['success' => false, 'message' => 'Método de pago inválido']);
            exit;
        }
        $result = PaymentModel::procesarPago($data['id_pedido'], $metodo_pago);
        echo json_encode($result);
        break;

    case 'eliminar':
        if (!isset($_SESSION['id_usuario'])) {
            echo json_encode(['success' => false, 'message' => 'Debes iniciar sesión']);
            exit;
        }
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['id_pedido'])) {
            echo json_encode(['success' => false, 'message' => 'ID de pedido no proporcionado']);
            exit;
        }
        $result = PaymentModel::eliminarPedido($data['id_pedido'], $_SESSION['id_usuario']);
        echo json_encode($result);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Acción no válida']);
        break;
}
?>