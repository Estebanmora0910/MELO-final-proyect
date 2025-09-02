<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/melo8-main/vista/css/estilosm.css">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <link href="https://fonts.googleapis.com/css2?family=Raleway:wght@400;700&family=Open+Sans:wght@400;600&display=swap" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Merriweather:ital,opsz,wght@0,18..144,300..900;1,18..144,300..900&display=swap" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <title>Contacto</title>
</head>
<body>
    
<?php include __DIR__ . "/../../header.php" ?>
              
              <!-- Contenedor de alertas -->
              <div id="alerta-contenedor" class="alerta-contenedor"></div>
            
        </div>
        </div>
    </nav>
<div class="contact-content">
  <br><br>
  <h1>Danos tu opinión</h1>
  <br>
  <div class="containertwo">
      
      <form class="formulario" method="post" action="../../Controlador/contacto.php">
      <fieldset>
          <div class="form-row">
              <label for="nombre" class="nombre">Nombre:</label>
              <input id="nombre" name="nombre" type="text" required>
          </div>

          <div class="form-row">
              <label for="email" class="email">Escribe tu email al cual nos contactaremos:</label>
              <input id="email" name="email" type="email" required>
          </div>

          <label for="mensaje" class="mensaje">Escribe tu mensaje:</label>
          <textarea id="mensaje" name="mensaje" rows="10" cols="50" placeholder="Escribe el mensaje" required></textarea>
      </fieldset>

      <input type="submit" value="Enviar" class="boton">
  </form>


  </div>
  <section class="contact py-5">
      <div class="container py-5">
          <div class="row">
              <div class="col-lg-9 m-auto text-center">
                  <h1>Nuestra Información</h1>
              </div>
          </div>
          <div class="row">
              <div class="col-lg-12">
                  <div class="row">
                      <div class="col-lg-3 py-3">
                          <h5 class="pb-3">Políticas</h5>
                          <!-- Abrir modal al hacer click en Privacidad o Legales -->
                          <p><a href="#" class="text-white text-decoration-underline" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacidad</a></p>
                          <p><a href="#" class="text-white text-decoration-underline" data-bs-toggle="modal" data-bs-target="#privacyModal">Cookies</a></p>
                          <p><a href="#" class="text-white text-decoration-underline" data-bs-toggle="modal" data-bs-target="#privacyModal">Legales</a></p>
                      </div>
                      <div class="col-lg-3 py-3">
                          <h5 class="pb-3">Entregas</h5>
                          <p>Puntuales</p>
                          <p>Contraentrega</p>
                          <p>En tienda</p>
                      </div>
                      <div class="col-lg-3 py-3">
                          <h5 class="pb-3">Ubicación</h5>
                          <p>Bosa Brasil</p>
                          <p>88c40 Cl. 51b sur</p>
                      </div>
                      <div class="col-lg-3 py-3">
                          <h5 class="pb-3">Redes Sociales</h5>
                          <a href="#"><svg xmlns="http://www.w3.org/2000/svg" class="facebook" width="30" height="30" fill="currentColor" viewBox="0 0 16 16">
                              <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951"/>
                          </svg></a>
                          <a href="#"><svg xmlns="http://www.w3.org/2000/svg" class="instagram" width="30" height="30" fill="currentColor" viewBox="0 0 16 16">
                              <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.9 3.9 0 0 0-1.417.923A3.9 3.9 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.9 3.9 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.9 3.9 0 0 0-.923-1.417A3.9 3.9 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599s.453.546.598.92c.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.5 2.5 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.5 2.5 0 0 1-.92-.598 2.5 2.5 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233s.008-2.388.046-3.231c.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92s.546-.453.92-.598c.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92m-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217m0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334"/>
                          </svg></a>
                          <a href="#"><svg xmlns="http://www.w3.org/2000/svg" class="x" width="30" height="30" fill="currentColor" viewBox="0 0 16 16">
                              <path d="M12.6.75h2.454l-5.36 6.142L16 15.25h-4.937l-3.867-5.07-4.425 5.07H.316l5.733-6.57L0 .75h5.063l3.495 4.633L12.601.75Zm-.86 13.028h1.36L4.323 2.145H2.865z"/>
                          </svg></a>
                      </div>
                  </div>
              </div>
          </div>
          <hr>
          <p class="text-center">Copyright @ 2025</p>
          <div class="text-center mt-3">
              <button class="btn btn-link text-white" data-bs-toggle="modal" data-bs-target="#privacyModal">Privacidad y Legales</button>
          </div>
      </div>
  </section>

  <!-- Modal de Privacidad y Legales -->
  <div class="modal fade" id="privacyModal" tabindex="-1" aria-labelledby="privacyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title fw-bold" id="privacyModalLabel">Privacidad y Legales</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <h6>Política de Privacidad</h6>
          <p>
            Esta política es emitida por productos para el aseo DR, NIT 123.456.789-0, con domicilio en Bogotá, Colombia, correo contacto@miempresa.com.
          </p>

          <h6>Legales</h6>
          <p>
            Aquí colocas los avisos legales: propiedad intelectual, responsabilidades, condiciones del servicio, cookies, etc.
          </p>

          <hr>
          <p class="small text-muted">
            Nota: Si deseas eliminar todas las cookies y datos locales almacenados por este sitio, puedes usar el botón <strong>Eliminar cookies</strong> abajo.
          </p>
        </div>
        <div class="modal-footer">
          <button id="clearCookiesBtn" type="button" class="btn btn-outline-danger">Eliminar cookies</button>
          <button id="acceptPrivacyBtn" type="button" class="btn btn-primary" data-bs-dismiss="modal">Aceptar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- SCRIPTS: Bootstrap bundle + manejo de localStorage y cookies -->
  <!-- Si ya cargas bootstrap.bundle en tu header.php, elimina este script para evitar duplicados -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/js/bootstrap.bundle.min.js" integrity="sha384-ENjdO4Dr2bkBIFxQpeo0zY3U6q2Yk6b1Zl2q9Kq0M9Q5V1p6w5N5DkFQ6fQnhcG7" crossorigin="anonymous"></script>

  <script>
  (function() {
    // Mostrar modal automáticamente la primera vez (si no aceptó)
    try {
      const accepted = localStorage.getItem('privacyAccepted');
      if (!accepted) {
        const privacyModalEl = document.getElementById('privacyModal');
        const privacyModal = new bootstrap.Modal(privacyModalEl);
        setTimeout(() => privacyModal.show(), 300);
      }
    } catch (e) {
      console.warn('No se pudo leer localStorage:', e);
    }

    // Guardar aceptación
    const acceptBtn = document.getElementById('acceptPrivacyBtn');
    if (acceptBtn) {
      acceptBtn.addEventListener('click', function() {
        try {
          localStorage.setItem('privacyAccepted', '1');
        } catch (e) {
          console.warn('No se pudo escribir localStorage:', e);
        }
      });
    }

    // Función para eliminar cookies locales
    function deleteAllCookies() {
      const cookies = document.cookie.split(';');
      for (let cookie of cookies) {
        const eqPos = cookie.indexOf('=');
        const name = eqPos > -1 ? cookie.substr(0, eqPos).trim() : cookie.trim();
        // Borrar en path raíz y con dominio
        document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/';
        document.cookie = name + '=;expires=Thu, 01 Jan 1970 00:00:00 GMT;path=/;domain=' + location.hostname;
      }
      try { localStorage.clear(); } catch (e) { console.warn(e); }
      try { sessionStorage.clear(); } catch (e) { console.warn(e); }
    }

    // Evento eliminar cookies
    const clearBtn = document.getElementById('clearCookiesBtn');
    if (clearBtn) {
      clearBtn.addEventListener('click', function() {
        deleteAllCookies();
        const privacyModalEl = document.getElementById('privacyModal');
        const instance = bootstrap.Modal.getInstance(privacyModalEl);
        if (instance) instance.hide();

        // Mostrar toast o alerta de confirmación
        // Simple alert por defecto:
        alert('Se han eliminado las cookies y datos locales del sitio. Recarga la página para ver los cambios.');
      });
    }
  })();
  </script>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>