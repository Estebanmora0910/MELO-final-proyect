<!-- header.php -->
<?php
session_start();
$isLoggedIn = isset($_SESSION['id_usuario']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogo D.R.</title>
    <link rel="stylesheet" href="/melo8-main/Vista/css/carrito.css">
    <link rel="stylesheet" href="/melo8-main/Vista/css/header_carrito.css">
    <link rel="stylesheet" href="/melo8-main/Vista/css/stylescatalogo.css">
    <link rel="stylesheet" href="/melo8-main/Vista/css/index.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body data-logged-in="<?php echo $isLoggedIn ? 'true' : 'false'; ?>">
    <div class="header-container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="/melo8-main/index.php">
                    <img src="/melo8-main/Vista/img/logo2.png" style="width: 100px;" alt="Logo" onerror="this.src='/melo8-main/Vista/img/placeholder.png';">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarScroll" aria-controls="navbarScroll" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarScroll">
                    <ul class="navbar-nav m-auto mb-2 mb-lg-0">
                        <li class="nav-item">
                            <a class="nav-link active" href="/melo8-main/index.php">Inicio</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/melo8-main/Controlador/CatalogController.php">Catálogo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/melo8-main/Vista/html/pagos.php">Mis Pedidos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/melo8-main/Vista/html/contacto.php">Contacto</a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center">
                        <form class="container-fluid justify-content-start me-3">
                            <?php if (!$isLoggedIn): ?>
                                <a href="/melo8-main/Vista/html/login.php">
                                    <button id="btn-login" class="btn btn-outline-custom me-2" type="button">Iniciar Sesión</button>
                                </a>
                                <a href="/melo8-main/Vista/html/registrarse.php">
                                    <button id="btn-register" class="btn btn-outline-custom me-2" type="button">Registrarse</button>
                                </a>
                            <?php else: ?>
                                <a id="perfil-link" href="/melo8-main/Vista/html/miperfil.php" class="btn btn-perfil me-2">Mi Perfil</a>
                                <a href="/melo8-main/Controlador/cerrar_sesion.php" id="btn-logout" class="btn btn-danger">Cerrar sesión</a>
                            <?php endif; ?>
                        </form>
                        <div class="carrito-wrapper position-relative ms-3">
                            <span class="carrito-boton">
                                <i class="fas fa-shopping-cart fa-2x text-primary"></i>
                                <span id="contador-carrito" class="contador-carrito badge bg-danger rounded-pill">0</span>
                            </span>
                            <div id="carrito" class="carrito-container carrito-hidden">
                                <ul id="carrito-lista" class="list-group list-group-flush"></ul>
                                <p id="carrito-total" class="carrito-total text-center fw-bold text-success mt-3 mb-2">Total: $0</p>
                                <div class="d-flex justify-content-between mt-2">
                                    <button id="vaciar-carrito" class="btn btn-vaciar btn-sm">Vaciar Carrito</button>
                                    <button id="ir-a-pagar" class="btn btn-pagar btn-sm">Ir a pagar</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/melo8-main/Controlador/navbar_sesion.js"></script>
    <script src="/melo8-main/Controlador/carrito.js"></script>
</body>
</html>