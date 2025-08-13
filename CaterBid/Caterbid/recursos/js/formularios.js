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
    // Enviar formulario de usuarios - SOLO si existe
    if ($('#usuarioForm').length > 0) {
        $('#usuarioForm').on('submit', function(e) {
            e.preventDefault();
            const btn = $(this).find('button[type="submit"]');
            const texto = btn.html();
            btn.html('<i class="fas fa-spinner fa-spin"></i> Procesando...').prop('disabled', true);
            
            // Validaciones previas
            const accion = $('input[name="accion"]').val();
            const contraseña = $('#contraseña').val();
            const confirmar = $('#confirmar_contraseña').val();
            
            // Para crear, la contraseña es obligatoria
            if (accion === 'crear' && !contraseña) {
                mensaje('La contraseña es obligatoria', 'danger');
                btn.html(texto).prop('disabled', false);
                return false;
            }
            
            // Si se proporciona contraseña, validarla
            if (contraseña && contraseña !== confirmar) {
                mensaje('Las contraseñas no coinciden', 'danger');
                btn.html(texto).prop('disabled', false);
                return false;
            }
            
            if (contraseña && contraseña.length < 6) {
                mensaje('La contraseña debe tener al menos 6 caracteres', 'danger');
                btn.html(texto).prop('disabled', false);
                return false;
            }
            
            $.ajax({
                url: '../controles/ajax_usuarios.php',
                type: 'POST',
                data: new FormData(this),
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(r) {
                    mensaje(r.mensaje, r.success ? 'success' : 'danger');
                    if (r.success) {
                        if ($('input[name="accion"]').val() === 'crear') limpiar();
                        cargarTablaUsuarios();
                    }
                },
                error: function() { mensaje('Error de conexión', 'danger'); },
                complete: function() { btn.html(texto).prop('disabled', false); }
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
        }else if ($('#usuarioForm').length > 0) {
            console.log('Editando usuario ID:', id);
            $.get('../controles/ajax_usuarios.php?accion=obtener&id=' + id, function(r) {
                console.log('Datos del usuario:', r);
                if (r.success) {
                    $('#nombre').val(r.data.nombre);
                    $('#correo').val(r.data.correo);
                    $('#direccion').val(r.data.direccion || '');
                    $('#id_rol').val(r.data.id_rol);
                    
                    // Limpiar campos de contraseña
                    $('#contraseña').val('');
                    $('#confirmar_contraseña').val('');
                    
                    // Mostrar mensaje informativo sobre la contraseña
                    $('.password-info').remove();
                    $('#contraseña').after('<small class="form-text text-muted password-info">Deja en blanco para mantener la contraseña actual</small>');
                    
                    $('input[name="accion"]').val('editar');
                    $('#usuarioForm').append('<input type="hidden" name="id_usuario" value="' + r.data.id_usuario + '">');
                    $('.form-container h3').text('Editar Usuario');
                    $('button[type="submit"]').html('<i class="fas fa-save me-1"></i>Actualizar');
                } else {
                    mensaje(r.mensaje, 'danger');
                }
            }, 'json').fail(function() {
                mensaje('Error al cargar datos del usuario', 'danger');
            });
        }
    });
    
    // Cambiar estado
    $(document).on('click', '.btn-toggle', function () {
        const $btn = $(this);
        const id = $btn.data('id');
        const estado = $btn.data('estado'); 
        const texto = estado === 'activar' ? 'activar' : 'desactivar';

        if (!confirm(`¿Confirmar que deseas ${texto} este registro?`)) return;

        let url = '';
        let reloadFn = null;

        if ($('#productoForm').length > 0) {
            url = '../controles/ajax_productos.php';
            reloadFn = cargarTablaProductos;
        } else if ($('#proveedorForm').length > 0) {
            url = '../controles/ajax_proveedores.php';
            reloadFn = cargarTabla;
        } else if ($('#rolForm').length > 0) {
            url = '../controles/ajax_roles.php';
            reloadFn = cargarTablaRoles;
        } else if ($('#paqueteForm').length > 0) {
            url = '../controles/ajax_paquetes.php';
            reloadFn = cargarTablaPaquetes;
        } else if ($('#usuarioForm').length > 0) {
            url = '../controles/ajax_usuarios.php';
            reloadFn = cargarTablaUsuarios;
        }

        if (!url) {
            console.warn('No se detectó formulario válido para cambio de estado.');
            return;
        }

        $.post(url, {
            accion: 'cambiar_estado',
            id: id,
            estado: estado
        }, function (r) {
            mensaje(r.mensaje, r.success ? 'success' : 'danger');
            if (r.success && typeof reloadFn === 'function') {
                reloadFn();
                // Cambiar el data-estado localmente para evitar doble clic con mismo valor
                $btn.data('estado', estado === 'activar' ? 'desactivar' : 'activar');
            }
        }, 'json').fail(function () {
            mensaje(`Error al cambiar estado`, 'danger');
        });
    });

    // Inicializar paginación al cargar la página
    inicializarSistemaPaginacion();
});

