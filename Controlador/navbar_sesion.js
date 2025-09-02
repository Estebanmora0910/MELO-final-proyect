document.addEventListener("DOMContentLoaded", () => {
  const perfilLink = document.getElementById("perfil-link");
  const btnLogin = document.getElementById("btn-login");
  const btnRegister = document.getElementById("btn-register");
  const btnLogout = document.getElementById("btn-logout");

  // Respaldo: usar el atributo data-logged-in del <body>
  const isLoggedInFallback = document.body.dataset.loggedIn === "true";

  fetch("/melo8-main/Controlador/verificar_estado.php")
    .then(res => {
      if (!res.ok) {
        throw new Error(`HTTP error! status: ${res.status}`);
      }
      return res.text();
    })
    .then(data => {
      const isLoggedIn = data.trim() === "1";

      if (isLoggedIn) {
        // Usuario logueado: mostrar perfil y cerrar sesión
        if (perfilLink) {
          perfilLink.classList.remove("d-none");
          perfilLink.style.display = "inline-block";
        }
        if (btnLogin) btnLogin.parentElement.style.display = "none";
        if (btnRegister) btnRegister.parentElement.style.display = "none";
        if (btnLogout) {
          btnLogout.classList.remove("d-none");
          btnLogout.style.display = "inline-block";
        }
      } else {
        // No hay sesión: mostrar login y registro, ocultar perfil y cerrar sesión
        if (perfilLink) {
          perfilLink.classList.add("d-none");
          perfilLink.style.display = "none";
        }
        if (btnLogin) btnLogin.parentElement.style.display = "inline-block";
        if (btnRegister) btnRegister.parentElement.style.display = "inline-block";
        if (btnLogout) {
          btnLogout.classList.add("d-none");
          btnLogout.style.display = "none";
        }
      }
    })
    .catch(err => {
      console.error("Error verificando sesión:", err);
      // Usar el respaldo de data-logged-in si la solicitud falla
      if (isLoggedInFallback) {
        if (perfilLink) {
          perfilLink.classList.remove("d-none");
          perfilLink.style.display = "inline-block";
        }
        if (btnLogin) btnLogin.parentElement.style.display = "none";
        if (btnRegister) btnRegister.parentElement.style.display = "none";
        if (btnLogout) {
          btnLogout.classList.remove("d-none");
          btnLogout.style.display = "inline-block";
        }
      } else {
        if (perfilLink) {
          perfilLink.classList.add("d-none");
          perfilLink.style.display = "none";
        }
        if (btnLogin) btnLogin.parentElement.style.display = "inline-block";
        if (btnRegister) btnRegister.parentElement.style.display = "inline-block";
        if (btnLogout) {
          btnLogout.classList.add("d-none");
          btnLogout.style.display = "none";
        }
      }
    });
});