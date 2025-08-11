/* FUNCIONES GENERALES */
$(document).ready(function() {
    // Enviar formulario proveedor - SOLO si existe
    if ($('#proveedorForm').length > 0) {
        $('#proveedorForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            const texto = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
            
            $.ajax({
                url: '../controles/ajax_proveedores.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(r) {
                    mensaje(r.mensaje, r.success ? 'success' : 'danger');
                    if (r.success) {
                        if ($('input[name="accion"]').val() === 'crear') limpiar();
                        cargarTabla();
                    }
                },
                error: function() { mensaje('Error de conexión', 'danger'); },
                complete: function() { btn.html(texto).prop('disabled', false); }
            });
        });
    }

    // Enviar formulario de productos - SOLO si existe
    if ($('#productoForm').length > 0) {
        $('#productoForm').on('submit', function(e) {
            e.preventDefault();
            console.log('Formulario de producto enviado'); // Debug
            
            const btn = $(this).find('button[type="submit"]');
            const texto = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
            
            // Validaciones previas
            const precio = parseFloat($('#precio_unitario').val());
            const cantidad = parseInt($('#cantidad_disponible').val());
            
            if (isNaN(precio) || precio <= 0) {
                mensaje('El precio unitario debe ser mayor a 0', 'danger');
                btn.html(texto).prop('disabled', false);
                return false;
            }
            
            if (isNaN(cantidad) || cantidad < 0) {
                mensaje('La cantidad disponible no puede ser negativa', 'danger');
                btn.html(texto).prop('disabled', false);
                return false;
            }
            
            if ($('#categoria').val() === '') {
                mensaje('Debe seleccionar una categoría', 'danger');
                btn.html(texto).prop('disabled', false);
                return false;
            }
            
            if ($('#id_proveedor').val() === '') {
                mensaje('Debe seleccionar un proveedor', 'danger');
                btn.html(texto).prop('disabled', false);
                return false;
            }
            
            $.ajax({
                url: '../controles/ajax_productos.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(r) {
                    console.log('Respuesta del servidor:', r); // Debug
                    mensaje(r.mensaje, r.success ? 'success' : 'danger');
                    if (r.success) {
                        if ($('input[name="accion"]').val() === 'crear') limpiar();
                        cargarTablaProductos();
                    }
                },
                error: function(xhr, status, error) { 
                    console.log('Error AJAX:', status, error, xhr.responseText); // Debug
                    mensaje('Error de conexión: ' + error, 'danger'); 
                },
                complete: function() { btn.html(texto).prop('disabled', false); }
            });
        });
    }

    // Enviar formulario de roles - SOLO si existe
    if ($('#rolForm').length > 0) {
        $('#rolForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            const texto = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);

            $.ajax({
                url: '../controles/ajax_roles.php',
                type: 'POST',
                data: $(this).serialize(),
                dataType: 'json',
                success: function(r) {
                    mensaje(r.mensaje, r.success ? 'success' : 'danger');
                    if (r.success) {
                        if ($('input[name="accion"]').val() === 'crear') limpiar();
                        cargarTablaRoles();
                    }
                },
                error: function() { mensaje('Error de conexión', 'danger'); },
                complete: function() { btn.html(texto).prop('disabled', false); }
            });
        });
    }

    // Enviar formulario de paquetes - SOLO si existe
    if ($('#paqueteForm').length > 0) {
        $('#paqueteForm').on('submit', function(e) {
            e.preventDefault();
            console.log('Formulario de paquete enviado');
            
            const btn = $(this).find('button[type="submit"]');
            const texto = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
            
            // Validaciones previas
            if ($('#categoria_evento').val() === '') {
                mensaje('Debe seleccionar una categoría de evento', 'danger');
                btn.html(texto).prop('disabled', false);
                return false;
            }
            
            if ($('#tipo_evento').val() === '') {
                mensaje('Debe seleccionar un tipo de evento', 'danger');
                btn.html(texto).prop('disabled', false);
                return false;
            }
            
            if ($('#id_proveedor').val() === '') {
                mensaje('Debe seleccionar un proveedor', 'danger');
                btn.html(texto).prop('disabled', false);
                return false;
            }
            
            // Validar que al menos un producto esté seleccionado
            const productosSeleccionados = $('input[name="productos[]"]:checked');
            if (productosSeleccionados.length === 0) {
                mensaje('Debe seleccionar al menos un producto para el paquete', 'danger');
                btn.html(texto).prop('disabled', false);
                return false;
            }
            
            // Validar cantidades
            let cantidadesValidas = true;
            let errorCantidad = '';
            
            productosSeleccionados.each(function() {
                const idProducto = $(this).val();
                const cantidadInput = $(`input[name="cantidades[${idProducto}]"]`);
                const cantidad = parseInt(cantidadInput.val());
                const maxStock = parseInt(cantidadInput.attr('max'));
                
                if (isNaN(cantidad) || cantidad <= 0) {
                    cantidadesValidas = false;
                    errorCantidad = 'Todas las cantidades deben ser mayores a 0';
                    return false;
                }
                
                if (maxStock && cantidad > maxStock) {
                    cantidadesValidas = false;
                    errorCantidad = `La cantidad no puede exceder el stock disponible (${maxStock})`;
                    return false;
                }
            });
            
            if (!cantidadesValidas) {
                mensaje(errorCantidad, 'danger');
                btn.html(texto).prop('disabled', false);
                return false;
            }
            
            // Preparar datos usando serialize para compatibilidad
            let formData = $(this).serialize();
            
            // Agregar productos seleccionados manualmente
            productosSeleccionados.each(function() {
                const idProducto = $(this).val();
                formData += '&productos[]=' + encodeURIComponent(idProducto);
            });
            
            // Agregar cantidades para cada producto seleccionado
            productosSeleccionados.each(function() {
                const idProducto = $(this).val();
                const cantidad = $(`input[name="cantidades[${idProducto}]"]`).val();
                formData += '&cantidades[' + encodeURIComponent(idProducto) + ']=' + encodeURIComponent(cantidad);
            });
            
            console.log('Datos a enviar:', formData);
            
            $.ajax({
                url: '../controles/ajax_paquetes.php',
                type: 'POST',
                data: formData,
                dataType: 'json',
                success: function(r) {
                    console.log('Respuesta del servidor:', r);
                    if (r.success) {
                        mensaje(r.mensaje, 'success');
                        if ($('input[name="accion"]').val() === 'crear') {
                            limpiarPaquete();
                        }
                        // Recargar tabla después de un pequeño delay para asegurar que se guardó
                        setTimeout(function() {
                            cargarTablaPaquetes();
                        }, 500);
                    } else {
                        mensaje(r.mensaje, 'danger');
                    }
                },
                error: function(xhr, status, error) { 
                    console.log('Error AJAX:', {
                        status: status,
                        error: error,
                        responseText: xhr.responseText
                    });
                    
                    // Intentar parsear la respuesta para mostrar un mensaje más específico
                    try {
                        const response = JSON.parse(xhr.responseText);
                        mensaje(response.mensaje || 'Error del servidor', 'danger');
                    } catch (e) {
                        mensaje('Error de conexión: ' + error, 'danger');
                    }
                },
                complete: function() { 
                    btn.html(texto).prop('disabled', false); 
                }
            });
        });
    }

    // Editar
    $(document).on('click', '.btn-editar', function() {
        const id = $(this).data('id');
        
        if ($('#productoForm').length > 0) {
            // Estamos en productos
            console.log('Editando producto ID:', id);
            $.get('../controles/ajax_productos.php?accion=obtener&id=' + id, function(r) {
                console.log('Datos del producto:', r);
                if (r.success) {
                    $('#nombre').val(r.data.nombre);
                    $('#descripcion').val(r.data.descripcion);
                    $('#precio_unitario').val(r.data.precio_unitario);
                    $('#cantidad_disponible').val(r.data.cantidad_disponible);
                    $('#categoria').val(r.data.categoria);
                    $('#id_proveedor').val(r.data.id_proveedor);
                    $('input[name="accion"]').val('editar');
                    $('#productoForm').append('<input type="hidden" name="id_producto" value="' + r.data.id_producto + '">');
                    $('.form-container h3').text('Editar Producto');
                    $('button[type="submit"]').html('<i class="fas fa-save me-1"></i>Actualizar');
                } else {
                    mensaje(r.mensaje, 'danger');
                }
            }, 'json').fail(function() {
                mensaje('Error al cargar datos del producto', 'danger');
            });
        } else if ($('#proveedorForm').length > 0) {
            // Estamos en proveedores
            $.get('../controles/ajax_proveedores.php?accion=obtener&id=' + id, function(r) {
                if (r.success) {
                    $('#nombre').val(r.data.nombre);
                    $('#ruc').val(r.data.ruc);
                    $('#correo').val(r.data.correo);
                    $('#telefono').val(r.data.telefono);
                    $('#direccion').val(r.data.direccion);
                    $('input[name="accion"]').val('editar');
                    $('#proveedorForm').append('<input type="hidden" name="id_proveedor" value="' + r.data.id_proveedor + '">');
                    $('.form-container h3').text('Editar Proveedor');
                    $('button[type="submit"]').html('<i class="fas fa-save me-1"></i>Actualizar');
                }
            }, 'json');
        } else if ($('#rolForm').length > 0) {
            // Estamos en roles
            $.get('../controles/ajax_roles.php?accion=obtener&id=' + id, function(r) {
                if (r.success) {
                    $('#nombre_rol').val(r.data.nombre_rol);
                    $('#descripcion').val(r.data.descripcion);
                    $('input[name="accion"]').val('editar');
                    $('#rolForm').append('<input type="hidden" name="id_rol" value="' + r.data.id_rol + '">');

                    // Limpiar permisos y marcar los correspondientes
                    $('input[name="permisos[]"]').prop('checked', false);
                    if (r.data.permisos && r.data.permisos.length > 0) {
                        r.data.permisos.forEach(function(p) {
                            $('#perm_' + p).prop('checked', true);
                        });
                    }

                    $('.form-container h3').text('Editar Rol');
                    $('button[type="submit"]').html('<i class="fas fa-save me-1"></i>Actualizar');
                } else {
                    mensaje(r.mensaje, 'danger');
                }
            }, 'json').fail(function() {
                mensaje('Error al cargar datos del rol', 'danger');
            });
        } else if ($('#paqueteForm').length > 0) {
            // Estamos en paquetes
            console.log('Editando paquete ID:', id);
            $.get('../controles/ajax_paquetes.php?accion=obtener&id=' + id, function(r) {
                console.log('Datos del paquete:', r);
                if (r.success) {
                    // Primero limpiar el formulario
                    limpiarPaquete();
                    
                    // Cargar datos básicos del paquete
                    $('#categoria_evento').val(r.data.categoria_evento);
                    
                    // Trigger change para cargar tipos de evento
                    $('#categoria_evento').trigger('change');
                    
                    // Esperar a que se carguen los tipos de evento
                    setTimeout(() => {
                        $('#tipo_evento').val(r.data.tipo_evento);
                        $('#id_proveedor').val(r.data.id_proveedor);
                        
                        // Trigger change para cargar productos del proveedor
                        $('#id_proveedor').trigger('change');
                        
                        // Esperar a que se carguen los productos y marcar los seleccionados
                        setTimeout(() => {
                            if (r.data.productos && r.data.productos.length > 0) {
                                r.data.productos.forEach(function(producto) {
                                    // Marcar checkbox del producto
                                    const checkbox = $(`#producto_${producto.id_producto}`);
                                    if (checkbox.length > 0) {
                                        checkbox.prop('checked', true);
                                        
                                        // Habilitar y establecer cantidad
                                        const cantidadInput = $(`#cantidad_${producto.id_producto}`);
                                        cantidadInput.prop('disabled', false)
                                                    .val(producto.cantidad_producto);
                                        
                                        // Marcar visualmente como seleccionado
                                        checkbox.closest('.producto-checkbox').addClass('selected');
                                    }
                                });
                                
                                // Validar selección después de cargar
                                validarSeleccionProductos();
                            }
                        }, 2000); // Aumentar tiempo de espera
                    }, 1000);
                    
                    // Configurar formulario para edición
                    $('input[name="accion"]').val('editar');
                    $('#paqueteForm').append('<input type="hidden" name="id_paquete" value="' + r.data.id_paquete + '">');
                    $('.form-container h3').text('Editar Paquete');
                    $('button[type="submit"]').html('<i class="fas fa-save me-1"></i>Actualizar');
                } else {
                    mensaje(r.mensaje, 'danger');
                }
            }, 'json').fail(function(xhr, status, error) {
                console.log('Error al cargar paquete:', status, error, xhr.responseText);
                mensaje('Error al cargar datos del paquete', 'danger');
            });
        }
    });
    
    // Cambiar estado
    $(document).on('click', '.btn-toggle', function() {
        if (!confirm('¿Confirmar acción?')) return;
        
        const id = $(this).data('id');
        const estado = $(this).data('estado');
        
        if ($('#productoForm').length > 0) {
            // Estamos en productos
            console.log('Cambiando estado producto ID:', id);
            $.post('../controles/ajax_productos.php', {
                accion: 'cambiar_estado',
                id: id,
                estado: estado
            }, function(r) {
                console.log('Respuesta cambio estado:', r);
                mensaje(r.mensaje, r.success ? 'success' : 'danger');
                if (r.success) cargarTablaProductos();
            }, 'json').fail(function() {
                mensaje('Error al cambiar estado del producto', 'danger');
            });
        } else if ($('#proveedorForm').length > 0) {
            // Estamos en proveedores
            $.post('../controles/ajax_proveedores.php', {
                accion: 'cambiar_estado',
                id: id,
                estado: estado
            }, function(r) {
                mensaje(r.mensaje, r.success ? 'success' : 'danger');
                if (r.success) cargarTabla();
            }, 'json');
        } else if ($('#rolForm').length > 0) {
            // Estamos en roles
            $.post('../controles/ajax_roles.php', {
                accion: 'cambiar_estado',
                id: id,
                estado: estado
            }, function(r) {
                mensaje(r.mensaje, r.success ? 'success' : 'danger');
                if (r.success) cargarTablaRoles();
            }, 'json').fail(function() {
                mensaje('Error al cambiar estado del rol', 'danger');
            });
        } else if ($('#paqueteForm').length > 0) {
            // Estamos en paquetes
            console.log('Cambiando estado paquete ID:', id);
            $.post('../controles/ajax_paquetes.php', {
                accion: 'cambiar_estado',
                id: id,
                estado: estado
            }, function(r) {
                console.log('Respuesta cambio estado:', r);
                mensaje(r.mensaje, r.success ? 'success' : 'danger');
                if (r.success) cargarTablaPaquetes();
            }, 'json').fail(function() {
                mensaje('Error al cambiar estado del paquete', 'danger');
            });
        }
    });
    
    // Cargar tablas SOLO si existen
    if ($('#tabla-productos').length) {
        cargarTablaProductos();
    }
    
    if ($('#tabla-proveedores').length) {
        cargarTabla();
    }

    // Validaciones específicas de proveedores - SOLO si el formulario existe
    if ($('#proveedorForm').length > 0) {
        initProveedorValidations();
    }

    if ($('#tabla-roles').length) {
        cargarTablaRoles();
    }

    if ($('#tabla-paquetes').length) {
        cargarTablaPaquetes();
    }
});

