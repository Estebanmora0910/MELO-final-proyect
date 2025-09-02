<?php
require_once __DIR__ . '/../Modelo/EditarProductoModel.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['editar_producto'])) {
        $data = [
            'id_original' => $_POST['id_original'],
            'pro_nombre' => $_POST['pro_nombre'],
            'pro_descripcion' => $_POST['pro_descripcion'],
            'pro_valor' => $_POST['pro_valor'],
            'id_categoria' => $_POST['id_categoria']
        ];
        $id_inventario = $_POST['id_inventario'];

        try {
            EditarProductoModel::actualizarProductoCompleto($data);
            $message = urlencode("Producto actualizado con éxito.");
            header("Location: /melo8-main/Controlador/EditarProductoController.php?id=" . urlencode($id_inventario) . "&message=" . $message);
        } catch (Exception $e) {
            $error = urlencode("Error al actualizar producto: " . $e->getMessage());
            header("Location: /melo8-main/Controlador/EditarProductoController.php?id=" . urlencode($id_inventario) . "&error=" . $error);
        }
        exit;
    } elseif (isset($_POST['ajustar_stock'])) {
        $id_producto = $_POST['id_producto'];
        $tipo = $_POST['tipo'];
        $cantidad = (int)$_POST['cantidad_ajuste'];
        $detalle = "Ajuste manual: {$tipo} de {$cantidad} unidades";
        $id_inventario = $_POST['id_inventario'];

        try {
            if ($cantidad <= 0) {
                throw new Exception("La cantidad debe ser positiva.");
            }
            EditarProductoModel::ajustarDisponibilidad($id_producto, $tipo, $cantidad, $detalle);
            $message = urlencode("Stock ajustado con éxito.");
            header("Location: /melo8-main/Controlador/EditarProductoController.php?id=" . urlencode($id_inventario) . "&message=" . $message);
        } catch (Exception $e) {
            $error = urlencode("Error al ajustar stock: " . $e->getMessage());
            header("Location: /melo8-main/Controlador/EditarProductoController.php?id=" . urlencode($id_inventario) . "&error=" . $error);
        }
        exit;
    }
}

if (isset($_GET['id'])) {
    $producto = EditarProductoModel::obtenerProductoPorId($_GET['id']);
    $categorias = EditarProductoModel::obtenerCategorias();
    include __DIR__ . '/../Vista/html/editar_producto.php';
} else {
    echo "ID no proporcionado.";
}
?>