/* SISTEMA DE PAGINACIÓN MEJORADO */

// Variable global para controlar la inicialización - protegida contra redeclaración
if (typeof window.sistemaInicializado === 'undefined') {
    window.sistemaInicializado = false;
}

// Configuración de tablas con paginación - protegida contra redeclaración
if (typeof window.TABLAS_CONFIG === 'undefined') {
    window.TABLAS_CONFIG = {
        'proveedores': {
            url: '../controles/ajax_proveedores.php',
            containerId: '#tabla-proveedores'
        },
        'productos': {
            url: '../controles/ajax_productos.php',
            containerId: '#tabla-productos'
        },
        'usuarios': {
            url: '../controles/ajax_usuarios.php',
            containerId: '#tabla-usuarios'
        },
        'roles': {
            url: '../controles/ajax_roles.php',
            containerId: '#tabla-roles'
        },
        'paquetes': {
            url: '../controles/ajax_paquetes.php',
            containerId: '#tabla-paquetes'
        },
        'auditorias': {
            url: '../controles/ajax_auditorias.php',
            containerId: '#tabla-auditorias'
        }
    };
}

// Función principal de inicialización del sistema de paginación
function inicializarSistemaPaginacion() {
    console.log('Inicializando sistema de paginación...');
    
    // Detectar el tipo de tabla actual
    const tipoTabla = detectarTipoTabla();
    console.log('Tipo de tabla detectado:', tipoTabla);
    
    if (tipoTabla) {
        // Asegurar que los controles de paginación estén presentes
        garantizarControlesPaginacion(tipoTabla);
        
        // Configurar event listeners
        configurarEventListeners();
        
        // Cargar la tabla inicial
        cargarTablaConPaginacion(tipoTabla, 1);
        
        window.sistemaInicializado = true;
        console.log('Sistema de paginación inicializado correctamente');
    } else {
        console.log('No se detectó ninguna tabla válida');
    }
}

// Función mejorada para garantizar que los controles existan
function garantizarControlesPaginacion(tipoTabla) {
    const config = window.TABLAS_CONFIG[tipoTabla];
    if (!config) return;
    
    const tableContainer = $(config.containerId).closest('.table-container');
    
    if (tableContainer.length > 0) {
        // Verificar si los controles ya existen
        let controlsContainer = tableContainer.prev('.pagination-controls');
        
        if (controlsContainer.length === 0) {
            // Crear los controles si no existen
            console.log('Creando controles de paginación para:', tipoTabla);
            tableContainer.before(`
                <div class="d-flex justify-content-between align-items-center mb-2 pagination-controls">
                    <div>
                        Mostrar 
                        <select id="filasPorPagina" class="form-select d-inline-block w-auto">
                            <option value="5">5</option>
                            <option value="10" selected>10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        registros
                    </div>
                    <div id="paginacion"></div>
                </div>
            `);
        } else {
            console.log('Los controles de paginación ya existen para:', tipoTabla);
        }
        
        // Asegurar que el selector de filas tenga el valor correcto
        if ($('#filasPorPagina').length > 0 && !$('#filasPorPagina').val()) {
            $('#filasPorPagina').val('10');
        }
    }
}

// Configurar event listeners con namespace para evitar duplicados
function configurarEventListeners() {
    // Remover eventos anteriores para evitar duplicados
    $(document).off('change.paginacion').off('click.paginacion');
    
    // Cambio en selector de filas por página
    $(document).on('change.paginacion', '#filasPorPagina', function() {
        console.log('Cambio en filas por página:', $(this).val());
        const tipo = detectarTipoTabla();
        if (tipo) {
            cargarTablaConPaginacion(tipo, 1);
        }
    });

    // Click en enlaces de paginación
    $(document).on('click.paginacion', '.pagina-link', function(e) {
        e.preventDefault();
        const pagina = $(this).data('pagina');
        console.log('Click en página:', pagina);
        const tipo = detectarTipoTabla();
        if (tipo && pagina) {
            cargarTablaConPaginacion(tipo, pagina);
        }
    });
    
    console.log('Event listeners configurados correctamente');
}