// Funciones para cargar tablas
function cargarTabla() {
    $.get('../controles/ajax_proveedores.php?accion=cargar_tabla', function(html) {
        $('#tabla-proveedores').html(html);
    });
}

function cargarTablaProductos() {
    $.get('../controles/ajax_productos.php?accion=cargar_tabla', function(html) {
        $('#tabla-productos').html(html);
    }).fail(function() {
        mensaje('Error al cargar tabla de productos', 'danger');
    });
}

function cargarTablaRoles() {
    $.get('../controles/ajax_roles.php?accion=cargar_tabla', function(html) {
        $('#tabla-roles').html(html);
    }).fail(function() {
        mensaje('Error al cargar tabla de roles', 'danger');
    });
}

function cargarTablaPaquetes() {
    console.log('Cargando tabla de paquetes...');
    
    const tabla = $('#tabla-paquetes');
    if (tabla.length > 0) {
        tabla.html('<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>');
    }
    
    $.get('../controles/ajax_paquetes.php', {
        accion: 'cargar_tabla'
    })
    .done(function(html) {
        console.log('Tabla cargada exitosamente');
        tabla.html(html);
    })
    .fail(function(xhr, status, error) {
        console.log('Error al cargar tabla:', status, error, xhr.responseText);
        tabla.html('<tr><td colspan="7" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Error al cargar datos</td></tr>');
        mensaje('Error al cargar tabla de paquetes', 'danger');
    });
}

