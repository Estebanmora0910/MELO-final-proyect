document.addEventListener("DOMContentLoaded", () => {
    // Selección de elementos del DOM
    const carritoBoton = document.querySelector(".carrito-boton");
    const carritoContainer = document.getElementById("carrito");
    const carritoLista = document.getElementById("carrito-lista");
    const carritoTotal = document.getElementById("carrito-total");
    const vaciarCarritoBtn = document.getElementById("vaciar-carrito");
    const pagarBtn = document.getElementById("ir-a-pagar");
    const contadorCarrito = document.getElementById("contador-carrito");

    // Inicializar carrito desde localStorage
    let carrito = [];
    async function cargarCarrito() {
        try {
            const storedCarrito = localStorage.getItem("carrito");
            if (storedCarrito) {
                carrito = JSON.parse(storedCarrito);
                if (!Array.isArray(carrito)) {
                    console.error("Carrito en localStorage no es un arreglo válido");
                    carrito = [];
                }
                await validarCarritoInicial();
            }
        } catch (error) {
            console.error("Error al cargar carrito desde localStorage:", error);
            carrito = [];
        }
        renderizarCarrito();
    }

    // Validar carrito al cargar
    async function validarCarritoInicial() {
        const carritoValidado = [];
        for (const producto of carrito) {
            try {
                const response = await fetch(`/melo8-main/Controlador/CatalogController.php?action=detalles&id_producto=${encodeURIComponent(producto.id)}`);
                if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
                const data = await response.json();

                if (data.success && data.producto.inv_disponibilidad > 0) {
                    producto.cantidad = Math.min(producto.cantidad, data.producto.inv_disponibilidad);
                    producto.precio = parseFloat(data.producto.pro_valor);
                    carritoValidado.push(producto);
                } else {
                    console.warn(`Producto ${producto.nombre} no disponible o inválido, removido del carrito`);
                }
            } catch (error) {
                console.error(`Error validando producto ${producto.nombre}:`, error);
            }
        }
        carrito = carritoValidado;
        guardarCarrito();
    }

    // Mostrar/ocultar carrito
    carritoBoton.addEventListener("click", () => {
        console.log("Toggle carrito");
        if (carritoContainer) {
            carritoContainer.classList.toggle("carrito-hidden");
            carritoContainer.classList.toggle("carrito-visible");
            if (!carritoContainer.classList.contains("carrito-hidden")) {
                renderizarCarrito(); // Renderizar solo al abrir
            }
        }
    });

    // Función global para agregar productos al carrito
    window.agregarAlCarrito = async (id, nombre, precio) => {
        if (!id || isNaN(precio) || precio <= 0) {
            alert("⚠ Error: Producto inválido");
            return;
        }
        try {
            const response = await fetch(`/melo8-main/Controlador/CatalogController.php?action=detalles&id_producto=${encodeURIComponent(id)}`);
            if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
            const data = await response.json();

            if (!data.success || data.producto.inv_disponibilidad <= 0) {
                alert("⚠ Este producto no está disponible");
                return;
            }

            const productoExistente = carrito.find(item => item.id === id);
            if (productoExistente) {
                if (productoExistente.cantidad >= data.producto.inv_disponibilidad) {
                    alert(`⚠ No puedes agregar más de ${data.producto.inv_disponibilidad} unidades de este producto`);
                    return;
                }
                productoExistente.cantidad++;
            } else {
                carrito.push({
                    id,
                    nombre,
                    precio: parseFloat(precio),
                    cantidad: 1
                });
            }
            guardarCarrito();
        } catch (error) {
            console.error("Error al verificar disponibilidad:", error);
            alert("❌ Error al agregar el producto al carrito");
        }
    };

    // Vaciar carrito
    vaciarCarritoBtn.addEventListener("click", () => {
        carrito = [];
        guardarCarrito();
    });

    // Ir a Mis Pedidos (sin guardar, solo redirección)
    pagarBtn.addEventListener("click", () => {
        if (carrito.length === 0) {
            alert("⚠ Tu carrito está vacío");
            return;
        }
        window.location.href = "/melo8-main/Vista/html/pagos.php";
    });

    function guardarCarrito() {
        try {
            localStorage.setItem("carrito", JSON.stringify(carrito));
            renderizarCarrito();
        } catch (error) {
            console.error("Error al guardar carrito en localStorage:", error);
            alert("❌ Error al guardar el carrito");
        }
    }

    function renderizarCarrito() {
        if (!carritoLista) {
            console.error("Elemento carritoLista no encontrado");
            return;
        }
        carritoLista.innerHTML = "";
        let total = 0;
        let cantidadTotal = 0;

        if (carrito.length === 0) {
            carritoLista.innerHTML = `
                <li class="list-group-item text-center text-muted animate__animated animate__fadeIn">
                    <i class="fas fa-shopping-cart fa-2x mb-2 text-primary"></i><br>
                    Tu carrito está vacío
                </li>
            `;
            if (carritoTotal) carritoTotal.textContent = `Total: $0`;
            if (contadorCarrito) contadorCarrito.textContent = '0';
            if (vaciarCarritoBtn) vaciarCarritoBtn.style.display = 'none';
            if (pagarBtn) pagarBtn.style.display = 'none';
        } else {
            carrito.forEach((producto, index) => {
                if (!producto.id || isNaN(producto.precio) || !producto.cantidad) {
                    console.warn(`Producto inválido en el carrito: ${JSON.stringify(producto)}`);
                    return;
                }

                const li = document.createElement("li");
                li.className = "list-group-item carrito-item animate__animated animate__fadeIn";
                li.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <strong class="text-primary">${producto.nombre}</strong>
                            <div class="text-muted">× ${producto.cantidad} - $${(producto.precio * producto.cantidad).toLocaleString('es-CO')}</div>
                        </div>
                        <div class="d-flex gap-2">
                            <button class="btn btn-sm btn-outline-secondary menos-cantidad" data-index="${index}" title="Reducir cantidad">➖</button>
                            <button class="btn btn-sm btn-outline-secondary mas-cantidad" data-index="${index}" title="Aumentar cantidad">➕</button>
                            <button class="btn btn-sm btn-danger eliminar-producto" data-index="${index}" title="Eliminar producto">✖</button>
                        </div>
                    </div>
                `;
                carritoLista.appendChild(li);

                total += producto.precio * producto.cantidad;
                cantidadTotal += producto.cantidad;
            });
            if (carritoTotal) carritoTotal.textContent = `Total: $${total.toLocaleString('es-CO')}`;
            if (contadorCarrito) contadorCarrito.textContent = cantidadTotal;
            if (vaciarCarritoBtn) vaciarCarritoBtn.style.display = 'block';
            if (pagarBtn) pagarBtn.style.display = 'block';
        }

        asignarEventosBotones();
    }

    function asignarEventosBotones() {
        document.querySelectorAll(".menos-cantidad").forEach(btn => {
            btn.addEventListener("click", () => {
                const index = parseInt(btn.dataset.index);
                if (carrito[index].cantidad > 1) {
                    carrito[index].cantidad--;
                } else {
                    carrito.splice(index, 1);
                }
                guardarCarrito();
            });
        });

        document.querySelectorAll(".mas-cantidad").forEach(btn => {
            btn.addEventListener("click", async () => {
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
                    guardarCarrito();
                } catch (error) {
                    console.error("Error al verificar disponibilidad:", error);
                    alert("❌ Error al aumentar la cantidad");
                }
            });
        });

        document.querySelectorAll(".eliminar-producto").forEach(btn => {
            btn.addEventListener("click", () => {
                const index = parseInt(btn.dataset.index);
                carrito.splice(index, 1);
                guardarCarrito();
            });
        });
    }

    // Inicializar
    cargarCarrito();
});