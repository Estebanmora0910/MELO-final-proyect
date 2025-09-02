<?php
require_once __DIR__ . '/../Modelo/PedidoModel.php';

$id_cliente = filter_var($_GET['id_cliente'], FILTER_VALIDATE_INT);
if (!$id_cliente) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="error_pedidos_cliente_' . $id_cliente . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Error']);
    fputcsv($output, ['ID de cliente inválido']);
    fclose($output);
    exit;
}

$detalles = PedidoModel::obtenerDetallesPorCliente($id_cliente);

if (isset($detalles['error'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="error_pedidos_cliente_' . $id_cliente . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Error']);
    fputcsv($output, [$detalles['error']]);
    fclose($output);
    exit;
}

if (!is_array($detalles['pedidos']) || empty($detalles['pedidos'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="error_pedidos_cliente_' . $id_cliente . '.csv"');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Error']);
    fputcsv($output, ['No hay pedidos para este cliente']);
    fclose($output);
    exit;
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="pedidos_cliente_' . $id_cliente . '.csv"');

$output = fopen('php://output', 'w');
fputcsv($output, ['ID Pedido', 'Productos', 'Fecha', 'Estado', 'Método de Pago', 'Valor Total']);

foreach ($detalles['pedidos'] as $pedido) {
    if (!isset($pedido['error'])) {
        $productos_str = '';
        foreach ($pedido['productos'] as $producto) {
            $productos_str .= sprintf(
                '%s x%s - $%s (%s c/u), ',
                $producto['pro_nombre'],
                $producto['det_cantidad'],
                number_format($producto['total_producto'], 0),
                number_format($producto['det_precio_unitario'], 0)
            );
        }
        $productos_str = rtrim($productos_str, ', ');
        fputcsv($output, [
            $pedido['id_pedido'],
            $productos_str,
            $pedido['fecha'],
            $pedido['estado'],
            $pedido['metodo_pago'] ?? 'N/A',
            number_format($pedido['valor_total'] ?? 0, 0)
        ]);
    }
}

fclose($output);
exit;
?>