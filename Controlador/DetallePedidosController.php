<?php
require_once __DIR__ . '/../Modelo/PedidoModel.php';

// Capturar el parámetro
$id_cliente = isset($_GET['id_cliente']) ? filter_var($_GET['id_cliente'], FILTER_VALIDATE_INT) : null;

if ($id_cliente === null || $id_cliente === false) {
    error_log("id_cliente no válido o no proporcionado: " . print_r($_GET, true));
    http_response_code(400);
    die("ID de cliente no proporcionado");
}

// Obtener los detalles del modelo
$detalles = PedidoModel::obtenerDetallesPorCliente($id_cliente);

if (isset($detalles['error'])) {
    http_response_code(404);
    die($detalles['error']);
}

// Pasar las variables a la vista
$id_cliente = $detalles['id_cliente'];
$cliente = $detalles['cliente'];
$pedidos = $detalles['pedidos'];

require_once __DIR__ . '/../Vista/html/detalle_pedidos.php';
?>