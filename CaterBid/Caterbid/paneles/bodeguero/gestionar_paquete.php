<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../index.html");
    exit();
}

$permisos_usuario = isset($_SESSION['usuario']['permisos']) ? $_SESSION['usuario']['permisos'] : [];
$tiene_permiso = in_array('gestion_paquete', $permisos_usuario) || 
                 in_array('crud_paquetes', $permisos_usuario) ||
                 (isset($_SESSION['usuario']['rol_nombre']) && $_SESSION['usuario']['rol_nombre'] == 'bodeguero');

if (!$tiene_permiso) {
    header("Location: ../../includes/dashboard.php");
    exit();
}

require_once '../../config/database.php';

// Obtener paquetes con información de proveedor
$paquetes_result = $conn->query("
    SELECT p.*, pr.nombre as proveedor_nombre,
           COUNT(pp.id_producto) as total_productos,
           COALESCE(SUM(pp.cantidad_producto), 0) as total_cantidad
    FROM paquete p 
    LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor 
    LEFT JOIN paquete_producto pp ON p.id_paquete = pp.id_paquete
    GROUP BY p.id_paquete, p.id_proveedor, p.tipo_evento, p.fecha_creacion, p.estado, pr.nombre
    ORDER BY p.fecha_creacion DESC
");

// Obtener proveedores activos
$proveedores_select = $conn->query("SELECT id_proveedor, nombre FROM proveedor WHERE estado = 'activo' ORDER BY nombre ASC");

// Tipos de eventos organizados por categorías
$tipos_eventos = [
    'Eventos sociales y familiares' => [
        'Bodas',
        'Cumpleaños',
        'Quinceañeros',
        'Bautizos',
        'Aniversarios',
        'Baby showers',
        'Graduaciones'
    ],
    'Eventos corporativos y empresariales' => [
        'Conferencias y seminarios',
        'Lanzamientos de productos',
        'Cenas corporativas',
        'Fiestas de fin de año empresariales',
        'Team building',
        'Ferias comerciales'
    ],
    'Eventos institucionales y comunitarios' => [
        'Eventos escolares',
        'Eventos benéficos',
        'Eventos deportivos',
        'Actividades religiosas',
        'Festivales culturales'
    ],
    'Otros eventos especiales' => [
        'Eventos temáticos',
        'Sesiones fotográficas',
        'Catering para producciones'
    ]
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Paquetes</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="../recursos/css/forms.css" rel="stylesheet">
    <style>
        .producto-checkbox {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }
        .producto-checkbox:hover {
            border-color: #007bff;
            background-color: #acb3bbff;
        }
        .producto-checkbox.selected {
            border-color: #007bff;
            background-color: #e3f2fd;
        }
        .productos-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
        }
        .cantidad-input {
            width: 80px;
            display: inline-block;
        }
        #productos-section {
            display: none;
        }
    </style>