// Función genérica para cargar tabla con paginación mejorada
function cargarTablaConPaginacion(tipoTabla, pagina = 1) {
    const config = window.TABLAS_CONFIG[tipoTabla];
    if (!config) {
        console.error(`Configuración no encontrada para tabla: ${tipoTabla}`);
        return;
    }

    console.log(`Cargando tabla ${tipoTabla}, página ${pagina}`);
    
    // Asegurar que los controles existan antes de cargar
    garantizarControlesPaginacion(tipoTabla);
    
    const filas = $('#filasPorPagina').val() || 10;
    const container = $(config.containerId);
    
    // Mostrar indicador de carga
    if (container.length > 0) {
        const colspan = container.closest('table').find('thead th').length || 7;
        container.html(`<tr><td colspan="${colspan}" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>`);
    }
    
    $.get(config.url, {
        accion: 'cargar_tabla',
        pagina: pagina,
        filas: filas
    })
    .done(function(html) {
        console.log(`Tabla ${tipoTabla} cargada exitosamente`);
        container.html(html);
        
        // Asegurar que la paginación se muestre después de cargar los datos
        setTimeout(function() {
            if ($('#paginacion').children().length === 0) {
                console.log('La paginación no se cargó, reintentando...');
                // Si la paginación no se cargó via script en la respuesta AJAX,
                // podríamos necesitar una llamada adicional o manejarla diferente
            }
        }, 100);
    })
    .fail(function(xhr, status, error) {
        console.error(`Error al cargar tabla ${tipoTabla}:`, status, error);
        const colspan = container.closest('table').find('thead th').length || 7;
        container.html(`<tr><td colspan="${colspan}" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Error al cargar datos</td></tr>`);
        mensaje(`Error al cargar tabla de ${tipoTabla}`, 'danger');
    });
}

// Detectar automáticamente qué tipo de tabla estamos manejando
function detectarTipoTabla() {
    for (const [tipo, config] of Object.entries(window.TABLAS_CONFIG)) {
        if ($(config.containerId).length > 0) {
            return tipo;
        }
    }
    return null;
}

// Funciones de carga específicas (mantienen compatibilidad con código existente)
function cargarTabla(pagina = 1) {
    console.log('cargarTabla() llamada - redirigiendo a sistema genérico');
    // Re-inicializar sistema si es necesario
    if (!window.sistemaInicializado) {
        inicializarSistemaPaginacion();
    } else {
        cargarTablaConPaginacion('proveedores', pagina);
    }
}

function cargarTablaUsuarios(pagina = 1) {
    console.log('cargarTablaUsuarios() llamada');
    if (!window.sistemaInicializado) {
        inicializarSistemaPaginacion();
    } else {
        cargarTablaConPaginacion('usuarios', pagina);
    }
}

function cargarTablaProductos(pagina = 1) {
    console.log('cargarTablaProductos() llamada');
    if (!window.sistemaInicializado) {
        inicializarSistemaPaginacion();
    } else {
        cargarTablaConPaginacion('productos', pagina);
    }
}

function cargarTablaRoles(pagina = 1) {
    console.log('cargarTablaRoles() llamada');
    if (!window.sistemaInicializado) {
        inicializarSistemaPaginacion();
    } else {
        cargarTablaConPaginacion('roles', pagina);
    }
}

function cargarTablaPaquetes(pagina = 1) {
    console.log('cargarTablaPaquetes() llamada');
    if (!window.sistemaInicializado) {
        inicializarSistemaPaginacion();
    } else {
        cargarTablaConPaginacion('paquetes', pagina);
    }
}

function cargarTablaAuditorias(pagina = 1) {
    console.log('cargarTablaAuditorias() llamada');
    if (!window.sistemaInicializado) {
        inicializarSistemaPaginacion();
    } else {
        cargarTablaConPaginacion('auditorias', pagina);
    }
}

// Función para reinicializar el sistema cuando se cambia de formulario
function reinicializarPaginacion() {
    console.log('Reinicializando sistema de paginación...');
    window.sistemaInicializado = false;
    
    // Limpiar event listeners anteriores
    $(document).off('change.paginacion').off('click.paginacion');
    
    // Volver a inicializar
    setTimeout(function() {
        inicializarSistemaPaginacion();
    }, 100);
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
    } else if ($('#usuarioForm').length > 0) {
        limpiarUsuario();
    }
}

