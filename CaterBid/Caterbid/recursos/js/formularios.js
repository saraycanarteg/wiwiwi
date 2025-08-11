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
    
    // Editar
    $(document).on('click', '.btn-editar', function() {
        const id = $(this).data('id');
        
        if ($('#productoForm').length > 0) {
            // Estamos en productos
            console.log('Editando producto ID:', id); // Debug
            $.get('../controles/ajax_productos.php?accion=obtener&id=' + id, function(r) {
                console.log('Datos del producto:', r); // Debug
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
        }
    });
    
    // Cambiar estado
    $(document).on('click', '.btn-toggle', function() {
        if (!confirm('¿Confirmar acción?')) return;
        
        const id = $(this).data('id');
        const estado = $(this).data('estado');
        
        if ($('#productoForm').length > 0) {
            // Estamos en productos
            console.log('Cambiando estado producto ID:', id); // Debug
            $.post('../controles/ajax_productos.php', {
                accion: 'cambiar_estado',
                id: id,
                estado: estado
            }, function(r) {
                console.log('Respuesta cambio estado:', r); // Debug
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

// Función para mostrar mensajes
function mensaje(text, tipo) {
    $('.alert').remove();
    $('.main-title').after(`<div class="alert alert-${tipo} alert-dismissible fade show">${text}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>`);
    setTimeout(() => $('.alert').fadeOut(), 4000);
}

// Función genérica para limpiar formularios
function limpiar() {
    if ($('#productoForm').length > 0) {
        limpiarProducto();
    } else if ($('#proveedorForm').length > 0) {
        limpiarProveedor();
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