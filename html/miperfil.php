<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/melo8-main/Vista/css/miperfil.css">
</head>
<body>

<?php include __DIR__ . '/../../header.php' ?>

<div class="miperfil-container">
    <div class="container">
        <h2 class="title">Información de la cuenta <i class="fas fa-user-circle"></i></h2>
        
        <!-- Vista de detalles -->
        <div class="main-content">
            <?php
            $message = isset($_GET['message']) ? urldecode($_GET['message']) : '';
            $error = isset($_GET['error']) ? urldecode($_GET['error']) : '';
            ?>
            <div class="header-actions d-flex justify-content-center">
                <button class="btn btn-secondary" onclick="window.location.href='/melo8-main/index.php'"><i class="fas fa-arrow-left"></i> Regresar</button>
            </div>
            <br>
            <ul class="profile-list" id="detalles-perfil">
                <li class="profile-item">
                    <div class="field-name">Nombre de visualización <i class="fas fa-user"></i></div>
                    <div class="field-value" id="det-nombre"></div>
                    <button class="edit-icon" data-field="reg_nombre"><i class="fas fa-edit"></i></button>
                </li>
                <li class="profile-item">
                    <div class="field-name">Nombre de usuario <i class="fas fa-id-card"></i></div>
                    <div class="field-value" id="det-usuario"></div>
                </li>
                <li class="profile-item">
                    <div class="field-name">Contraseña <i class="fas fa-lock"></i></div>
                    <div class="field-value">********</div>
                    <button class="edit-icon" data-field="contrasena"><i class="fas fa-edit"></i></button>
                </li>
                <li class="profile-item">
                    <div class="field-name">Número de teléfono <i class="fas fa-phone"></i></div>
                    <div class="field-value" id="det-telefono"></div>
                    <button class="edit-icon" data-field="reg_telefono"><i class="fas fa-edit"></i></button>
                </li>
                <li class="profile-item">
                    <div class="field-name">Dirección de correo electrónico <i class="fas fa-envelope"></i></div>
                    <div class="field-value" id="det-correo"></div>
                    <button class="edit-icon" data-field="reg_correo"><i class="fas fa-edit"></i></button>
                </li>
            </ul>

            <!-- Sección de pedidos anteriores -->
            <h2 class="title">Mis Pedidos Anteriores <i class="fas fa-shopping-bag"></i></h2>
            <ul class="order-list" id="pedidos-anteriores">
                <!-- Los pedidos se cargarán dinámicamente con JavaScript -->
            </ul>
        </div>

        <!-- Modal para editar campo -->
        <div id="modal-editar" class="modal" inert>
            <div class="modal-content">
                <span class="close-btn" onclick="cancelEdit('modal-editar')"><i class="fas fa-times"></i></span>
                <h3 id="modal-title"><i class="fas fa-pen"></i> Editar Campo</h3>
                <form id="edit-form" action="/melo8-main/Controlador/PerfilController.php" method="POST">
                    <input type="hidden" name="id_usuario" value="<?php echo htmlspecialchars($_SESSION['id_usuario'] ?? ''); ?>">
                    <input type="hidden" name="field" id="edit-field">
                    <input type="hidden" name="editar_campo" value="1">
                    <div class="form-group" id="edit-group">
                        <label id="edit-label" for="edit-value"></label>
                        <input name="value" id="edit-value" required>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn-update"><i class="fas fa-save"></i> Actualizar</button>
                        <button type="button" class="btn-cancel" onclick="cancelEdit('modal-editar')"><i class="fas fa-times"></i> Cancelar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Modal para detalles de pedido -->
        <div id="modal-detalle-pedido" class="modal" inert>
            <div class="modal-content">
                <span class="close-btn" onclick="cancelEdit('modal-detalle-pedido')"><i class="fas fa-times"></i></span>
                <h3 id="modal-detalle-title"><i class="fas fa-shopping-bag"></i> Detalles del Pedido</h3>
                <div id="detalle-pedido-contenido">
                    <!-- Los detalles se cargarán dinámicamente con JavaScript -->
                </div>
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="cancelEdit('modal-detalle-pedido')"><i class="fas fa-times"></i> Cerrar</button>
                </div>
            </div>
        </div>
        
        <div id="custom-alert" class="custom-alert"></div>
        <div id="mensaje" class="message"></div>
    </div>