function limpiarUsuario() {
    $('#usuarioForm')[0].reset();
    $('input[name="accion"]').val('crear');
    $('input[name="id_usuario"]').remove();
    $('.form-container h3').text('Nuevo Usuario');
    $('button[type="submit"]').html('<i class="fas fa-save me-1"></i>Guardar');
    $('.password-info').remove();
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
                    mensaje('El RUC debe contener 10 dígitos', 'danger');
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

// Función para ser llamada cuando se cambia de formulario
// Esta función debe ser llamada desde tu sistema de navegación
window.onFormularioChange = function() {
    console.log('Detectado cambio de formulario');
    reinicializarPaginacion();
};

// También detectar cambios automáticamente usando MutationObserver - protegido contra redeclaración
if (typeof window.formulariosObserver === 'undefined') {
    window.formulariosObserver = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                // Verificar si se agregaron nuevas tablas
                const addedNodes = Array.from(mutation.addedNodes);
                const hasTableContainer = addedNodes.some(node => 
                    node.nodeType === Node.ELEMENT_NODE && 
                    (node.classList?.contains('table-container') || 
                     node.querySelector?.('.table-container'))
                );
                
                if (hasTableContainer) {
                    console.log('Nueva tabla detectada, reinicializando paginación...');
                    setTimeout(function() {
                        reinicializarPaginacion();
                    }, 200);
                }
            }
        });
    });

    // Observar cambios en el DOM
    window.formulariosObserver.observe(document.body, {
        childList: true,
        subtree: true
    });
}

// FUNCIONES LEGACY MANTENIDAS PARA COMPATIBILIDAD

// Función original de inicialización (mantenida para compatibilidad)
function inicializarPaginacionGenerica() {
    console.log('inicializarPaginacionGenerica() llamada - redirigiendo a nuevo sistema');
    inicializarSistemaPaginacion();
}

// Crear controles de paginación dinámicamente (legacy)
function crearControlesPaginacion(tipoTabla) {
    console.log('crearControlesPaginacion() llamada - usando nueva implementación');
    garantizarControlesPaginacion(tipoTabla);
}

/* SISTEMA DE EXPORTACIÓN PDF */

// Función genérica para exportar tabla a PDF
function exportarTablaPDF() {
    // Detectar qué tipo de tabla tenemos
    const tipoTabla = detectarTipoTabla();
    if (!tipoTabla) {
        mensaje('No se pudo detectar el tipo de tabla para exportar', 'danger');
        return;
    }

    // Configuración específica para cada tipo de tabla
    const configuraciones = {
        'proveedores': {
            titulo: 'Reporte de Proveedores',
            url: '../controles/ajax_proveedores.php',
            columnas: ['ID', 'Nombre', 'RUC', 'Email', 'Teléfono', 'Estado', 'Dirección'],
            campos: ['id_proveedor', 'nombre', 'ruc', 'correo', 'telefono', 'estado', 'direccion']
        },
        'productos': {
            titulo: 'Reporte de Productos',
            url: '../controles/ajax_productos.php',
            columnas: ['ID', 'Nombre', 'Descripción', 'Precio', 'Stock', 'Categoría', 'Proveedor', 'Estado'],
            campos: ['id_producto', 'nombre', 'descripcion', 'precio_unitario', 'cantidad_disponible', 'categoria', 'proveedor_nombre', 'estado']
        },
        'usuarios': {
            titulo: 'Reporte de Usuarios',
            url: '../controles/ajax_usuarios.php',
            columnas: ['ID', 'Nombre', 'Email', 'Rol', 'Estado', 'Fecha Registro'],
            campos: ['id_usuario', 'nombre', 'correo', 'rol_nombre', 'estado', 'fecha_creacion']
        },
        'roles': {
            titulo: 'Reporte de Roles y Permisos',
            url: '../controles/ajax_roles.php',
            columnas: ['ID', 'Nombre del Rol', 'Descripción', 'Permisos', 'Estado'],
            campos: ['id_rol', 'nombre_rol', 'descripcion', 'permisos_lista', 'estado']
        },
        'paquetes': {
            titulo: 'Reporte de Paquetes',
            url: '../controles/ajax_paquetes.php',
            columnas: ['ID', 'Tipo de Evento', 'Proveedor', 'Total Productos', 'Fecha Creación', 'Estado'],
            campos: ['id_paquete', 'tipo_evento', 'proveedor_nombre', 'total_productos', 'fecha_creacion', 'estado']
        },
        'auditorias': {
            titulo: 'Reporte de Auditorías',
            url: '../controles/ajax_auditorias.php',
            columnas: ['ID Log', 'Usuario', 'Tabla', 'Valor Anterior', 'Nuevo Valor', 'Fecha'],
            campos: ['id_auditoria', 'id_usuario', 'tabla_afectada', 'valor_anterior_corto', 'valor_nuevo_corto', 'fecha_cambio']
        }
    };

    const config = configuraciones[tipoTabla];
    if (!config) {
        mensaje('Configuración de exportación no encontrada', 'danger');
        return;
    }

    // Mostrar indicador de carga
    const btnExportar = $('#btn-exportar-pdf');
    const textoOriginal = btnExportar.html();
    btnExportar.html('<i class="fas fa-spinner fa-spin"></i> Generando PDF...').prop('disabled', true);

    // Obtener todos los datos para exportar
    $.get(config.url, {
        accion: 'exportar_pdf',
        todos: true
    })
    .done(function(response) {
        try {
            let datos;
            if (typeof response === 'string') {
                datos = JSON.parse(response);
            } else {
                datos = response;
            }

            if (datos.success && datos.data) {
                generarPDF(config, datos.data);
            } else {
                mensaje('Error al obtener datos para exportar: ' + (datos.mensaje || 'Error desconocido'), 'danger');
            }
        } catch (error) {
            console.error('Error al procesar respuesta:', error);
            mensaje('Error al procesar los datos para exportar', 'danger');
        }
    })
    .fail(function() {
        mensaje('Error de conexión al obtener datos para exportar', 'danger');
    })
    .always(function() {
        btnExportar.html(textoOriginal).prop('disabled', false);
    });
}

