document.getElementById("form-recuperar").addEventListener("submit", function (e) {
  e.preventDefault();

  const formData = new FormData(this);

  fetch("../../Controlador/cambiar_contrasena.php", {
    method: "POST",
    body: formData
  })
    .then(res => res.json())
    .then(data => {
      const mensajeDiv = document.getElementById("mensaje");
      if (data.success) {
        mensajeDiv.style.color = "green";
        mensajeDiv.textContent = data.success;
      } else {
        mensajeDiv.style.color = "red";
        mensajeDiv.textContent = data.error;
      }
    })
    .catch(() => {
      document.getElementById("mensaje").textContent = "Ocurrió un error al procesar la solicitud.";
    });
});
