<?php
session_start()
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Persona</title>
  <link rel="stylesheet" href="/melo8-main/Vista/css/agregar_producto.css">
</head>
<body>
<main class="main-content">
  <h2>Editar Persona</h2>
  <?php if ($persona): ?>
    <form method="POST" action="/melo8-main/Controlador/PersonasController.php">
      <input type="hidden" name="id_personas" value="<?= $persona['id_personas'] ?>">

      <label for="reg_nombre">Nombre:</label>
      <input type="text" name="reg_nombre" value="<?= $persona['reg_nombre'] ?>" required>

      <label for="reg_correo">Correo:</label>
      <input type="email" name="reg_correo" value="<?= $persona['reg_correo'] ?>" required>

      <label for="reg_direccion">Dirección:</label>
      <input type="text" name="reg_direccion" value="<?= $persona['reg_direccion'] ?>" required>

      <label for="reg_ciudad">Ciudad:</label>
      <input type="text" name="reg_ciudad" value="<?= $persona['reg_ciudad'] ?>" required>

      <label for="reg_tipo">Tipo:</label>
      <input type="text" name="reg_tipo" value="<?= $persona['reg_tipo'] ?>" required>

      <label for="reg_telefono">Teléfono:</label>
      <input type="text" name="reg_telefono" value="<?= $persona['reg_telefono'] ?>" required>

      <button type="submit" name="editar_persona">Actualizar Persona</button>
    </form>
  <?php else: ?>
    <p>Error: Persona no encontrada.</p>
  <?php endif; ?>
</main>
</body>
</html>
