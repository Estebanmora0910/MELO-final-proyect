<?php
require_once __DIR__ . '/../../Modelo/CatalogModel.php';

// Traer productos activos con disponibilidad usando el modelo
$productos = CatalogModel::obtenerProductosDisponibles();
?>

<?php include __DIR__ . '/../../header.php'; ?>

<div class="catalog-content">
  <!-- Bienvenida con imagen de fondo -->
  <section class="welcome-banner text-center py-5 position-relative">
    <div class="container py-5 position-relative">
      <div class="text-container">
        <h1 class="display-3 fw-bold">¡Aquí descubrirás el mejor catálogo en jabones! 🌿✨</h1>
        <p class="lead fs-4">¡Sigue bajando y descubre los mejores precios!</p>
        <a href="#catalog" class="btn btn-primary btn-icon btn-lg mt-4">
          <i class="fas fa-shopping-cart me-2"></i> Explorar Catálogo
        </a>
      </div>
    </div>
  </section>

  <!-- Sección de Catálogo -->
  <section class="product-section py-5" id="catalog">
    <div class="container">
      <h2 class="text-center mb-4">Catálogo</h2>
      <p class="text-center text-muted mb-5">Somos tu mejor opción</p>
      <div class="row g-4">
        <?php if (!empty($productos)): ?>
          <?php foreach ($productos as $index => $producto): ?>
            <div class="col-lg-3 col-md-6 col-sm-12">
              <div class="card product-card h-100 shadow-lg" data-name="<?php echo htmlspecialchars($producto['id_producto']); ?>">
                <div class="product-image-container">
                  <i class="fas fa-soap product-icon"></i>
                  <img src="<?php echo ($producto['pro_nombre'] === 'Suavizante de Ropa') ? '/melo8-main/Vista/img/suavizante.png' : '/melo8-main/Vista/img/' . strtolower(str_replace(' ', '', $producto['pro_nombre'])) . '.png'; ?>" 
                      class="card-img-top product-image" 
                      alt="<?php echo htmlspecialchars($producto['pro_nombre']); ?>" 
                      data-bs-toggle="modal" 
                      data-bs-target="#productModal"
                      data-id="<?php echo htmlspecialchars($producto['id_producto']); ?>"
                      onerror="this.src='/melo8-main/Vista/img/placeholder.png';">
                </div>
                <div class="card-body text-center d-flex flex-column justify-content-between">
                  <div>
                    <h6 class="card-title"><?php echo htmlspecialchars($producto['pro_nombre']); ?></h6>
                    <p class="text-success fw-bold">$<?php echo number_format($producto['pro_valor'], 0, ',', '.'); ?></p>
                  </div>
                  <div class="button-container">
                    <?php if ($producto['inv_disponibilidad'] > 0): ?>
                      <button class="btn btn-custom-info btn-icon w-100 mt-3" onclick="document.querySelector('.product-card[data-name=\'<?php echo htmlspecialchars($producto['id_producto']); ?>\'] .product-image').click();">
                        <i class="fas fa-search-plus me-2"></i> Ver más información
                      </button>
                    <?php else: ?>
                      <p class="text-danger fw-bold no-disponible"><i class="fas fa-exclamation-circle me-2"></i> No disponible</p>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12 text-center">
            <p class="text-muted">No hay productos disponibles en este momento.</p>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- Modal para detalles del producto -->
  <div class="modal fade" id="productModal" tabindex="-1" aria-labelledby="productModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="productModalLabel"><i class="fas fa-box-open me-2"></i> Detalles del Producto</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <img id="modal-image" src="" alt="" class="img-fluid mb-3 modal-img" style="max-height: 180px; margin: auto;">
          <h5 id="modal-nombre" class="mb-2"></h5>
          <p><strong><i class="fas fa-tag me-2"></i> Categoría:</strong> <span id="modal-categoria" class="text-muted"></span></p>
          <p><strong><i class="fas fa-info-circle me-2"></i> Descripción:</strong> <span id="modal-descripcion" class="text-muted"></span></p>
          <p><strong><i class="fas fa-dollar-sign me-2"></i> Precio:</strong> $<span id="modal-precio" class="text-success fw-bold"></span></p>
          <p><strong><i class="fas fa-boxes me-2"></i> Disponibilidad:</strong> <span id="modal-disponibilidad" class="text-muted"></span> unidades</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary btn-icon" data-bs-dismiss="modal"><i class="fas fa-times me-2"></i> Cancelar</button>
          <button type="button" class="btn btn-primary btn-icon" id="modal-agregar-carrito" disabled><i class="fas fa-cart-plus me-2"></i> Agregar al carrito</button>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__ . '/../../footer.php'; ?>
<script src="/melo8-main/Controlador/scriptcatalogo.js"></script>