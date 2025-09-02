<?php
// Verificar que el usuario sea administrador
if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 1) {
    header("Location: /melo8-main/Vista/html/login.php?mensaje=Acceso denegado. Solo administradores.");
    exit();
}

// Redirigir si no se pasa inventario (indicador de que no viene del controlador)
if (!isset($inventario)) {
    header("Location: /melo8-main/Controlador/InventoryController.php");
    exit();
}

// Obtener categorías para el formulario
require_once __DIR__ . '/../../Modelo/ProductoModel.php';
$categorias = ProductoModel::obtenerCategorias();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Inventario</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/melo8-main/Vista/css/admin.css">
  <link rel="stylesheet" href="/melo8-main/Vista/css/inventario.css">
</head>
<body>
  <header class="admin-header">
    <div class="header-left">
      <img src="/melo8-main/Vista/img/logo3.png" alt="Logo" class="header-logo">
      <h1 class="company-name">Productos de Aseo D.R.</h1>
    </div>
    <nav class="nav-links">
      <a href="/melo8-main/Vista/html/administrador.php">Inicio</a>
      <a href="/melo8-main/Vista/Controlador/InventoryController.php" class="active">Inventario</a>
      <a href="/melo8-main/Controlador/MovimientosController.php">Movimientos</a>
      <a href="/melo8-main/Controlador/ListaClientesController.php">Lista de Clientes</a>
      <a href="/melo8-main/Controlador/PersonasController.php">Gestión de Personas</a>
    </nav>
    <button class="logout-button" onclick="location.href='/melo8-main/logout.php'">Cerrar sesión</button>
  </header>

  <main class="main-content container">
    <!-- Cuadro de bienvenida -->
    <div class="welcome-box">
      <h2>Inventario</h2>
      <p>Gestiona y visualiza los productos disponibles en el sistema.</p>
    </div>

    <!-- Mostrar mensajes de éxito o error -->
    <?php if (isset($_GET['mensaje'])): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
    <?php elseif (isset($error) && $error): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif (isset($_GET['error'])): ?>
      <div class="alert alert-danger"><?php echo htmlspecialchars($_GET['error']); ?></div>
    <?php endif; ?>

    <div class="filtro-wrapper d-flex align-items-center mb-3">
      <form method="GET" action="/melo8-main/Controlador/InventoryController.php" class="filtro-inventario me-3">
        <label for="categoria" class="form-label me-2">Categoría:</label>
        <select name="categoria" id="categoria" class="form-select d-inline-block w-auto">
          <option value="">-- Todas --</option>
          <?php foreach ($categorias as $cat): ?>
            <option value="<?= htmlspecialchars($cat['tipo_categoria']) ?>" <?= ($categoria ?? '') == $cat['tipo_categoria'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['tipo_categoria']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <label for="search" class="form-label me-2">Buscar:</label>
        <input type="text" name="search" id="search" class="form-control d-inline-block w-auto" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Buscar producto...">
        <button type="submit" class="btn btn-primary">Filtrar</button>
      </form>
      <form method="POST" action="/melo8-main/Controlador/InventoryController.php" class="ms-auto">
        <input type="hidden" name="action" value="sincronizar">
        <button type="submit" class="btn btn-success">Sincronizar Inventario</button>
      </form>
    </div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>ID Inventario</th>
            <th>Producto</th>
            <th>Categoría</th>
            <th>Disponibilidad</th>
            <th>Fecha Ingreso</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($inventario)): ?>
            <?php foreach ($inventario as $item): ?>
              <tr>
                <td><?= htmlspecialchars($item['id_inventario']) ?></td>
                <td><?= htmlspecialchars($item['pro_nombre']) ?></td>
                <td><?= htmlspecialchars($item['categoria']) ?></td>
                <td><?= htmlspecialchars($item['inv_disponibilidad']) ?></td>
                <td><?= htmlspecialchars($item['fecha_ingreso']) ?></td>
                <td><?= $item['activo'] == 1 ? 'Activo' : 'Inactivo' ?></td>
                <td class="acciones">
                  <a href="/melo8-main/Controlador/EditarProductoController.php?id=<?= $item['id_inventario'] ?>" class="btn btn-sm btn-primary">Editar</a>
                  <form method="POST" action="/melo8-main/Controlador/InventoryController.php" style="display:inline;">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id_inventario" value="<?= $item['id_inventario'] ?>">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro de inactivar este producto?')">Eliminar</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="7" class="text-center">No hay productos en el inventario.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <nav aria-label="Paginación">
      <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?categoria=<?= urlencode($categoria ?? '') ?>&search=<?= urlencode($search ?? '') ?>&page=<?= $page - 1 ?>">Anterior</a>
        </li>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= $page == $i ? 'active' : '' ?>">
            <a class="page-link" href="?categoria=<?= urlencode($categoria ?? '') ?>&search=<?= urlencode($search ?? '') ?>&page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
          <a class="page-link" href="?categoria=<?= urlencode($categoria ?? '') ?>&search=<?= urlencode($search ?? '') ?>&page=<?= $page + 1 ?>">Siguiente</a>
        </li>
      </ul>
    </nav>

    <!-- Botón para abrir el modal de crear producto -->
    <div class="text-end mb-3">
      <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#crearProductoModal">
        Crear Producto
      </button>
    </div>

    <!-- Modal para crear producto -->
    <div class="modal fade" id="crearProductoModal" tabindex="-1" aria-labelledby="crearProductoModalLabel" aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="crearProductoModalLabel">Crear Nuevo Producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" action="/melo8-main/Controlador/ProductoController.php">
              <div class="mb-3">
                <label for="pro_nombre" class="form-label">Nombre del producto:</label>
                <input type="text" name="pro_nombre" id="pro_nombre" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="pro_descripcion" class="form-label">Descripción:</label>
                <textarea name="pro_descripcion" id="pro_descripcion" class="form-control" required></textarea>
              </div>
              <div class="mb-3">
                <label for="pro_valor" class="form-label">Precio:</label>
                <input type="number" name="pro_valor" id="pro_valor" step="0.01" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="id_categoria" class="form-label">Categoría:</label>
                <select name="id_categoria" id="id_categoria" class="form-select" required>
                  <option value="">Selecciona una categoría</option>
                  <?php if (!empty($categorias)): ?>
                    <?php foreach ($categorias as $cat): ?>
                      <option value="<?= htmlspecialchars($cat['id_categoria']) ?>">
                        <?= htmlspecialchars($cat['tipo_categoria']) ?>
                      </option>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <option value="">No hay categorías disponibles</option>
                  <?php endif; ?>
                </select>
              </div>
              <div class="mb-3">
                <label for="cantidad_entrada" class="form-label">Cantidad inicial:</label>
                <input type="number" name="cantidad_entrada" id="cantidad_entrada" min="0" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="fecha_ingreso" class="form-label">Fecha de ingreso:</label>
                <input type="date" name="fecha_ingreso" id="fecha_ingreso" class="form-control" required>
              </div>
              <button type="submit" name="crear_producto" class="btn btn-primary">Crear Producto</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>