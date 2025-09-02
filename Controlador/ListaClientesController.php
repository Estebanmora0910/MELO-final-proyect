<?php
require_once __DIR__ . '/../Modelo/ClienteModel.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 5;
$search = isset($_GET['search']) ? trim(filter_var($_GET['search'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) : '';
$city = isset($_GET['city']) ? trim(filter_var($_GET['city'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) : '';
$minOrders = isset($_GET['min_orders']) && $_GET['min_orders'] !== '' ? intval($_GET['min_orders']) : null;

try {
    $clientes = ClienteModel::obtenerClientes($search, $city, $minOrders, $page, $perPage);
    $totalClientes = ClienteModel::obtenerTotalClientes($search, $city, $minOrders);
    $totalPages = ceil($totalClientes / $perPage);
} catch (Exception $e) {
    error_log("Error en ListaClientesController: " . $e->getMessage());
    die("Error al cargar la lista de clientes: " . $e->getMessage());
}

include __DIR__ . '/../Vista/html/lista_clientes.php';
?>