<?php
require_once __DIR__ . '/../../Modelo/conexion.php';

$token = $_GET['token'] ?? '';

if (empty($token)) {
    die("Token inválido.");
}

// Verificar que el token exista y no esté expirado
$stmt = $pdo->prepare("SELECT id_usuario FROM reset_password WHERE token = ? AND expiracion >= NOW()");
$stmt->execute([$token]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    die("Token inválido o expirado.");
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <link rel="stylesheet" href="/melo8-main/Vista/css/estilos2.css">
</head>
<body>

    

    <section class="login-section">
        <div class="container">
            <h2>Restablecer Contraseña</h2>

            <?php if (isset($_GET['mensaje'])): ?>
                <div class="alert alert-info"><?php echo htmlspecialchars($_GET['mensaje']); ?></div>
            <?php endif; ?>

            <form method="post" action="../../Controlador/restablecer_contrasena_api.php">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                
                <label for="password">Nueva contraseña:</label>
                <input type="password" name="password" id="password" placeholder="Ingresa nueva contraseña" required>

                <label for="password_confirm">Confirmar contraseña:</label>
                <input type="password" name="password_confirm" id="password_confirm" placeholder="Confirma tu contraseña" required>

                <button type="submit">Actualizar contraseña</button>
            </form>
        </div>
    </section>
</body>
</html>
