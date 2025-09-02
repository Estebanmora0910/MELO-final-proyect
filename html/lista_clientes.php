<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../Controlador/ListaClientesController.php';

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$perPage = 5;
$search = isset($_GET['search']) ? trim(filter_var($_GET['search'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) : '';
$city = isset($_GET['city']) ? trim(filter_var($_GET['city'], FILTER_SANITIZE_FULL_SPECIAL_CHARS)) : '';
$minOrders = isset($_GET['min_orders']) && $_GET['min_orders'] !== '' ? intval($_GET['min_orders']) : null;

$clientes = ClienteModel::obtenerClientes($search, $city, $minOrders, $page, $perPage);
$totalClientes = ClienteModel::obtenerTotalClientes($search, $city, $minOrders);
$totalPages = ceil($totalClientes / $perPage);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="/melo8-main/Vista/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <style>
        .modal-content {
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.2);
        }
        .btn-primary {
            background-color: #0000CD;
            border-color: #0000CD;
        }
        .btn-primary:hover {
            background-color: #1E90FF;
            border-color: #1E90FF;
        }
        .filter-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }
        .table-responsive {
            margin-top: 20px;
        }
        #clientesPedidosChart {
            max-width: 600px;
            margin: 20px auto;
        }
    </style>
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
            <a href="/melo8-main/Controlador/ListaClientesController.php" class="active">Lista de Clientes</a>
            <a href="/melo8-main/Controlador/PersonasController.php">Gestión de Personas</a>
        </nav>
        <button class="logout-button" onclick="location.href='/melo8-main/logout.php'">Cerrar sesión</button>
    </header>

    <main class="main-content container mt-4">
        <div class="welcome-box text-center mb-4">
            <h2 class="animate__animated animate__fadeIn">Clientes Registrados</h2>
            <p class="animate__animated animate__fadeIn">Administra y visualiza la lista de clientes registrados.</p>
        </div>

        <!-- Gráfico de pedidos por cliente -->
        <?php if (!empty($clientes) && is_array($clientes)): ?>
            <canvas id="clientesPedidosChart" width="400" height="200"></canvas>
            <script>
                const clientes = <?php echo json_encode($clientes); ?>;
                const labels = clientes.map(cliente => cliente.nombre);
                const data = clientes.map(cliente => cliente.numero_pedidos);

                const ctx = document.getElementById('clientesPedidosChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Número de Pedidos por Cliente',
                            data: data,
                            backgroundColor: 'rgba(0, 123, 255, 0.5)',
                            borderColor: 'rgba(0, 123, 255, 1)',
                            borderWidth: 1
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Número de Pedidos'
                                }
                            },
                            x: {
                                title: {
                                    display: true,
                                    text: 'Clientes'
                                }
                            }
                        },
                        plugins: {
                            title: {
                                display: true,
                                text: 'Número de Pedidos por Cliente'
                            }
                        }
                    }
                });
            </script>
        <?php endif; ?>

        <!-- Filtros Avanzados -->
        <div class="filter-section animate__animated animate__fadeInUp">
            <form method="GET" action="/melo8-main/Controlador/ListaClientesController.php" class="row g-3">
                <div class="col-md-3">
                    <label for="search" class="form-label">Buscar por Nombre/Correo/Usuario:</label>
                    <input type="text" name="search" id="search" class="form-control" value="<?= htmlspecialchars($search) ?>">
                </div>
                <div class="col-md-3">
                    <label for="city" class="form-label">Ciudad:</label>
                    <input type="text" name="city" id="city" class="form-control" value="<?= htmlspecialchars($city) ?>">
                </div>
                <div class="col-md-2">
                    <label for="min_orders" class="form-label">Mín. Pedidos:</label>
                    <input type="number" name="min_orders" id="min_orders" class="form-control" value="<?= htmlspecialchars($minOrders ?? '') ?>" min="0">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">Filtrar</button>
                </div>
            </form>
        </div>

        <?php if (!empty($clientes) && is_array($clientes)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Ciudad</th>
                            <th>Usuario</th>
                            <th>Número de Pedidos</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente): ?>
                            <tr class="animate__animated animate__fadeIn">
                                <td><?= htmlspecialchars($cliente['id_cliente']) ?></td>
                                <td><?= htmlspecialchars($cliente['nombre']) ?></td>
                                <td><?= htmlspecialchars($cliente['correo']) ?></td>
                                <td><?= htmlspecialchars($cliente['ciudad']) ?></td>
                                <td><?= htmlspecialchars($cliente['usuario']) ?></td>
                                <td><?= htmlspecialchars($cliente['numero_pedidos']) ?></td>
                                <td>
                                    <a href="/melo8-main/Controlador/DetallePedidosController.php?id_cliente=<?= htmlspecialchars($cliente['id_cliente']) ?>" class="btn btn-primary btn-sm">
                                        Ver Pedidos
                                    </a>
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
                        <a class="page-link" href="?search=<?= urlencode($search) ?>&city=<?= urlencode($city) ?>&min_orders=<?= urlencode($minOrders ?? '') ?>&page=<?= $page - 1 ?>">Anterior</a>
                    </li>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $page == $i ? 'active' : '' ?>">
                            <a class="page-link" href="?search=<?= urlencode($search) ?>&city=<?= urlencode($city) ?>&min_orders=<?= urlencode($minOrders ?? '') ?>&page=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                    <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?search=<?= urlencode($search) ?>&city=<?= urlencode($city) ?>&min_orders=<?= urlencode($minOrders ?? '') ?>&page=<?= $page + 1 ?>">Siguiente</a>
                    </li>
                </ul>
            </nav>
        <?php else: ?>
            <p class="text-center animate__animated animate__fadeIn">No hay clientes que coincidan con los filtros aplicados.</p>
        <?php endif; ?>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
?>