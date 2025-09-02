<?php
require_once __DIR__ . '/../Modelo/MovimientosModel.php';

$tipo_movimiento = $_GET['tipo_movimiento'] ?? '';
$search = $_GET['search'] ?? '';
$categoria = $_GET['categoria'] ?? '';
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10; // Ítems por página
$offset = ($page - 1) * $limit;

$movimientos = MovimientosModel::obtenerMovimientos($tipo_movimiento, $search, $categoria, $limit, $offset);
$total_items = MovimientosModel::contarMovimientos($tipo_movimiento, $search, $categoria);
$total_pages = ceil($total_items / $limit);

include __DIR__ . '/../Vista/html/movimientos.php';
?>