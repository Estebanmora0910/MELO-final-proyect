<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../Modelo/InventoryModel.php';

session_start();

// Verificar si el usuario es administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header("Location: /melo8-main/Vista/html/login.php?error=Acceso denegado. Solo administradores.");
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? ''; // Obtener action de POST o GET

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'delete') {
    global $pdo;
    $id_inventario = $_POST['id_inventario'] ?? '';

    try {
        $pdo->beginTransaction();

        // Obtener el id_producto asociado al id_inventario
        $stmt = $pdo->prepare("SELECT id_producto FROM inventario WHERE id_inventario = ?");
        $stmt->execute([$id_inventario]);
        $producto = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$producto) {
            throw new Exception("Producto no encontrado en el inventario.");
        }

        $id_producto = $producto['id_producto'];

        // Marcar el producto como inactivo
        $stmt_producto = $pdo->prepare("UPDATE producto SET activo = 0 WHERE id_producto = ?");
        $stmt_producto->execute([$id_producto]);

        $pdo->commit();
        header("Location: /melo8-main/Controlador/InventoryController.php?mensaje=Producto inactivado correctamente");
    } catch (Exception $e) {
        $pdo->rollBack();
        error_log("Error al inactivar producto: " . $e->getMessage());
        header("Location: /melo8-main/Controlador/InventoryController.php?error=" . urlencode("Error: " . $e->getMessage()));
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'sincronizar') {
    try {
        InventoryModel::sincronizarInventario();
        header("Location: /melo8-main/Controlador/InventoryController.php?mensaje=Inventario sincronizado correctamente");
    } catch (Exception $e) {
        header("Location: /melo8-main/Controlador/InventoryController.php?error=" . urlencode("Error al sincronizar inventario: " . $e->getMessage()));
    }
    exit;
}

// Parámetros de filtrado y paginación
$categoria = $_GET['categoria'] ?? '';
$search = $_GET['search'] ?? '';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Productos por página
$offset = ($page - 1) * $limit;

// Obtener productos del inventario
$inventario = InventoryModel::obtenerInventarioFiltrado($categoria, $search, $limit, $offset);

// Calcular el número total de páginas
$total_productos = InventoryModel::contarInventarioFiltrado($categoria, $search);
$total_pages = ceil($total_productos / $limit);

// Mensajes de éxito o error
$mensaje = $_GET['mensaje'] ?? '';
$error = $_GET['error'] ?? '';

// Incluir la vista
include __DIR__ . '/../Vista/html/inventario.php';
?>