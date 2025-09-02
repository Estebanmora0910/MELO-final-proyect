<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Agregar Producto</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="/melo8-main/Vista/css/agregar_producto.css">
</head>
<body>

  <!-- Encabezado -->
  <header class="admin-header">
    <div class="header-left">
      <img src="/melo8-main/Vista/img/logo3.png" alt="Logo" class="header-logo">
      <h1 class="company-name">Productos de Aseo D.R.</h1>
    </div>
    <nav class="nav-links">
      <a href="/melo8-main/Vista/html/administrador.php">Inicio</a>
      <a href="/melo8-main/Controlador/InventoryController.php" class="active">Inventario</a>
      <a href="/melo8-main/Controlador/MovimientosController.php">Movimientos</a>
      <a href="/melo8-main/Controlador/ListaClientesController.php">Lista de Clientes</a>
      <a href="/melo8-main/Controlador/PersonasController.php">Gestión de Personas</a>
    </nav>
    <button class="logout-button" onclick="location.href='/melo8-main/logout.php'">Cerrar sesión</button>
  </header>

  <main class="main-content container">
    <h2>Agregar Nuevo Producto</h2>
    <button type="button" class="btn btn-success btn-add-product" data-bs-toggle="modal" data-bs-target="#addProductModal">
      Agregar Producto
    </button>

    <!-- Modal -->
    <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="addProductModalLabel">Agregar Nuevo Producto</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form method="POST" action="/melo8-main/Controlador/ProductoController.php">
              <div class="mb-3">
                <label for="id_producto" class="form-label">ID del Producto:</label>
                <input type="text" name="id_producto" id="id_producto" class="form-control" required placeholder="Ej. PRO001">
              </div>
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
                <label for="inv_cantidad_entrada" class="form-label">Cantidad de entrada:</label>
                <input type="number" name="inv_cantidad_entrada" id="inv_cantidad_entrada" min="0" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="inv_disponibilidad" class="form-label">Disponibilidad:</label>
                <input type="number" name="inv_disponibilidad" id="inv_disponibilidad" min="0" class="form-control" required>
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