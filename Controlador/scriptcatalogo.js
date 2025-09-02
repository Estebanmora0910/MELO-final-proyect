document.addEventListener('DOMContentLoaded', function () {
    const productModal = document.getElementById('productModal');

    if (productModal) {
        let currentProducto = null;

        productModal.addEventListener('show.bs.modal', function (event) {
            const triggerElement = event.relatedTarget;
            const idProducto = triggerElement.getAttribute('data-id');
            const imageSrc = triggerElement.src;

            // Limpiar campos
            document.getElementById('modal-nombre').textContent = '';
            document.getElementById('modal-categoria').textContent = '';
            document.getElementById('modal-descripcion').textContent = '';
            document.getElementById('modal-precio').textContent = '';
            document.getElementById('modal-disponibilidad').textContent = '';
            document.getElementById('modal-image').src = imageSrc || '/melo8-main/Vista/img/placeholder.png';
            const agregarBtn = document.getElementById('modal-agregar-carrito');
            agregarBtn.disabled = true;
            agregarBtn.setAttribute('tabindex', '-1'); // Evitar enfoque automático

            if (idProducto) {
                console.log('Solicitando detalles para ID:', idProducto);
                fetch(`/melo8-main/Controlador/CatalogController.php?action=detalles&id_producto=${encodeURIComponent(idProducto)}`, {
                    method: 'GET',
                    headers: { 'Accept': 'application/json' }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor: ' + response.status);
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('Datos recibidos:', data);
                        if (data.success) {
                            const producto = data.producto;
                            currentProducto = producto;
                            document.getElementById('modal-nombre').textContent = producto.pro_nombre;
                            document.getElementById('modal-categoria').textContent = producto.categoria || 'No disponible';
                            document.getElementById('modal-descripcion').textContent = producto.pro_descripcion || 'No disponible';
                            document.getElementById('modal-precio').textContent = Number(producto.pro_valor).toLocaleString('es-CO');
                            document.getElementById('modal-disponibilidad').textContent = producto.inv_disponibilidad;
                            agregarBtn.disabled = (producto.inv_disponibilidad <= 0);
                            agregarBtn.onclick = () => {
                                if (window.agregarAlCarrito) {
                                    window.agregarAlCarrito(producto.id_producto, producto.pro_nombre, parseFloat(producto.pro_valor));
                                    bootstrap.Modal.getInstance(productModal).hide();
                                } else {
                                    console.error('Función agregarAlCarrito no está definida');
                                    alert('❌ Error: No se puede agregar al carrito');
                                }
                            };
                        } else {
                            alert(data.message || 'Error al cargar detalles');
                            agregarBtn.disabled = true;
                        }
                    })
                    .catch(error => {
                        console.error('Error AJAX:', error);
                        alert('No se pudieron cargar los detalles del producto.');
                        agregarBtn.disabled = true;
                    });
            } else {
                alert('ID de producto no encontrado.');
                agregarBtn.disabled = true;
            }
        });

        productModal.addEventListener('shown.bs.modal', function () {
            setTimeout(() => {
                const modalTitle = productModal.querySelector('.modal-title');
                if (modalTitle) {
                    modalTitle.focus();
                }
            }, 100);
        });
    }
});