</div>

    <script>
        let lastFocusedElement = null;

        window.openModal = function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                lastFocusedElement = document.activeElement;
                modal.classList.add('show-modal');
                modal.removeAttribute('inert');
                modal.setAttribute('aria-hidden', 'false');
                const firstFocusable = modal.querySelector('input, button, [tabindex="0"]');
                if (firstFocusable) {
                    firstFocusable.focus();
                }
                console.log('Modal abierto:', modalId);
            }
        };

        window.cancelEdit = function(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                if (lastFocusedElement) {
                    lastFocusedElement.focus();
                } else {
                    const firstFocusableOutside = document.querySelector('button:not(.btn-cancel, .btn-update), a');
                    if (firstFocusableOutside) {
                        firstFocusableOutside.focus();
                    }
                }
                modal.classList.remove('show-modal');
                modal.setAttribute('inert', '');
                modal.setAttribute('aria-hidden', 'true');
                console.log('Modal cerrado:', modalId);
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('modal-editar');
            const modalTitle = document.getElementById('modal-title');
            const editField = document.getElementById('edit-field');
            const editLabel = document.getElementById('edit-label');
            const editValue = document.getElementById('edit-value');
            const editGroup = document.getElementById('edit-group');
            const editForm = document.getElementById('edit-form');
            const customAlert = document.getElementById('custom-alert');
            let profileData = {};

            function showAlert(message, type = 'success') {
                customAlert.className = `custom-alert alert-${type}`;
                customAlert.textContent = message;
                customAlert.style.display = 'block';
                setTimeout(() => {
                    customAlert.style.opacity = '0';
                    setTimeout(() => {
                        customAlert.style.display = 'none';
                        customAlert.style.opacity = '1';
                    }, 500);
                }, 3000);
            }

            window.onclick = function(event) {
                if (event.target === modal || event.target === document.getElementById('modal-detalle-pedido')) {
                    cancelEdit(event.target.id);
                }
            };

            function maskEmail(email) {
                if (!email || typeof email !== 'string') return '';
                const [user, domain] = email.split('@');
                return user[0] + '*'.repeat(Math.max(0, user.length - 1)) + '@' + domain;
            }

            function maskPhone(phone) {
                if (!phone || typeof phone !== 'string') return '';
                if (phone.length !== 13 || !phone.match(/^\+\d{2}\d{10}$/)) return phone;
                const digits = phone.slice(3);
                return '+57 ****' + digits.slice(-4);
            }

            const fieldConfig = {
                'reg_nombre': {label: 'Nombre de visualización', type: 'text'},
                'contrasena': {label: 'Contraseña', type: 'password'},
                'reg_telefono': {label: 'Número de teléfono', type: 'tel'},
                'reg_correo': {label: 'Dirección de correo electrónico', type: 'email'}
            };

            document.querySelectorAll('.edit-icon').forEach(button => {
                button.addEventListener('click', function() {
                    const field = this.dataset.field;
                    const config = fieldConfig[field];
                    if (!config) return;

                    editGroup.innerHTML = '';

                    if (field === 'contrasena') {
                        modalTitle.textContent = `Editar ${config.label}`;
                        editField.value = field;

                        const oldLabel = document.createElement('label');
                        oldLabel.textContent = 'Contraseña anterior:';
                        oldLabel.setAttribute('for', 'old_password');
                        const oldInput = document.createElement('input');
                        oldInput.type = 'password';
                        oldInput.name = 'old_password';
                        oldInput.id = 'old_password';
                        oldInput.required = true;

                        const newLabel = document.createElement('label');
                        newLabel.textContent = 'Contraseña nueva:';
                        newLabel.setAttribute('for', 'new_password');
                        const newInput = document.createElement('input');
                        newInput.type = 'password';
                        newInput.name = 'new_password';
                        newInput.id = 'new_password';
                        newInput.required = true;

                        const confirmLabel = document.createElement('label');
                        confirmLabel.textContent = 'Confirmar contraseña:';
                        confirmLabel.setAttribute('for', 'confirm_password');
                        const confirmInput = document.createElement('input');
                        confirmInput.type = 'password';
                        confirmInput.name = 'confirm_password';
                        confirmInput.id = 'confirm_password';
                        confirmInput.required = true;

                        editGroup.appendChild(oldLabel);
                        editGroup.appendChild(oldInput);
                        editGroup.appendChild(newLabel);
                        editGroup.appendChild(newInput);
                        editGroup.appendChild(confirmLabel);
                        editGroup.appendChild(confirmInput);
                    } else {
                        modalTitle.textContent = `Editar ${config.label}`;
                        editLabel.textContent = `${config.label}:`;
                        editField.value = field;
                        editValue.type = config.type;
                        editValue.value = profileData[field] || '';
                        editValue.name = 'value';
                        editGroup.appendChild(editLabel);
                        editGroup.appendChild(editValue);
                    }

                    openModal('modal-editar');
                });
            });

            editForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const formData = new FormData(this);
                formData.append('editar_campo', '1');
                const formDataObj = Object.fromEntries(formData);
                console.log('Form data sent:', formDataObj);

                fetch('/melo8-main/Controlador/PerfilController.php', {
                    method: 'POST',
                    body: formData
                })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    console.log('Response from server:', data);
                    if (data.success) {
                        showAlert('Perfil actualizado con éxito!', 'success');
                        loadProfile();
                        cancelEdit('modal-editar');
                    } else {
                        showAlert(data.error || 'Error al actualizar el perfil', 'error');
                    }
                })
                .catch(err => {
                    console.error('Fetch error:', err);
                    showAlert('Error en la conexión: ' + err.message, 'error');
                });
            });

            function loadProfile() {
                fetch("/melo8-main/Controlador/PerfilController.php", {
                    method: "GET"
                })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    console.log('Profile load response:', data);
                    const mensajeDiv = document.getElementById("mensaje");
                    if (data.error) {
                        mensajeDiv.style.color = "red";
                        mensajeDiv.textContent = data.error;
                        mensajeDiv.classList.add("show");
                        setTimeout(() => mensajeDiv.classList.remove("show"), 3000);
                    } else {
                        profileData = data.data || {};
                        document.getElementById("det-nombre").textContent = profileData.reg_nombre || '';
                        document.getElementById("det-usuario").textContent = profileData.reg_nombre_usuario || '';
                        document.getElementById("det-telefono").textContent = maskPhone(profileData.reg_telefono) || profileData.reg_telefono || '';
                        document.getElementById("det-correo").textContent = maskEmail(profileData.reg_correo) || profileData.reg_correo || '';
                        mensajeDiv.textContent = '';
                    }
                })
                .catch(err => {
                    console.error("Error al cargar perfil:", err);
                    document.getElementById("mensaje").style.color = "red";
                    document.getElementById("mensaje").textContent = "Error al cargar datos del perfil: " + err.message;
                    document.getElementById("mensaje").classList.add("show");
                    setTimeout(() => document.getElementById("mensaje").classList.remove("show"), 3000);
                });
            }

            function loadOrders() {
                fetch("/melo8-main/Controlador/PerfilController.php?action=get_orders", {
                    method: "GET"
                })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    console.log('Orders load response:', data);
                    const orderList = document.getElementById("pedidos-anteriores");
                    const mensajeDiv = document.getElementById("mensaje");
                    if (data.error) {
                        mensajeDiv.style.color = "red";
                        mensajeDiv.textContent = data.error;
                        mensajeDiv.classList.add("show");
                        setTimeout(() => mensajeDiv.classList.remove("show"), 3000);
                    } else {
                        orderList.innerHTML = '';
                        if (data.orders && data.orders.length > 0) {
                            data.orders.forEach(order => {
                                const li = document.createElement('li');
                                li.className = 'order-item';
                                li.innerHTML = `
                                    <div class="order-info">
                                        <span class="order-id">Pedido #${order.id_pedido}</span>
                                        <span class="order-date">${new Date(order.ped_fecha).toLocaleDateString('es-ES')}</span>
                                        <span class="order-total">Total: $${parseFloat(order.ped_valor_total).toFixed(2)}</span>
                                    </div>
                                    <button class="order-details-btn" data-pedido-id="${order.id_pedido}"><i class="fas fa-info-circle"></i> Ver Detalles</button>
                                `;
                                orderList.appendChild(li);
                            });

                            document.querySelectorAll('.order-details-btn').forEach(button => {
                                button.addEventListener('click', function() {
                                    const pedidoId = this.dataset.pedidoId;
                                    loadOrderDetails(pedidoId);
                                });
                            });
                        } else {
                            orderList.innerHTML = '<li class="order-item">No hay pedidos anteriores.</li>';
                        }
                    }
                })
                .catch(err => {
                    console.error("Error al cargar pedidos:", err);
                    document.getElementById("mensaje").style.color = "red";
                    document.getElementById("mensaje").textContent = "Error al cargar los pedidos: " + err.message;
                    document.getElementById("mensaje").classList.add("show");
                    setTimeout(() => document.getElementById("mensaje").classList.remove("show"), 3000);
                });
            }

            function loadOrderDetails(pedidoId) {
                fetch(`/melo8-main/Controlador/PerfilController.php?action=get_order_details&pedido_id=${pedidoId}`, {
                    method: "GET"
                })
                .then(res => {
                    if (!res.ok) throw new Error(`HTTP error! status: ${res.status}`);
                    return res.json();
                })
                .then(data => {
                    console.log('Order details response:', data);
                    const detalleContenido = document.getElementById("detalle-pedido-contenido");
                    if (data.error) {
                        showAlert(data.error, 'error');
                    } else {
                        detalleContenido.innerHTML = `
                            <p><strong>Pedido #${data.id_pedido}</strong></p>
                            <p><strong>Fecha:</strong> ${new Date(data.ped_fecha).toLocaleDateString('es-ES')}</p>
                            <p><strong>Total:</strong> $${parseFloat(data.ped_valor_total).toFixed(2)}</p>
                            <h4>Productos</h4>
                            <ul class="order-detail-list">
                                ${data.detalles.map(item => `
                                    <li class="order-detail-item">
                                        <span>${item.pro_nombre}</span>
                                        <span>Cantidad: ${item.det_cantidad}</span>
                                        <span>Precio Unitario: $${parseFloat(item.det_precio_unitario).toFixed(2)}</span>
                                        <span>Total: $${(item.det_cantidad * item.det_precio_unitario).toFixed(2)}</span>
                                    </li>
                                `).join('')}
                            </ul>
                        `;
                        openModal('modal-detalle-pedido');
                    }
                })
                .catch(err => {
                    console.error("Error al cargar detalles del pedido:", err);
                    showAlert("Error al cargar detalles del pedido: " + err.message, 'error');
                });
            }

            loadProfile();
            loadOrders();
        });
    </script>
    <?php include __DIR__ . '/../../footer.php' ?>
</body>
</html>