</head>
<body>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="main-title">
                <i class="fas fa-box-open"></i> Gestión de Paquetes
            </h1>
        </div>
    </div>
    
    <!-- Formulario -->
    <div class="row">
        <div class="col-12">
            <div class="form-container">
                <h3 class="mb-4" style="color: var(--primary-blue);">Nuevo Paquete</h3>
                
                <form id="paqueteForm">
                    <input type="hidden" name="accion" value="crear">
                    
                    <!-- Tipo de Evento -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="categoria_evento" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-calendar me-2"></i>Categoría de Evento <span class="required">*</span>
                            </label>
                            <select class="form-select" id="categoria_evento" name="categoria_evento" required onchange="cargarTiposEvento()">
                                <option value="">Seleccionar categoría...</option>
                                <?php foreach ($tipos_eventos as $categoria => $tipos): ?>
                                    <option value="<?php echo htmlspecialchars($categoria); ?>">
                                        <?php echo htmlspecialchars($categoria); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-5 mb-3">
                            <label for="tipo_evento" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-star me-2"></i>Tipo de Evento <span class="required">*</span>
                            </label>
                            <select class="form-select" id="tipo_evento" name="tipo_evento" required disabled>
                                <option value="">Primero selecciona una categoría...</option>
                            </select>
                        </div>
                        
                        <div class="col-md-1 mb-3">
                            <label class="form-label" style="color: var(--primary-blue);">&nbsp;</label>
                            <button type="button" class="btn btn-outline-primary w-100" data-bs-toggle="modal" data-bs-target="#modalNuevoTipo" title="Agregar nuevo tipo">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Proveedor -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_proveedor" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-truck me-2"></i>Proveedor <span class="required">*</span>
                            </label>
                            <select class="form-select" id="id_proveedor" name="id_proveedor" required onchange="cargarProductos()">
                                <option value="">Seleccionar proveedor...</option>
                                <?php if ($proveedores_select && $proveedores_select->num_rows > 0): ?>
                                    <?php while ($proveedor = $proveedores_select->fetch_assoc()): ?>
                                        <option value="<?php echo $proveedor['id_proveedor']; ?>">
                                            <?php echo htmlspecialchars($proveedor['nombre']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Sección de Productos -->
                    <div id="productos-section">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label class="form-label" style="color: var(--primary-blue);">
                                    <i class="fas fa-boxes me-2"></i>Productos del Paquete <span class="required">*</span>
                                </label>
                                <div class="productos-container" id="productos-container">
                                    <div class="text-center py-4">
                                        <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
                                        <p class="mt-2 text-muted">Cargando productos...</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary me-2">
                                <i class="fas fa-save me-1"></i>Guardar
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="limpiar()">
                                <i class="fas fa-eraser me-1"></i>Limpiar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Tabla de Paquetes -->
    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tipo de Evento</th>
                                <th>Proveedor</th>
                                <th>Productos</th>
                                <th>Fecha Creación</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-paquetes">
                            <?php if ($paquetes_result && $paquetes_result->num_rows > 0): ?>
                                <?php while ($p = $paquetes_result->fetch_assoc()): ?>
                                    <?php 
                                        $badge = $p['estado'] === 'activo' ? 'success' : 'danger';
                                        $toggle_action = $p['estado'] === 'activo' ? 'inactivo' : 'activar';
                                        $toggle_icon = $p['estado'] === 'activo' ? 'ban' : 'check';
                                        $toggle_class = $p['estado'] === 'activo' ? 'btn-danger' : 'btn-success';
                                    ?>
                                    <tr>
                                        <td><?php echo $p['id_paquete']; ?></td>
                                        <td><?php echo htmlspecialchars($p['tipo_evento']); ?></td>
                                        <td>
                                            <?php if ($p['proveedor_nombre']): ?>
                                                <span class="text-muted">#<?php echo $p['id_proveedor']; ?></span><br>
                                                <?php echo htmlspecialchars($p['proveedor_nombre']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin proveedor</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-info"><?php echo $p['total_productos']; ?> productos</span>
                                            <?php if ($p['total_cantidad'] > 0): ?>
                                                <br><small class="text-muted"><?php echo $p['total_cantidad']; ?> unidades total</small>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d/m/Y', strtotime($p['fecha_creacion'])); ?></td>
                                        <td><span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($p['estado']); ?></span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-info btn-ver" data-id="<?php echo $p['id_paquete']; ?>" title="Ver detalles">
                                                    <i class="fas fa-eye fa-fw"></i>
                                                </button>
                                                <button class="btn btn-edit btn-editar" data-id="<?php echo $p['id_paquete']; ?>" title="Editar">
                                                    <i class="fas fa-edit fa-fw"></i>
                                                </button>
                                                <button class="btn <?php echo $toggle_class; ?> btn-toggle" data-id="<?php echo $p['id_paquete']; ?>" data-estado="<?php echo $toggle_action; ?>" title="<?php echo ucfirst($toggle_action); ?>">
                                                    <i class="fas fa-<?php echo $toggle_icon; ?> fa-fw"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-3"></i><br>
                                        No hay paquetes registrados
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para Nuevo Tipo de Evento -->
<div class="modal fade" id="modalNuevoTipo" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-plus-circle me-2"></i>Agregar Nuevo Tipo de Evento
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="formNuevoTipo">
                    <div class="mb-3">
                        <label for="nueva_categoria" class="form-label">Categoría</label>
                        <select class="form-select" id="nueva_categoria" name="nueva_categoria" required>
                            <option value="">Seleccionar categoría...</option>
                            <?php foreach ($tipos_eventos as $categoria => $tipos): ?>
                                <option value="<?php echo htmlspecialchars($categoria); ?>">
                                    <?php echo htmlspecialchars($categoria); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="nuevo_tipo" class="form-label">Nombre del Tipo de Evento</label>
                        <input type="text" class="form-control" id="nuevo_tipo" name="nuevo_tipo" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="guardarNuevoTipo()">
                    <i class="fas fa-save me-1"></i>Guardar
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="../recursos/js/formularios.js"></script>

<script>
// Array de tipos de eventos desde PHP
const tiposEventos = <?php echo json_encode($tipos_eventos); ?>;

function cargarTiposEvento() {
    const categoria = document.getElementById('categoria_evento').value;
    const tipoSelect = document.getElementById('tipo_evento');
    
    // Limpiar opciones anteriores
    tipoSelect.innerHTML = '<option value="">Seleccionar tipo de evento...</option>';
    
    if (categoria && tiposEventos[categoria]) {
        tipoSelect.disabled = false;
        
        tiposEventos[categoria].forEach(function(tipo) {
            const option = document.createElement('option');
            option.value = tipo;
            option.textContent = tipo;
            tipoSelect.appendChild(option);
        });
    } else {
        tipoSelect.disabled = true;
        tipoSelect.innerHTML = '<option value="">Primero selecciona una categoría...</option>';
    }
}

function cargarProductos() {
    const proveedorId = document.getElementById('id_proveedor').value;
    const productosSection = document.getElementById('productos-section');
    const productosContainer = document.getElementById('productos-container');
    
    if (!proveedorId) {
        productosSection.style.display = 'none';
        return;
    }
    
    // Mostrar sección de productos
    productosSection.style.display = 'block';
    
    // Mostrar indicador de carga
    productosContainer.innerHTML = `
        <div class="text-center py-4">
            <i class="fas fa-spinner fa-spin fa-2x text-muted"></i>
            <p class="mt-2 text-muted">Cargando productos del proveedor...</p>
        </div>
    `;
    
    // Llamada AJAX para cargar productos
    $.ajax({
        url: '../controles/ajax_paquetes.php',
        method: 'POST',
        data: {
            accion: 'cargar_productos',
            id_proveedor: proveedorId
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                if (response.productos.length === 0) {
                    productosContainer.innerHTML = `
                        <div class="text-center py-4">
                            <i class="fas fa-exclamation-circle fa-2x text-warning"></i>
                            <p class="mt-2 text-warning">${response.mensaje}</p>
                        </div>
                    `;
                } else {
                    mostrarProductos(response.productos);
                }
            } else {
                productosContainer.innerHTML = `
                    <div class="text-center py-4">
                        <i class="fas fa-exclamation-triangle fa-2x text-danger"></i>
                        <p class="mt-2 text-danger">Error al cargar productos</p>
                    </div>
                `;
            }
        },
        error: function() {
            productosContainer.innerHTML = `
                <div class="text-center py-4">
                    <i class="fas fa-times-circle fa-2x text-danger"></i>
                    <p class="mt-2 text-danger">Error de conexión al cargar productos</p>
                </div>
            `;
        }
    });
}

function mostrarProductos(productos) {
    const productosContainer = document.getElementById('productos-container');
    let html = '';
    
    // Agrupar por categoría
    const productosPorCategoria = {};
    productos.forEach(producto => {
        if (!productosPorCategoria[producto.categoria]) {
            productosPorCategoria[producto.categoria] = [];
        }
        productosPorCategoria[producto.categoria].push(producto);
    });
    
    // Generar HTML por categoría
    Object.keys(productosPorCategoria).forEach(categoria => {
        html += `
            <div class="mb-3">
                <h6 class="text-primary border-bottom pb-2">
                    <i class="fas fa-layer-group me-2"></i>${categoria}
                </h6>
        `;
        
        productosPorCategoria[categoria].forEach(producto => {
            html += `
                <div class="producto-checkbox" data-id="${producto.id_producto}">
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input me-3" type="checkbox" 
                               name="productos[]" value="${producto.id_producto}" 
                               id="producto_${producto.id_producto}"
                               onchange="toggleProducto(this, ${producto.id_producto})">
                        <div class="flex-grow-1">
                            <label class="form-check-label fw-bold" for="producto_${producto.id_producto}">
                                ${producto.nombre}
                            </label>
                            <div class="text-muted small">${producto.descripcion}</div>
                            <div class="row mt-2">
                                <div class="col-6">
                                    <small class="text-info">
                                        <i class="fas fa-dollar-sign"></i> ${parseFloat(producto.precio_unitario).toFixed(2)}
                                    </small>
                                </div>
                                <div class="col-6">
                                    <small class="text-success">
                                        <i class="fas fa-boxes"></i> Stock: ${producto.cantidad_disponible}
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="ms-3">
                            <label class="form-label small mb-1">Cantidad:</label>
                            <input type="number" class="form-control cantidad-input" 
                                   name="cantidades[${producto.id_producto}]"
                                   id="cantidad_${producto.id_producto}"
                                   min="1" max="${producto.cantidad_disponible}" 
                                   value="1" disabled>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
    });
    
    productosContainer.innerHTML = html;
}

function toggleProducto(checkbox, idProducto) {
    const cantidadInput = document.getElementById(`cantidad_${idProducto}`);
    const productoDiv = checkbox.closest('.producto-checkbox');
    
    if (checkbox.checked) {
        cantidadInput.disabled = false;
        productoDiv.classList.add('selected');
    } else {
        cantidadInput.disabled = true;
        cantidadInput.value = 1;
        productoDiv.classList.remove('selected');
    }
    
    // Validar que al menos un producto esté seleccionado
    validarSeleccionProductos();
}

function validarSeleccionProductos() {
    const productosSeleccionados = document.querySelectorAll('input[name="productos[]"]:checked');
    const submitBtn = document.querySelector('#paqueteForm button[type="submit"]');
    
    if (productosSeleccionados.length === 0) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>Selecciona al menos un producto';
    } else {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save me-1"></i>Guardar Paquete';
    }
}

function guardarNuevoTipo() {
    const categoria = document.getElementById('nueva_categoria').value;
    const tipo = document.getElementById('nuevo_tipo').value.trim();
    
    if (!categoria || !tipo) {
        mensaje('Complete todos los campos', 'danger');
        return;
    }
    
    // Agregar temporalmente al selector principal
    if (!tiposEventos[categoria]) {
        tiposEventos[categoria] = [];
    }
    
    if (!tiposEventos[categoria].includes(tipo)) {
        tiposEventos[categoria].push(tipo);
        
        // Actualizar selector si está en la misma categoría
        const categoriaActual = document.getElementById('categoria_evento').value;
        if (categoriaActual === categoria) {
            const tipoSelect = document.getElementById('tipo_evento');
            const option = document.createElement('option');
            option.value = tipo;
            option.textContent = tipo;
            tipoSelect.appendChild(option);
            tipoSelect.value = tipo; // Seleccionar automáticamente
        }
        
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevoTipo'));
        modal.hide();
        
        // Limpiar formulario del modal
        document.getElementById('formNuevoTipo').reset();
        
        mensaje('Tipo de evento agregado correctamente', 'success');
    } else {
        mensaje('Este tipo de evento ya existe en la categoría seleccionada', 'warning');
    }
}
</script>
</body>
</html>