<?php
session_start();
require_once __DIR__ . '/../../Modelo/MovimientosModel.php';
$categorias = MovimientosModel::obtenerCategorias();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Movimientos</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="/melo8-main/Vista/css/admin.css">
</head>
<body>
  <header class="admin-header">
    <div class="header-left">
      <img src="/melo8-main/Vista/img/logo3.png" alt="Logo" class="header-logo">
      <h1 class="company-name">Productos de Aseo D.R.</h1>
    </div>
    <nav class="nav-links">
      <a href="/melo8-main/Vista/html/administrador.php">Inicio</a>
      <a href="/melo8-main/Controlador/InventoryController.php">Inventario</a>
      <a href="/melo8-main/Controlador/MovimientosController.php" class="active">Movimientos</a>
      <a href="/melo8-main/Controlador/ListaClientesController.php">Lista de Clientes</a>
      <a href="/melo8-main/Controlador/PersonasController.php">Gestión de Personas</a>
    </nav>
    <button class="logout-button" onclick="location.href='/melo8-main/logout.php'">Cerrar sesión</button>
  </header>

  <main class="main-content">
    <div class="welcome-box">
      <h2>Historial de Movimientos</h2>
      <p>Visualiza las entradas y salidas de productos registradas en el sistema.</p>
    </div>

    <div class="filtro-wrapper">
      <form method="GET" action="/melo8-main/Controlador/MovimientosController.php" class="filtro-inventario">
        <label for="categoria" class="form-label">Categoría:</label>
        <select name="categoria" id="categoria" class="form-select">
          <option value="">-- Todas --</option>
          <?php foreach ($categorias as $cat): ?>
            <option value="<?= htmlspecialchars($cat['tipo_categoria']) ?>" <?= ($categoria ?? '') == $cat['tipo_categoria'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cat['tipo_categoria']) ?>
            </option>
          <?php endforeach; ?>
        </select>
        <label for="tipo_movimiento" class="form-label">Tipo:</label>
        <select name="tipo_movimiento" id="tipo_movimiento" class="form-select">
          <option value="">-- Todos --</option>
          <option value="entrada" <?= ($tipo_movimiento ?? '') == 'entrada' ? 'selected' : '' ?>>Entrada</option>
          <option value="salida" <?= ($tipo_movimiento ?? '') == 'salida' ? 'selected' : '' ?>>Salida</option>
        </select>
        <label for="search" class="form-label">Buscar por producto:</label>
        <input type="text" name="search" id="search" class="form-control" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Ej. Jabón">
        <button type="submit" class="btn btn-primary">Filtrar</button>
      </form>
    </div>

    <div class="table-responsive">
      <table class="table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Producto</th>
            <th>Tipo</th>
            <th>Cantidad</th>
            <th>Fecha y Hora</th>
            <th>Detalle</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!empty($movimientos)): ?>
            <?php foreach ($movimientos as $mov): ?>
              <tr>
                <td><?= htmlspecialchars($mov['id_movimiento']) ?></td>
                <td><?= htmlspecialchars($mov['producto']) ?></td>
                <td class="tipo-<?= htmlspecialchars($mov['tipo_movimiento']) ?>">
                  <?= htmlspecialchars(ucfirst($mov['tipo_movimiento'])) ?>
                </td>
                <td><?= htmlspecialchars($mov['cantidad']) ?></td>
                <td><?= htmlspecialchars($mov['fecha_movimiento']) ?></td>
                <td><?= htmlspecialchars($mov['detalle']) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php else: ?>
            <tr><td colspan="6" class="text-center">No hay movimientos registrados.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <!-- Paginación -->
    <nav aria-label="Paginación">
      <ul class="pagination justify-content-center">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
          <a class="page-link" href="?categoria=<?= urlencode($categoria) ?>&tipo_movimiento=<?= urlencode($tipo_movimiento) ?>&search=<?= urlencode($search) ?>&page=<?= $page - 1 ?>">Anterior</a>
        </li>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
          <li class="page-item <?= $page == $i ? 'active' : '' ?>">
            <a class="page-link" href="?categoria=<?= urlencode($categoria) ?>&tipo_movimiento=<?= urlencode($tipo_movimiento) ?>&search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
          </li>
        <?php endfor; ?>
        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
          <a class="page-link" href="?categoria=<?= urlencode($categoria) ?>&tipo_movimiento=<?= urlencode($tipo_movimiento) ?>&search=<?= urlencode($search) ?>&page=<?= $page + 1 ?>">Siguiente</a>
        </li>
      </ul>
    </nav>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>