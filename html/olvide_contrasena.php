<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Contraseña</title>
    <link rel="stylesheet" href="/melo8-main/Vista/css/estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include __DIR__ . '/../../header.php' ?>

    <section class="login-section py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-5 col-md-7 col-sm-9">
                    <div class="card shadow-lg border-0 rounded">
                        <div class="card-body p-5">
                            <h2 class="text-center mb-4">Recuperar Contraseña</h2>

                            <?php if (isset($_GET['mensaje'])): ?>
                                <div class="alert alert-info text-center">
                                    <?php echo htmlspecialchars($_GET['mensaje']); ?>
                                </div>
                            <?php endif; ?>

                            <form id="form-recuperar" method="post" action="../../Controlador/olvide_contrasena_api.php">
                                <div class="mb-3">
                                    <label for="correo" class="form-label">Correo Electrónico</label>
                                    <input type="email" class="form-control" name="correo" placeholder="Ingresa tu correo" required>
                                </div>
                                <div class="d-grid mb-3">
                                    <button type="submit" class="btn btn-primary">Enviar enlace de recuperación</button>
                                </div>
                            </form>

                            <div class="text-center">
                                <a href="login.php" class="text-decoration-none">Volver al inicio de sesión</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include __DIR__ . '/../../footer.php' ?>

</body>
</html>
