<?php
require_once __DIR__ . '/../../Modelo/PaymentModel.php';
?>

<?php include '../../header.php'; ?>
<link rel="stylesheet" href="/melo8-main/Vista/css/pagos.css">

<!-- Alerta flotante -->
<div id="alerta-contenedor" class="alerta-contenedor"></div>

<!-- Contenedor principal -->
<div class="payments-content">
  <div class="container">
    <h2 class="mt-4 text-center text-primary">Confirmar Compra</h2>

    <!-- Carrito actual -->
    <div id="carrito-actual" class="pedido-container mt-4 bg-light p-4 rounded shadow-sm">
      <h3 class="text-center text-success">Carrito Actual</h3>
      <div id="carrito-lista-actual" class="list-group"></div>
      <p id="carrito-total-actual" class="carrito-total text-end fw-bold mt-3">Total: $0</p>
      <button id="confirmar-pedido" class="btn btn-primary w-100 mt-3">Confirmar Pedido</button>
    </div>

    <!-- Pedidos históricos -->
    <div id="pedidos-dinamicos" class="pedido-container mt-5">
      <h3 class="text-center text-primary">Mis Pedidos Anteriores</h3>
      <p class="text-center text-muted">Cargando pedidos...</p>
    </div>
  </div>
</div>

<?php include '../../footer.php'; ?>

