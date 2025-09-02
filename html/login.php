<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/melo8-main/Vista/css/auth.css">
    <title>Iniciar Sesión - Jabones DR</title>
</head>
<body>
  <?php include __DIR__ . '/../../header.php' ?>

  <div class="auth-content">
    <section class="login-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7 col-sm-9">
                    <div class="card shadow-lg border-0 rounded">
                        <div class="card-body p-5">
                            <h2 class="text-center mb-4">Iniciar Sesión</h2>
                            <?php if (isset($_GET['mensaje']) && !empty($_GET['mensaje'])): ?>
                              <div class="alert alert-danger text-center">
                                <?php echo htmlspecialchars($_GET['mensaje']); ?>
                              </div>
                            <?php endif; ?>
                            
                            <form method="post" action="../../Controlador/login_controlador.php">
                              <div class="mb-3">
                                <label for="usuario" class="form-label">Nombre de usuario</label>
                                <input type="text" class="form-control" name="usuario" id="usuario" placeholder="Ingresa tu usuario" required>
                              </div>
                              <div class="mb-3">
                                <label for="password" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" name="password" id="password" placeholder="Ingresa tu contraseña" required>
                              </div>
                              <div class="d-grid mb-3">
                                <button type="submit" class="btn btn-primary btn-login">Ingresar</button>
                              </div>
                              <div class="text-center">
                                <a href="olvide_contrasena.php" class="text-decoration-none">¿Olvidé mi contraseña?</a>
                              </div>
                            </form>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <p>¿No tienes una cuenta? <a href="registrarse.php" class="text-decoration-none">Crear una cuenta</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>
  </div>

  <?php include __DIR__ . '/../../footer.php' ?>
</body>
</html>