<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse - Jabones DR</title>
    <link rel="stylesheet" href="/melo8-main/Vista/css/auth.css">
</head>
<body>
    <?php include __DIR__ . '/../../header.php'; ?>

    <div class="auth-content">
      <section class="registro-section py-5">
          <div class="container">
              <div class="row justify-content-center">
                  <div class="col-lg-5 col-md-7 col-sm-9">
                      <div class="card shadow-lg border-0 rounded">
                          <div class="card-body p-5">
                              <h2 class="text-center mb-4">Registrarse</h2>

                              <!-- Mostrar mensajes -->
                              <?php if (isset($_SESSION['error'])): ?>
                                  <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                      <?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                  </div>
                              <?php endif; ?>
                              <?php if (isset($_SESSION['exito'])): ?>
                                  <div class="alert alert-success alert-dismissible fade show" role="alert">
                                      <?= htmlspecialchars($_SESSION['exito']); unset($_SESSION['exito']); ?>
                                      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                                  </div>
                              <?php endif; ?>

                              <!-- Formulario -->
                              <form action="../../Controlador/registro_controlador.php" method="POST">
                                  <div class="mb-3">
                                      <label for="nombre" class="form-label">Nombre completo</label>
                                      <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Ingresa tu nombre completo" required>
                                  </div>
                                  <div class="mb-3">
                                      <label for="correo" class="form-label">Correo electrónico</label>
                                      <input type="email" name="correo" id="correo" class="form-control" placeholder="Ingresa tu correo electrónico" required>
                                  </div>
                                  <div class="mb-3">
                                      <label for="usuario" class="form-label">Nombre de usuario</label>
                                      <input type="text" name="usuario" id="usuario" class="form-control" placeholder="Ingresa tu usuario" required>
                                  </div>
                                  <div class="mb-3">
                                      <label for="contrasena" class="form-label">Contraseña</label>
                                      <input type="password" name="contrasena" id="contrasena" class="form-control" placeholder="Ingresa tu contraseña" required>
                                  </div>
                                  <div class="mb-3">
                                      <label for="confirmar_contrasena" class="form-label">Confirmar contraseña</label>
                                      <input type="password" name="confirmar_contrasena" id="confirmar_contrasena" class="form-control" placeholder="Confirma tu contraseña" required>
                                  </div>
                                  <div class="mb-3">
                                      <label for="direccion" class="form-label">Dirección</label>
                                      <input type="text" name="direccion" id="direccion" class="form-control" placeholder="Ingresa tu dirección" required>
                                  </div>
                                  <div class="mb-3">
                                      <label for="ciudad" class="form-label">Ciudad</label>
                                      <input type="text" name="ciudad" id="ciudad" class="form-control" placeholder="Ingresa tu ciudad" required>
                                  </div>
                                  <div class="mb-3">
                                      <label for="telefono" class="form-label">Teléfono</label>
                                      <input type="tel" name="telefono" id="telefono" class="form-control" placeholder="Ingresa tu teléfono" required>
                                  </div>
                                  <div class="d-grid mb-3">
                                      <button type="submit" class="btn btn-primary btn-login">Registrarse</button>
                                  </div>
                                  <div class="text-center">
                                      <p>¿Ya tienes una cuenta? <a href="login.php" class="text-decoration-none">Iniciar Sesión</a></p>
                                  </div>
                              </form>
                          </div>
                      </div>
                  </div>
              </div>
          </div>
      </section>
    </div>

    <?php include __DIR__ . '/../../footer.php' ?>
</body>
</html>