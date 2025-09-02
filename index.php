<?php include 'header.php'; ?>

<!doctype html>
<html lang="es">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home Page - Melo's Jabones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="../Vista/css/index.css">
  </head>
  <body>
    <div class="main-content">
      <section class="main">
        <div class="container py-5">
          <div class="row py-4 align-items-center">
            <div class="col-lg-7 pt-5 text-center text-lg-start">
              <h1 class="display-3 fw-bold">¡Descubre la calidad y frescura de cada jabón!</h1>
              <p class="lead mb-4">Explora nuestra colección de jabones naturales que cuidan tu piel y tu hogar.</p>
              <a href="/melo8-main/Controlador/CatalogController.php">
                <button class="btn1 mt-3">Acceder al catálogo</button>
              </a>
            </div>
            <div class="col-lg-5 d-none d-lg-block">
              <img src="Vista/img/jabonreym.png" class="img-fluid main-img" alt="Jabón destacado">
            </div>
          </div>
        </div>
      </section>

      <section class="new bg-light py-5">
        <div class="container py-5">
          <div class="row pt-5 text-center">
            <div class="col-lg-12">
              <h2 class="text-primary fw-bold mb-4">¿Por qué elegirnos?</h2>
            </div>
            <div class="col-lg-4">
              <img src="Vista/img/natural.png" class="feature-img mb-3" alt="imagen natural">
              <h6 class="text-success">100% Naturales</h6>
              <p class="text-muted">Ingredientes puros que respetan tu piel y el medio ambiente.</p>
            </div>
            <div class="col-lg-4">
              <img src="Vista/img/economico.png" class="feature-img mb-3" alt="imagen economia">
              <h6 class="text-success">Precios Asequibles</h6>
              <p class="text-muted">Calidad premium a un costo que se adapta a tu bolsillo.</p>
            </div>
            <div class="col-lg-4">
              <img src="Vista/img/calidad.png" class="feature-img mb-3" alt="imagen calidad">
              <h6 class="text-success">Máxima Calidad</h6>
              <p class="text-muted">Productos elaborados con estándares de excelencia.</p>
            </div>
          </div>
        </div>
      </section>

      <section class="product py-5">
        <div class="container py-5">
          <div class="row py-5 text-center">
            <div class="col-12">
              <h1 class="display-4 fw-bold text-primary">Productos Destacados</h1>
              <h6 class="text-info">Somos tu mejor opción en cuidado y limpieza</h6>
            </div>
          </div>
          <div class="row g-4">
            <div class="col-lg-3 col-md-6 text-center">
              <div class="card border-0 h-100">
                <div class="card-body p-4">
                  <img src="Vista/img/blanqueador.png" class="product-img mb-3" alt="blanqueador">
                  <h6 class="card-title">Blanqueador</h6>
                  <a href="/melo8-main/Controlador/CatalogController.php" class="btn btn-outline-primary btn-sm">Ver más</a>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
              <div class="card border-0 h-100">
                <div class="card-body p-4">
                  <img src="Vista/img/desengrasante.png" class="product-img mb-3" alt="desengrasante">
                  <h6 class="card-title">Desengrasante</h6>
                  <a href="/melo8-main/Controlador/CatalogController.php" class="btn btn-outline-primary btn-sm">Ver más</a>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
              <div class="card border-0 h-100">
                <div class="card-body p-4">
                  <img src="Vista/img/lavaloza.png" class="product-img mb-3" alt="lavaloza">
                  <h6 class="card-title">Lava Loza</h6>
                  <a href="/melo8-main/Controlador/CatalogController.php" class="btn btn-outline-primary btn-sm">Ver más</a>
                </div>
              </div>
            </div>
            <div class="col-lg-3 col-md-6 text-center">
              <div class="card border-0 h-100">
                <div class="card-body p-4">
                  <img src="Vista/img/limpiavidrios.png" class="product-img mb-3" alt="limpiavidrios">
                  <h6 class="card-title">Limpiavidrios</h6>
                  <a href="/melo8-main/Controlador/CatalogController.php" class="btn btn-outline-primary btn-sm">Ver más</a>
                </div>
              </div>
            </div>
          </div>
          <div class="row py-4">
            <div class="col-lg-6 mx-auto text-center">
              <a href="/melo8-main/Controlador/CatalogController.php">
                <button class="btn1">Explora todo el catálogo</button>
              </a>
            </div>
          </div>
        </div>
      </section>

      <section class="about bg-light py-5">
        <div class="container py-5">
          <div class="row py-5 text-center">
            <div class="col-12">
              <h1 class="display-5 fw-bold text-primary">¿Quiénes somos?</h1>
              <h6 class="text-info">La mejor empresa en venta de jabones en Colombia</h6>
            </div>
          </div>
          <div class="row g-4">
            <div class="col-lg-4">
              <img src="Vista/img/bosa.jpg" class="about-img mb-3 shadow" alt="bosa">
              <h5 class="text-primary">Ubicados en Bosa</h5>
              <p class="text-muted">Estamos en Bosa Brasil, un punto estratégico para atenderte rápido y eficiente. Nuestra ubicación optimiza la distribución.</p>
            </div>
            <div class="col-lg-4">
              <img src="Vista/img/personal.jpg" class="about-img mb-3 shadow" alt="personal">
              <h5 class="text-primary">Mejor Personal</h5>
              <p class="text-muted">Equipo experto en jabones, comprometido con calidad e innovación para productos seguros y efectivos.</p>
            </div>
            <div class="col-lg-4">
              <img src="Vista/img/domiciliario.jpg" class="about-img mb-3 shadow" alt="domicilio">
              <h5 class="text-primary">Envío a Domicilio</h5>
              <p class="text-muted">Recibe tus jabones en casa con rapidez y seguridad, pensados en tu comodidad.</p>
            </div>
          </div>
        </div>
      </section>

      <section class="jabon py-5">
        <div class="container text-white py-5">
          <div class="row py-5 align-items-center">
            <div class="col-lg-6">
              <h1 class="display-4 fw-bold py-3">Jabones naturales únicos</h1>
              <h6 class="text-info">Cuidan tu piel, limpian tu hogar y protegen el planeta</h6>
              <p class="lead mb-4">Más de 10 años de experiencia en jabones naturales.</p>
              <a href="/melo8-main/Controlador/CatalogController.php">
                <button class="btn1 mt-3">Descubre las mejores ofertas</button>
              </a>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
              <img src="Vista/img/jabonreym.png" class="img-fluid main-img" alt="Jabón natural">
            </div>
          </div>
        </div>
      </section>
    </div>

    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>