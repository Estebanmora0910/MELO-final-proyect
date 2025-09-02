<?php
// CatalogController.php
// Controlador para el catálogo, ahora con manejo de acciones

require_once __DIR__ . '/../Modelo/CatalogModel.php';

$action = isset($_GET['action']) ? $_GET['action'] : null;

if ($action === 'detalles') {
    // Manejar solicitud de detalles de un producto específico (para el modal)
    $id_producto = isset($_GET['id_producto']) ? $_GET['id_producto'] : null;
    
    if ($id_producto) {
        // Usar el modelo para obtener el producto por ID
        $producto = CatalogModel::obtenerProductoPorId($id_producto);
        
        if ($producto) {
            header('Content-Type: application/json'); // Asegurar que la respuesta sea JSON
            echo json_encode(['success' => true, 'producto' => $producto]);
        } else {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
        }
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'ID de producto no proporcionado']);
    }
    exit; // Asegurar que la ejecución se detenga aquí
} else {
    // Cargar productos disponibles para la vista principal
    $productos = CatalogModel::obtenerProductosDisponibles();
    
    // Incluir la vista
    include __DIR__ . '/../Vista/html/catalogo.php';
}