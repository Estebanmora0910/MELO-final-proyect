<?php
// ProductoController.php actualizado basado en el dump de la BD
require_once __DIR__ . '/../Modelo/ProductoModel.php';
require_once __DIR__ . '/../Modelo/conexion.php'; // Aseguramos que $pdo esté disponible

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear_producto'])) {
    // Generar id_producto automáticamente
    $ultimoId = ProductoModel::obtenerUltimoIdProducto();
    $nuevoIdNumero = $ultimoId + 1;
    $id_producto = sprintf("PROD%03d", $nuevoIdNumero); // Ej. PROD001, PROD002

    $producto = [
        'id_producto' => $id_producto,
        'pro_nombre' => $_POST['pro_nombre'] ?? '',
        'pro_descripcion' => $_POST['pro_descripcion'] ?? '',
        'pro_valor' => $_POST['pro_valor'] ?? 0,
        'id_categoria' => $_POST['id_categoria'] ?? ''
    ];

    $inventario = [
        'inv_disponibilidad' => $_POST['cantidad_entrada'] ?? 0, // Usamos cantidad_entrada como disponibilidad inicial
        'fecha_ingreso' => $_POST['fecha_ingreso'] ?? date('Y-m-d H:i:s')
    ];

    try {
        // Validaciones
        if (empty($producto['pro_nombre']) || empty($producto['id_categoria']) || $producto['pro_valor'] <= 0 || $inventario['inv_disponibilidad'] < 0) {
            throw new Exception("Todos los campos son obligatorios y deben ser válidos (precio > 0, cantidad >= 0).");
        }

        // Verificar si el id_producto ya existe
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM producto WHERE id_producto = ?");
        $stmt->execute([$id_producto]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("El ID de producto $id_producto ya existe.");
        }

        ProductoModel::crearProductoConInventario($producto, $inventario);
        error_log("Producto creado exitosamente: {$id_producto}");
        header('Location: /melo8-main/Controlador/InventoryController.php?mensaje=Producto agregado correctamente');
        exit;
    } catch (Exception $e) {
        error_log("Error en ProductoController: " . $e->getMessage());
        header('Location: /melo8-main/Controlador/InventoryController.php?error=' . urlencode($e->getMessage()));
        exit;
    }
}

// Manejar solicitud AJAX para detalles del producto
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'detalles' && isset($_GET['id_producto'])) {
    header('Content-Type: application/json');
    error_log("Solicitud AJAX recibida - id_producto: {$_GET['id_producto']}");
    $id_producto = $_GET['id_producto'];
    
    try {
        if (!$pdo) {
            error_log("Error: Conexión a la base de datos no disponible en ProductoController.php");
            echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
            exit;
        }
        $producto = ProductoModel::obtenerDetallesProducto($id_producto);
        if ($producto) {
            // Asumimos que la imagen está nombrada según pro_nombre en minúsculas y sin espacios
            $producto['imagen'] = '/melo8-main/Vista/img/' . strtolower(str_replace(' ', '', $producto['pro_nombre'])) . '.png';
            error_log("Detalles devueltos para id_producto: {$id_producto} - Nombre: {$producto['pro_nombre']}");
            echo json_encode(['success' => true, 'producto' => $producto]);
        } else {
            error_log("Producto no encontrado o no activo para id_producto: {$id_producto}");
            echo json_encode(['success' => false, 'message' => 'Producto no encontrado o no está activo']);
        }
    } catch (Exception $e) {
        error_log("Error al obtener detalles del producto: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error al obtener detalles del producto: ' . $e->getMessage()]);
    }
    exit;
}

// Si no es POST ni AJAX, mostrar la vista (si existe)
$categorias = ProductoModel::obtenerCategorias();
include __DIR__ . '/../Vista/html/agregar_producto.php';
?>