// Función para generar el PDF con jsPDF - VERSIÓN SIMPLE
function generarPDF(config, datos) {
    try {
        // Usar window.jspdf.jsPDF como en el gestionar_paquete que funcionaba
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF('l', 'mm', 'a4'); // Landscape para más espacio

        // Configurar fuente
        doc.setFont('helvetica');

        // Título del documento
        doc.setFontSize(16);
        doc.setTextColor(40, 40, 40);
        doc.text(config.titulo, 20, 20);

        // Fecha de generación
        doc.setFontSize(10);
        doc.setTextColor(100, 100, 100);
        const fechaActual = new Date().toLocaleString('es-ES');
        doc.text(`Generado el: ${fechaActual}`, 20, 30);

        // Preparar datos para la tabla
        const filas = datos.map(fila => {
            return config.campos.map(campo => {
                let valor = fila[campo] || '';
                
                // Formatear valores específicos
                if (campo === 'precio_unitario' && valor) {
                    valor = '$' + parseFloat(valor).toFixed(2);
                } else if (campo === 'fecha_creacion' && valor) {
                    valor = new Date(valor).toLocaleDateString('es-ES');
                } else if (campo === 'fecha_cambio' && valor) {
                    valor = new Date(valor).toLocaleString('es-ES');
                } else if ((campo === 'valor_anterior_corto' || campo === 'valor_nuevo_corto') && valor) {
                    // Truncar valores largos para auditorías
                    valor = valor.length > 30 ? valor.substring(0, 30) + '...' : valor;
                } else if (campo === 'estado' && valor) {
                    valor = valor.charAt(0).toUpperCase() + valor.slice(1);
                }
                
                return String(valor);
            });
        });

        console.log(`Generando PDF con ${datos.length} registros`); // Debug

        // Generar tabla con autoTable
        doc.autoTable({
            head: [config.columnas],
            body: filas,
            startY: 40,
            styles: {
                fontSize: 8,
                cellPadding: 2,
            },
            headStyles: {
                fillColor: [41, 128, 185],
                textColor: 255,
                fontStyle: 'bold'
            },
            alternateRowStyles: {
                fillColor: [245, 245, 245]
            },
            margin: { top: 40, left: 20, right: 20 }
        });

        // Agregar número de página en el pie
        const pageCount = doc.internal.getNumberOfPages();
        for (let i = 1; i <= pageCount; i++) {
            doc.setPage(i);
            doc.setFontSize(8);
            doc.setTextColor(150);
            doc.text(`Página ${i} de ${pageCount}`, doc.internal.pageSize.getWidth() - 40, doc.internal.pageSize.getHeight() - 10);
        }

        // Descargar el PDF
        const nombreArchivo = `${config.titulo.toLowerCase().replace(/\s+/g, '_')}_${new Date().toISOString().slice(0, 10)}.pdf`;
        doc.save(nombreArchivo);

        mensaje(`PDF generado exitosamente con ${datos.length} registros`, 'success');

    } catch (error) {
        console.error('Error al generar PDF:', error);
        mensaje('Error al generar el archivo PDF: ' + error.message, 'danger');
    }
}