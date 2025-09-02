<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Gestión de Personas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <link rel="stylesheet" href="/melo8-main/Vista/css/admin.css">
  <link rel="stylesheet" href="/melo8-main/Vista/css/gestion.css">
</head>
<body>
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
      <a href="/melo8-main/Controlador/PersonasController.php" class="active">Gestión de Personas</a>
    </nav>
    <button class="logout-button" onclick="location.href='/melo8-main/logout.php'">Cerrar sesión</button>
  </header>

  <main class="main-content">
    <div class="welcome-box">
      <h2>Gestión de Personas</h2>
      <p>Administra los usuarios registrados en el sistema.</p>
    </div>

    <!-- Filtros: Rol, Búsqueda -->
    <div class="filtro-wrapper d-flex align-items-center mb-3">
      <form method="GET" action="/melo8-main/Controlador/PersonasController.php" class="filtro-inventario d-flex align-items-end">
        <div class="me-3">
          <label for="rol" class="form-label">Filtro por Rol:</label>
          <select name="rol" id="rol" class="form-select w-auto">
            <option value="todos" <?= ($rol === 'todos' || empty($rol)) ? 'selected' : '' ?>>Todos</option>
            <?php foreach ($roles as $r): ?>
              <option value="<?= htmlspecialchars($r['id_rol']) ?>" <?= $rol == $r['id_rol'] ? 'selected' : '' ?>><?= htmlspecialchars($r['tipo_rol']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="me-3">
          <label for="search" class="form-label">Buscar por Nombre/Correo/Usuario:</label>
          <input type="text" name="search" id="search" class="form-control w-auto" value="<?= htmlspecialchars($search ?? '') ?>">
        </div>
        <button type="submit" class="btn btn-primary">Aplicar Filtro</button>
      </form>
    </div>

    <?php if (!empty($personas)): ?>
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Correo</th>
              <th>Dirección</th>
              <th>Ciudad</th>
              <th>Teléfono</th>
              <th>Usuario</th>
              <th>Rol</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($personas as $persona): ?>
              <tr>
                <td><?= htmlspecialchars($persona['id_personas']) ?></td>
                <td><?= htmlspecialchars($persona['reg_nombre']) ?></td>
                <td><?= htmlspecialchars($persona['reg_correo']) ?></td>
                <td><?= htmlspecialchars($persona['reg_direccion']) ?></td>
                <td><?= htmlspecialchars($persona['reg_ciudad']) ?></td>
                <td><?= htmlspecialchars($persona['reg_telefono']) ?></td>
                <td><?= htmlspecialchars($persona['usu_nombre_usuario']) ?></td>
                <td><?= htmlspecialchars($persona['tipo_rol'] ?? 'No asignado') ?></td>
                <td>
                  <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editarPersonaModal<?= $persona['id_personas'] ?>">Editar Rol</button>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <nav aria-label="Paginación">
        <ul class="pagination justify-content-center">
          <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link" href="?rol=<?= urlencode($rol ?? 'todos') ?>&search=<?= urlencode($search ?? '') ?>&page=<?= $page - 1 ?>">Anterior</a>
          </li>
          <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $page == $i ? 'active' : '' ?>">
              <a class="page-link" href="?rol=<?= urlencode($rol ?? 'todos') ?>&search=<?= urlencode($search ?? '') ?>&page=<?= $i ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
          <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link" href="?rol=<?= urlencode($rol ?? 'todos') ?>&search=<?= urlencode($search ?? '') ?>&page=<?= $page + 1 ?>">Siguiente</a>
          </li>
        </ul>
      </nav>
    <?php else: ?>
      <p>No hay personas registradas que coincidan con los filtros.</p>
    <?php endif; ?>

    <!-- Modales para editar rol -->
    <?php foreach ($personas as $persona): ?>
      <div class="modal fade" id="editarPersonaModal<?= $persona['id_personas'] ?>" tabindex="-1" aria-labelledby="editarPersonaModalLabel<?= $persona['id_personas'] ?>" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editarPersonaModalLabel<?= $persona['id_personas'] ?>">Editar Rol de <?= htmlspecialchars($persona['reg_nombre']) ?></h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form method="POST" action="/melo8-main/Controlador/PersonasController.php">
                <input type="hidden" name="id_personas" value="<?= htmlspecialchars($persona['id_personas']) ?>">
                <div class="mb-3">
                  <label for="id_rol" class="form-label">Rol</label>
                  <select name="id_rol" id="id_rol" class="form-select" required>
                    <?php foreach ($roles as $rol): ?>
                      <option value="<?= htmlspecialchars($rol['id_rol']) ?>" <?= $persona['id_rol'] == $rol['id_rol'] ? 'selected' : '' ?>><?= htmlspecialchars($rol['tipo_rol']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <button type="submit" name="editar_persona" class="btn btn-primary">Guardar Cambios</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>