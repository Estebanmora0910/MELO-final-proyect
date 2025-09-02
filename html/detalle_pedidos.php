<?php
session_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalles de Pedidos - <?= htmlspecialchars($cliente['nombre'] ?? 'Cliente') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/melo8-main/Vista/css/admin.css">
    <style>
        .table-responsive {
            margin-top: 20px;
        }
        .btn-primary {
            background-color: #0000CD;
            border-color: #0000CD;
        }
        .btn-primary:hover {
            background-color: #1E90FF;
            border-color: #1E90FF;
        }
        .product-list {
            list-style-type: none;
            padding-left: 0;
        }
        .product-list li {
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    <header class="admin-header">
        <div class="header-left">
            <img src="/melo8-main/Vista/img/logo2.jpg" alt="Logo" class="header-logo">
            <h1 class="company-name">Productos de Aseo D.R.</h1>
        </div>
        <nav class="nav-links">
            <a href="/melo8-main/Vista/html/administrador.php">Inicio</a>
            <a href="/melo8-main/Controlador/InventoryController.php">Inventario</a>
            <a href="/melo8-main/Controlador/MovimientosController.php">Movimientos</a>
            <a href="/melo8-main/Controlador/ListaClientesController.php">Lista de Clientes</a>
            <a href="/melo8-main/Controlador/PersonasController.php">Gestión de Personas</a>
        </nav>
        <button class="logout-button" onclick="location.href='/melo8-main/logout.php'">Cerrar sesión</button>
    </header>

    <main class="main-content container mt-4">
        <div class="welcome-box text-center mb-4">
            <h2>Pedidos de <?= htmlspecialchars($cliente['nombre'] ?? 'Cliente Desconocido') ?> (ID: <?= htmlspecialchars($id_cliente) ?>)</h2>
            <p>Visualiza y gestiona los pedidos registrados para este cliente.</p>
        </div>

        <?php if (is_array($pedidos) && !isset($pedidos['error']) && !empty($pedidos)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID Pedido</th>
                            <th>Productos</th>
                            <th>Fecha</th>
                            <th>Estado</th>
                            <th>Método de Pago</th>
                            <th>Valor Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td><?= htmlspecialchars($pedido['id_pedido']) ?></td>
                                <td>
                                    <ul class="product-list">
                                        <?php foreach ($pedido['productos'] as $producto): ?>
                                            <li>
                                                <?= htmlspecialchars($producto['pro_nombre']) ?> x<?= htmlspecialchars($producto['det_cantidad']) ?> - 
                                                $<?= number_format($producto['total_producto'], 0) ?> 
                                                (<?= number_format($producto['det_precio_unitario'], 0) ?> c/u)
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </td>
                                <td><?= htmlspecialchars($pedido['fecha']) ?></td>
                                <td style="color: <?= $pedido['estado'] == 'pagado' ? '#28a745' : ($pedido['estado'] == 'pendiente' ? '#FFA500' : '#DC3545') ?>;">
                                    <?= htmlspecialchars(ucfirst($pedido['estado'])) ?>
                                </td>
                                <td><?= htmlspecialchars($pedido['metodo_pago'] ?? 'N/A') ?></td>
                                <td>$<?= number_format($pedido['valor_total'] ?? 0, 0) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-end">
                <a href="/melo8-main/Controlador/ExportarPedidosController.php?id_cliente=<?= htmlspecialchars($id_cliente) ?>" class="btn btn-primary">Exportar a CSV</a>
            </div>
        <?php else: ?>
            <p class="text-center"><?= isset($pedidos['error']) ? htmlspecialchars($pedidos['error']) : 'Este cliente no tiene pedidos registrados.' ?></p>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
