<?php
session_start();
require_once __DIR__ . '/../../Modelo/EditarProductoModel.php';

$producto = isset($_GET['id']) ? EditarProductoModel::obtenerProductoPorId($_GET['id']) : null;
$categorias = EditarProductoModel::obtenerCategorias();
$message = isset($_GET['message']) ? urldecode($_GET['message']) : '';
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Producto</title>
  <link rel="stylesheet" href="/melo8-main/Vista/css/editar_producto.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <main class="main-content">
    <?php if ($message): ?>
      <div id="message" class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    <div class="header-actions d-flex justify-content-center">
      <button class="btn btn-secondary" onclick="window.location.href='/melo8-main/Controlador/InventoryController.php'">Regresar</button>
    </div>
    <div class="container">
      <div class="left-column">
        <h2>Detalles del Producto</h2>
        <?php if ($producto): ?>
          <div class="product-detail"><strong>ID:</strong> <?php echo htmlspecialchars($producto['id_producto']); ?></div>
          <div class="product-detail"><strong>Producto:</strong> <?php echo htmlspecialchars($producto['pro_nombre']); ?></div>
          <div class="product-detail"><strong>Descripción:</strong> <?php echo htmlspecialchars($producto['pro_descripcion']); ?></div>
          <div class="product-detail"><strong>Precio:</strong> $<?php echo htmlspecialchars($producto['pro_valor']); ?></div>
          <div class="product-detail"><strong>Categoría:</strong> 
            <?php foreach ($categorias as $cat) { if ($cat['id_categoria'] == $producto['id_categoria']) { echo htmlspecialchars($cat['tipo_categoria']); break; } } ?>
          </div>
          <div class="product-detail"><strong>Cantidad actual:</strong> <?php echo htmlspecialchars($producto['inv_disponibilidad']); ?> (No editable directamente)</div>
          <button class="btn btn-primary" onclick="openModal('modal-editar')">Editar Detalles Básicos</button>
          <button class="btn btn-success" onclick="openModal('modal-sumar')">Sumar Cantidad</button>
          <button class="btn btn-danger" onclick="openModal('modal-restar')">Restar Cantidad</button>
        <?php else: ?>
          <p>No se encontró el producto.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Modal para editar detalles básicos -->
    <div id="modal-editar" class="modal">
      <div class="modal-content">
        <h2>Editar Producto</h2>
        <form method="POST" action="/melo8-main/Controlador/EditarProductoController.php">
          <input type="hidden" name="id_original" value="<?php echo htmlspecialchars($producto['id_producto'] ?? ''); ?>">
          <input type="hidden" name="id_inventario" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
          <!-- Campos básicos sin disponibilidad -->
          <label for="pro_nombre">Nombre:</label>
          <input type="text" name="pro_nombre" value="<?php echo htmlspecialchars($producto['pro_nombre'] ?? ''); ?>" required>
          <label for="pro_descripcion">Descripción:</label>
          <textarea name="pro_descripcion" required><?php echo htmlspecialchars($producto['pro_descripcion'] ?? ''); ?></textarea>
          <label for="pro_valor">Precio:</label>
          <input type="number" name="pro_valor" step="0.01" value="<?php echo htmlspecialchars($producto['pro_valor'] ?? ''); ?>" required>
          <label for="id_categoria">Categoría:</label>
          <select name="id_categoria" required>
            <?php foreach ($categorias as $cat): ?>
              <option value="<?php echo htmlspecialchars($cat['id_categoria']); ?>" <?php echo $cat['id_categoria'] == ($producto['id_categoria'] ?? '') ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($cat['tipo_categoria']); ?>
              </option>
            <?php endforeach; ?>
          </select>
          <button type="submit" name="editar_producto">Actualizar</button>
          <button type="button" onclick="closeModal('modal-editar')">Cancelar</button>
        </form>
      </div>
    </div>

    <!-- Modal para sumar cantidad (entrada) -->
    <div id="modal-sumar" class="modal">
      <div class="modal-content">
        <h2>Sumar Cantidad (Entrada)</h2>
        <form method="POST" action="/melo8-main/Controlador/EditarProductoController.php">
          <input type="hidden" name="ajustar_stock" value="1">
          <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto['id_producto'] ?? ''); ?>">
          <input type="hidden" name="id_inventario" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
          <input type="hidden" name="tipo" value="entrada">
          <label for="cantidad_ajuste">Cantidad a sumar:</label>
          <input type="number" name="cantidad_ajuste" min="1" required>
          <button type="submit">Sumar</button>
          <button type="button" onclick="closeModal('modal-sumar')">Cancelar</button>
        </form>
      </div>
    </div>

    <!-- Modal para restar cantidad (salida) -->
    <div id="modal-restar" class="modal">
      <div class="modal-content">
        <h2>Restar Cantidad (Salida)</h2>
        <form method="POST" action="/melo8-main/Controlador/EditarProductoController.php">
          <input type="hidden" name="ajustar_stock" value="1">
          <input type="hidden" name="id_producto" value="<?php echo htmlspecialchars($producto['id_producto'] ?? ''); ?>">
          <input type="hidden" name="id_inventario" value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>">
          <input type="hidden" name="tipo" value="salida">
          <label for="cantidad_ajuste">Cantidad a restar:</label>
          <input type="number" name="cantidad_ajuste" min="1" required>
          <button type="submit">Restar</button>
          <button type="button" onclick="closeModal('modal-restar')">Cancelar</button>
        </form>
      </div>
    </div>
  </main>

  <script>
    function openModal(modalId) { document.getElementById(modalId).style.display = 'flex'; }
    function closeModal(modalId) { document.getElementById(modalId).style.display = 'none'; }
    window.onclick = function(event) {
      const modals = document.getElementsByClassName('modal');
      for (let modal of modals) { if (event.target === modal) { modal.style.display = 'none'; } }
    };
  </script>
</body>
</html>