// Función para mostrar mensajes
function mensaje(text, tipo) {
    $('.alert').remove();
    $('.main-title').after(`<div class="alert alert-${tipo} alert-dismissible fade show">${text}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
    setTimeout(() => $('.alert').fadeOut(), 4000);
}

// Función genérica para limpiar formularios
function limpiar() {
    if ($('#paqueteForm').length > 0) {
        limpiarPaquete();
    } else if ($('#productoForm').length > 0) {
        limpiarProducto();
    } else if ($('#proveedorForm').length > 0) {
        limpiarProveedor();
    } else if ($('#rolForm').length > 0) {
        limpiarRol();
    }
}

function limpiarProducto() {
    $('#productoForm')[0].reset();
    $('input[name="accion"]').val('crear');
    $('input[name="id_producto"]').remove();
    $('.form-container h3').text('Nuevo Producto');
    $('button[type="submit"]').html('<i class="fas fa-save me-1"></i>Guardar');
}

function limpiarProveedor() {
    $('#proveedorForm')[0].reset();
    $('input[name="accion"]').val('crear');
    $('input[name="id_proveedor"]').remove();
    $('.form-container h3').text('Nuevo Proveedor');
    $('button[type="submit"]').html('<i class="fas fa-save me-1"></i>Guardar');
}

function limpiarRol() {
    $('#rolForm')[0].reset();
    $('input[name="accion"]').val('crear');
    $('input[name="id_rol"]').remove();
    $('.form-container h3').text('Nuevo Rol');
    $('button[type="submit"]').html('<i class="fas fa-save me-1"></i>Guardar');
    $('input[name="permisos[]"]').prop('checked', false);
}

function limpiarPaquete() {
    $('#paqueteForm')[0].reset();
    $('input[name="accion"]').val('crear');
    $('input[name="id_paquete"]').remove();
    $('.form-container h3').text('Nuevo Paquete');
    $('button[type="submit"]').html('<i class="fas fa-save me-1"></i>Guardar Paquete');
    
    $('#tipo_evento').prop('disabled', true).html('<option value="">Primero selecciona una categoría...</option>');
    $('#productos-section').hide();
    
    $('#productos-container').html('');
    
    $('input[name="productos[]"]').prop('checked', false);
    $('.producto-checkbox').removeClass('selected');
    
    $('input[name^="cantidades"]').prop('disabled', true).val(1);
    
    const submitBtn = document.querySelector('#paqueteForm button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Guardar Paquete';
    }
}

// Función para validar selección de productos (desde el HTML)
function validarSeleccionProductos() {
    const productosSeleccionados = document.querySelectorAll('input[name="productos[]"]:checked');
    const submitBtn = document.querySelector('#paqueteForm button[type="submit"]');
    
    if (submitBtn) {
        if (productosSeleccionados.length === 0) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Selecciona al menos un producto';
        } else {
            submitBtn.disabled = false;
            const accion = $('input[name="accion"]').val();
            const textoBoton = accion === 'editar' ? 'Actualizar' : 'Guardar Paquete';
            submitBtn.innerHTML = `<i class="fas fa-save me-1"></i>${textoBoton}`;
        }
    }
}

// Función para toggle de productos (desde el HTML)
function toggleProducto(checkbox, idProducto) {
    console.log('Toggle producto:', idProducto, checkbox.checked);
    
    const cantidadInput = document.getElementById(`cantidad_${idProducto}`);
    const productoDiv = checkbox.closest('.producto-checkbox');
    
    if (checkbox && cantidadInput && productoDiv) {
        if (checkbox.checked) {
            cantidadInput.disabled = false;
            productoDiv.classList.add('selected');
            
            // Asegurar que tenga un valor válido
            if (!cantidadInput.value || cantidadInput.value <= 0) {
                cantidadInput.value = 1;
            }
            
            // Agregar event listener para validar en tiempo real
            cantidadInput.addEventListener('input', function() {
                const valor = parseInt(this.value);
                const max = parseInt(this.getAttribute('max'));
                
                if (isNaN(valor) || valor <= 0) {
                    this.setCustomValidity('La cantidad debe ser mayor a 0');
                } else if (max && valor > max) {
                    this.setCustomValidity(`La cantidad no puede exceder ${max}`);
                } else {
                    this.setCustomValidity('');
                }
            });
            
        } else {
            cantidadInput.disabled = true;
            cantidadInput.value = 1;
            cantidadInput.setCustomValidity('');
            productoDiv.classList.remove('selected');
        }
        
        // Validar que al menos un producto esté seleccionado
        validarSeleccionProductos();
    }
}

// Inicializar validaciones específicas de proveedores
function initProveedorValidations() {
    // Validación en tiempo real del RUC - SOLO si el elemento existe
    const rucInput = document.getElementById('ruc');
    if (rucInput) {
        rucInput.addEventListener('input', function() {
            const ruc = this.value;
            const isValid = /^\d{10,13}$/.test(ruc);
            
            if (ruc.length > 0 && !isValid) {
                this.setCustomValidity('El RUC debe contener entre 10 y 13 dígitos');
            } else {
                this.setCustomValidity('');
            }
        });
    }

    // Validación del formulario de proveedores antes del envío - SOLO si existe
    const proveedorForm = document.getElementById('proveedorForm');
    if (proveedorForm) {
        proveedorForm.addEventListener('submit', function(e) {
            const rucElement = document.getElementById('ruc');
            const correoElement = document.getElementById('correo');
            
            if (rucElement && correoElement) {
                const ruc = rucElement.value;
                const correo = correoElement.value;
                
                if (!/^\d{10,13}$/.test(ruc)) {
                    e.preventDefault();
                    mensaje('El RUC debe contener entre 10 y 13 dígitos', 'danger');
                    return;
                }
                
                if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
                    e.preventDefault();
                    mensaje('Por favor ingrese un correo electrónico válido', 'danger');
                    return;
                }
            }
        });
    }
}

// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }
    });
}, 5000);