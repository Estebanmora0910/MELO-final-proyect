document.getElementById('pagar-carrito').addEventListener('click', () => {
  fetch('/melo8-main/Controlador/verificar_sesion.php')
    .then(res => res.text())
    .then(data => {
      if (data.trim() === '1') {
        // Usuario logueado
        window.location.href = '/melo8-main/Vista/html/pagos.php';
      } else {
        alert('Debes iniciar sesión antes de continuar con el pago.');
        window.location.href = '/melo8-main/Vista/html/login.php';
      }
    })
    .catch(err => console.error('Error al verificar sesión:', err));
});