<script>
  // Cargar pedidos guardados
  function cargarPedidos() {
    fetch("/melo8-main/Controlador/PaymentController.php?action=cargar")
      .then(res => res.json())
      .then(data => {
        const container = document.getElementById("pedidos-dinamicos");
        container.innerHTML = "<h3 class='text-center text-primary'>Mis Pedidos Anteriores</h3>";

        if (data.error) {
          container.innerHTML += `<div class="alert alert-danger">${data.error}</div>`;
        } else if (data.length === 0) {
          container.innerHTML += `<div class="alert alert-info">No tienes pedidos guardados.</div>`;
        } else {
          data.forEach(pedido => {
            console.log('Pedido recibido:', pedido); // Depuración
            const div = document.createElement("div");
            div.className = "pedido-box mb-4 p-3 border rounded";
            let productosHtml = '<ul class="product-list">';
            pedido.productos.forEach(producto => {
              productosHtml += `<li>${producto.pro_nombre} x${producto.det_cantidad} - $${producto.total_producto.toLocaleString('es-CO')} (${producto.det_precio_unitario.toLocaleString('es-CO')} c/u)</li>`;
            });
            productosHtml += '</ul>';
            const estadoClass = pedido.estado === 'pagado' ? 'estado-pagado' : (pedido.estado === 'pendiente' ? 'estado-pendiente' : 'estado-cancelado');
            div.innerHTML = `
              <h4 class="text-center">Pedido #${pedido.id_pedido}</h4>
              <p><strong>Productos:</strong></p>
              ${productosHtml}
              <p><strong>Total:</strong> $${parseFloat(pedido.total).toLocaleString('es-CO')}</p>
              <p><strong>Fecha:</strong> ${pedido.ped_fecha_compra}</p>
              <p><strong>Estado:</strong> <span class="${estadoClass}">${pedido.estado.charAt(0).toUpperCase() + pedido.estado.slice(1)}</span></p>
              <p><strong>Método de pago:</strong> ${pedido.metodo_pago || 'Aún no elegido'}</p>
              ${pedido.estado === 'pendiente' ? `
                <hr>
                <p><strong>Seleccionar método de pago:</strong></p>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="pago_${pedido.id_pedido}" id="nequi_${pedido.id_pedido}" value="Nequi">
                  <label class="form-check-label" for="nequi_${pedido.id_pedido}">Nequi</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="pago_${pedido.id_pedido}" id="contra_${pedido.id_pedido}" value="Contraentrega">
                  <label class="form-check-label" for="contra_${pedido.id_pedido}">Contraentrega</label>
                </div>
                <hr>
                <p><strong>Total:</strong> $${parseFloat(pedido.total).toLocaleString('es-CO')}</p>
                <div class="d-flex justify-content-between">
                  <button class="btn btn-primary pagar-pedido" data-id="${pedido.id_pedido}">Pagar</button>
                  <button class="btn btn-danger eliminar-pedido" data-id="${pedido.id_pedido}">Eliminar</button>
                </div>
              ` : ''}
            `;
            container.appendChild(div);
          });

          // Asignar eventos a botones de pago
          document.querySelectorAll('.pagar-pedido').forEach(btn => {
            btn.addEventListener('click', async () => {
              const idPedido = btn.dataset.id;
              const metodoPago = document.querySelector(`input[name="pago_${idPedido}"]:checked`);
              if (!metodoPago) {
                alert('Selecciona un método de pago.');
                return;
              }
              try {
                console.log('Procesando pago para pedido:', idPedido, 'con método:', metodoPago.value); // Depuración
                const response = await fetch('/melo8-main/Controlador/PaymentController.php?action=procesar_pago', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({ id_pedido: idPedido, metodo_pago: metodoPago.value })
                });
                const data = await response.json();
                console.log('Respuesta del servidor (pago):', data); // Depuración
                if (data.success) {
                  alert('✅ Pago procesado correctamente');
                  window.location.reload();
                } else {
                  alert(`❌ Error: ${data.message}`);
                }
              } catch (error) {
                console.error('Error al procesar pago:', error);
                alert('❌ Error al procesar el pago');
              }
            });
          });

          // Asignar eventos a botones de eliminar
          document.querySelectorAll('.eliminar-pedido').forEach(btn => {
            btn.addEventListener('click', async () => {
              const idPedido = btn.dataset.id;
              console.log('Eliminando pedido:', idPedido); // Depuración
              if (!confirm('¿Estás seguro de que quieres eliminar este pedido? Esta acción restaurará el stock y no se puede deshacer.')) {
                return;
              }
              try {
                const response = await fetch('/melo8-main/Controlador/PaymentController.php?action=eliminar', {
                  method: 'POST',
                  headers: { 'Content-Type': 'application/json' },
                  body: JSON.stringify({ id_pedido: idPedido })
                });
                const data = await response.json();
                console.log('Respuesta del servidor (eliminar):', data); // Depuración
                if (data.success) {
                  alert('✅ Pedido eliminado correctamente');
                  window.location.reload();
                } else {
                  alert(`❌ Error: ${data.message}`);
                }
              } catch (error) {
                console.error('Error al eliminar pedido:', error);
                alert('❌ Error al eliminar el pedido');
              }
            });
          });
        }
      })
      .catch(error => {
        console.error('Error al cargar pedidos:', error); // Depuración
        document.getElementById("pedidos-dinamicos").innerHTML =
          `<div class="alert alert-danger">Error al cargar los pedidos.</div>`;
      });
  }

  // Cargar carrito actual desde localStorage
  function renderizarCarritoActual() {
    const carritoLista = document.getElementById('carrito-lista-actual');
    const carritoTotal = document.getElementById('carrito-total-actual');
    let carrito = [];
    try {
      const storedCarrito = localStorage.getItem('carrito');
      if (storedCarrito) {
        carrito = JSON.parse(storedCarrito);
        if (!Array.isArray(carrito)) carrito = [];
      }
    } catch (error) {
      console.error('Error al cargar carrito:', error);
    }

    carritoLista.innerHTML = '';
    let total = 0;
    if (carrito.length === 0) {
      carritoLista.innerHTML = `
        <div class="alert alert-info text-center">
          <i class="fas fa-shopping-cart fa-2x mb-2"></i><br>
          Tu carrito está vacío
        </div>
      `;
    } else {
      carrito.forEach((producto, index) => {
        const div = document.createElement('div');
        div.className = 'list-group-item';
        div.innerHTML = `
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <strong>${producto.nombre}</strong>
              <div>Cantidad: ${producto.cantidad}</div>
              <div>Precio unitario: $${parseFloat(producto.precio).toLocaleString('es-CO')}</div>
              <div>Total: $${(producto.precio * producto.cantidad).toLocaleString('es-CO')}</div>
            </div>
            <div class="d-flex gap-2">
              <button class="btn btn-sm btn-outline-secondary menos-cantidad" data-index="${index}"><i class="fas fa-minus"></i></button>
              <button class="btn btn-sm btn-outline-secondary mas-cantidad" data-index="${index}"><i class="fas fa-plus"></i></button>
              <button class="btn btn-sm btn-danger eliminar-producto" data-index="${index}"><i class="fas fa-trash"></i></button>
            </div>
          </div>
        `;
        carritoLista.appendChild(div);
        total += producto.precio * producto.cantidad;
      });
    }
    carritoTotal.textContent = `Total: $${total.toLocaleString('es-CO')}`;
    document.getElementById('confirmar-pedido').disabled = carrito.length === 0;

    // Eventos para botones
    document.querySelectorAll('.menos-cantidad').forEach(btn => {
      btn.addEventListener('click', () => {
        const index = parseInt(btn.dataset.index);
        if (carrito[index].cantidad > 1) {
          carrito[index].cantidad--;
        } else {
          carrito.splice(index, 1);
        }
        localStorage.setItem('carrito', JSON.stringify(carrito));
        renderizarCarritoActual();
      });
    });

    document.querySelectorAll('.mas-cantidad').forEach(btn => {
      btn.addEventListener('click', async () => {
        const index = parseInt(btn.dataset.index);
        try {
          const response = await fetch(`/melo8-main/Controlador/CatalogController.php?action=detalles&id_producto=${encodeURIComponent(carrito[index].id)}`);
          if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
          const data = await response.json();

          if (!data.success || carrito[index].cantidad >= data.producto.inv_disponibilidad) {
            alert(`⚠ No puedes agregar más de ${data.producto.inv_disponibilidad} unidades de este producto`);
            return;
          }
          carrito[index].cantidad++;
          localStorage.setItem('carrito', JSON.stringify(carrito));
          renderizarCarritoActual();
        } catch (error) {
          console.error('Error al verificar disponibilidad:', error);
          alert('❌ Error al aumentar la cantidad');
        }
      });
    });

    document.querySelectorAll('.eliminar-producto').forEach(btn => {
      btn.addEventListener('click', () => {
        const index = parseInt(btn.dataset.index);
        carrito.splice(index, 1);
        localStorage.setItem('carrito', JSON.stringify(carrito));
        renderizarCarritoActual();
      });
    });
  }

  // Confirmar pedido
  document.getElementById('confirmar-pedido').addEventListener('click', async () => {
    let carrito = [];
    try {
      const storedCarrito = localStorage.getItem('carrito');
      if (storedCarrito) {
        carrito = JSON.parse(storedCarrito);
        if (!Array.isArray(carrito)) carrito = [];
      }
    } catch (error) {
      console.error('Error al cargar carrito:', error);
      alert('❌ Error al procesar el carrito');
      return;
    }

    if (carrito.length === 0) {
      alert('⚠ Tu carrito está vacío');
      return;
    }

    try {
      console.log('Enviando carrito para confirmar:', carrito); // Depuración
      const response = await fetch('/melo8-main/Controlador/PaymentController.php?action=guardar', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ carrito })
      });
      const result = await response.json();
      console.log('Respuesta del servidor (confirmar):', result); // Depuración
      if (result.success) {
        alert('✅ Pedido guardado correctamente');
        localStorage.removeItem('carrito');
        renderizarCarritoActual(); // Actualizar carrito

        // Crear el HTML del nuevo pedido y añadirlo al inicio
        const container = document.getElementById("pedidos-dinamicos");
        const nuevoPedido = {
          id_pedido: result.id_pedido,
          productos: carrito.map(p => ({
            pro_nombre: p.nombre,
            det_cantidad: p.cantidad,
            det_precio_unitario: p.precio,
            total_producto: p.precio * p.cantidad
          })),
          total: carrito.reduce((sum, p) => sum + p.precio * p.cantidad, 0),
          ped_fecha_compra: new Date().toISOString().split('T')[0], // Fecha actual aproximada
          estado: 'pendiente',
          metodo_pago: 'Aún no elegido'
        };
        const div = document.createElement("div");
        div.className = "pedido-box mb-4 p-3 border rounded";
        let productosHtml = '<ul class="product-list">';
        nuevoPedido.productos.forEach(producto => {
          productosHtml += `<li>${producto.pro_nombre} x${producto.det_cantidad} - $${producto.total_producto.toLocaleString('es-CO')} (${producto.det_precio_unitario.toLocaleString('es-CO')} c/u)</li>`;
        });
        productosHtml += '</ul>';
        const estadoClass = nuevoPedido.estado === 'pagado' ? 'estado-pagado' : (nuevoPedido.estado === 'pendiente' ? 'estado-pendiente' : 'estado-cancelado');
        div.innerHTML = `
          <h4 class="text-center">Pedido #${nuevoPedido.id_pedido}</h4>
          <p><strong>Productos:</strong></p>
          ${productosHtml}
          <p><strong>Total:</strong> $${parseFloat(nuevoPedido.total).toLocaleString('es-CO')}</p>
          <p><strong>Fecha:</strong> ${nuevoPedido.ped_fecha_compra}</p>
          <p><strong>Estado:</strong> <span class="${estadoClass}">${nuevoPedido.estado.charAt(0).toUpperCase() + nuevoPedido.estado.slice(1)}</span></p>
          <p><strong>Método de pago:</strong> ${nuevoPedido.metodo_pago}</p>
          <hr>
          <p><strong>Seleccionar método de pago:</strong></p>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="pago_${nuevoPedido.id_pedido}" id="nequi_${nuevoPedido.id_pedido}" value="Nequi">
            <label class="form-check-label" for="nequi_${nuevoPedido.id_pedido}">Nequi</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="radio" name="pago_${nuevoPedido.id_pedido}" id="contra_${nuevoPedido.id_pedido}" value="Contraentrega">
            <label class="form-check-label" for="contra_${nuevoPedido.id_pedido}">Contraentrega</label>
          </div>
          <hr>
          <p><strong>Total:</strong> $${parseFloat(nuevoPedido.total).toLocaleString('es-CO')}</p>
          <div class="d-flex justify-content-between">
            <button class="btn btn-primary pagar-pedido" data-id="${nuevoPedido.id_pedido}">Pagar</button>
            <button class="btn btn-danger eliminar-pedido" data-id="${nuevoPedido.id_pedido}">Eliminar</button>
          </div>
        `;
        container.insertBefore(div, container.children[1]); // Insertar después del título

        // Asignar eventos al nuevo botón de pagar
        const pagarBtn = div.querySelector('.pagar-pedido');
        pagarBtn.addEventListener('click', async () => {
          const idPedido = pagarBtn.dataset.id;
          const metodoPago = div.querySelector(`input[name="pago_${idPedido}"]:checked`);
          if (!metodoPago) {
            alert('Selecciona un método de pago.');
            return;
          }
          try {
            const response = await fetch('/melo8-main/Controlador/PaymentController.php?action=procesar_pago', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ id_pedido: idPedido, metodo_pago: metodoPago.value })
            });
            const data = await response.json();
            if (data.success) {
              alert('✅ Pago procesado correctamente');
              window.location.reload();
            } else {
              alert(`❌ Error: ${data.message}`);
            }
          } catch (error) {
            console.error('Error al procesar pago:', error);
            alert('❌ Error al procesar el pago');
          }
        });

        // Asignar eventos al nuevo botón de eliminar
        const eliminarBtn = div.querySelector('.eliminar-pedido');
        eliminarBtn.addEventListener('click', async () => {
          const idPedido = eliminarBtn.dataset.id;
          if (!confirm('¿Estás seguro de que quieres eliminar este pedido? Esta acción restaurará el stock y no se puede deshacer.')) {
            return;
          }
          try {
            const response = await fetch('/melo8-main/Controlador/PaymentController.php?action=eliminar', {
              method: 'POST',
              headers: { 'Content-Type': 'application/json' },
              body: JSON.stringify({ id_pedido: idPedido })
            });
            const data = await response.json();
            if (data.success) {
              alert('✅ Pedido eliminado correctamente');
              window.location.reload();
            } else {
              alert(`❌ Error: ${data.message}`);
            }
          } catch (error) {
            console.error('Error al eliminar pedido:', error);
            alert('❌ Error al eliminar el pedido');
          }
        });
      } else {
        alert(`❌ Error: ${result.message}`);
      }
    } catch (error) {
      console.error('Error al guardar pedido:', error);
      alert('❌ Error al procesar el pedido');
    }
  });

  // Inicializar
  cargarPedidos();
  renderizarCarritoActual();
</script>