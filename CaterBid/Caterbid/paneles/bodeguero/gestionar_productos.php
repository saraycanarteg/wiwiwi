<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../index.html");
    exit();
}

$permisos_usuario = isset($_SESSION['usuario']['permisos']) ? $_SESSION['usuario']['permisos'] : [];
$tiene_permiso = in_array('gestionar_productos', $permisos_usuario) || 
                 in_array('registrar_paquete', $permisos_usuario) || 
                 in_array('crud_productos', $permisos_usuario) ||
                 (isset($_SESSION['usuario']['rol_nombre']) && $_SESSION['usuario']['rol_nombre'] == 'bodeguero');

if (!$tiene_permiso) {
    header("Location: ../../includes/dashboard.php");
    exit();
}
require_once '../../config/database.php';
$productos_result = $conn->query("
    SELECT p.*, pr.nombre as proveedor_nombre 
    FROM producto p 
    LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor 
    ORDER BY p.fecha_creacion DESC
");

// Obtener proveedores para el selector
$proveedores_select = $conn->query("SELECT id_proveedor, nombre FROM proveedor WHERE estado = 'activo' ORDER BY nombre ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="../recursos/css/forms.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="main-title">
                <i class="fas fa-box"></i> Gestión de Productos
            </h1>
        </div>
    </div>
    
    <!-- Formulario -->
    <div class="row">
        <div class="col-12">
            <div class="form-container">
                <h3 class="mb-4" style="color: var(--primary-blue);">Nuevo Producto</h3>
                
                <form id="productoForm">
                    <input type="hidden" name="accion" value="crear">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-tag me-2"></i>Nombre <span class="required">*</span>
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="descripcion" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-align-left me-2"></i>Descripción <span class="required">*</span>
                            </label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="precio_unitario" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-dollar-sign me-2"></i>Precio Unitario <span class="required">*</span>
                            </label>
                            <input type="number" class="form-control" id="precio_unitario" name="precio_unitario" 
                                   min="0" step="0.01" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="cantidad_disponible" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-boxes me-2"></i>Stock <span class="required">*</span>
                            </label>
                            <input type="number" class="form-control" id="cantidad_disponible" name="cantidad_disponible" 
                                   min="0" required>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="categoria" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-list me-2"></i>Categoría <span class="required">*</span>
                            </label>
                            <select class="form-select" id="categoria" name="categoria" required>
                                <option value="">Seleccionar categoría...</option>
                                <option value="Comida">Comida</option>
                                <option value="Bebidas">Bebidas</option>
                                <option value="Menaje y utensilios">Menaje y utensilios</option>
                                <option value="Equipos y mobiliario">Equipos y mobiliario</option>
                                <option value="Personal y servicios">Personal y servicios</option>
                                <option value="Decoración y ambientación">Decoración y ambientación</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_proveedor" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-truck me-2"></i>Proveedor <span class="required">*</span>
                            </label>
                            <select class="form-select" id="id_proveedor" name="id_proveedor" required>
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
    
    <!-- Tabla -->
    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio Unitario</th>
                                <th>Stock</th>
                                <th>Categoría</th>
                                <th>Proveedor</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-productos">
                            <?php if ($productos_result && $productos_result->num_rows > 0): ?>
                                <?php while ($p = $productos_result->fetch_assoc()): ?>
                                    <?php 
                                        $badge = $p['estado'] === 'activo' ? 'success' : 'danger';
                                        $toggle_action = $p['estado'] === 'activo' ? 'inactivo' : 'activar';
                                        $toggle_icon = $p['estado'] === 'activo' ? 'ban' : 'check';
                                        $toggle_class = $p['estado'] === 'activo' ? 'btn-danger' : 'btn-success';
                                    ?>
                                    <tr>
                                        <td><?php echo $p['id_producto']; ?></td>
                                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($p['descripcion']); ?></td>
                                        <td>$<?php echo number_format($p['precio_unitario'], 2); ?></td>
                                        <td><?php echo $p['cantidad_disponible']; ?></td>
                                        <td><?php echo htmlspecialchars($p['categoria']); ?></td>
                                        <td>
                                            <?php if ($p['proveedor_nombre']): ?>
                                                <span class="text-muted">#<?php echo $p['id_proveedor']; ?></span><br>
                                                <?php echo htmlspecialchars($p['proveedor_nombre']); ?>
                                            <?php else: ?>
                                                <span class="text-muted">Sin proveedor</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($p['estado']); ?></span></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-edit btn-editar" data-id="<?php echo $p['id_producto']; ?>" title="Editar">
                                                    <i class="fas fa-edit fa-fw"></i>
                                                </button>
                                                <button class="btn <?php echo $toggle_class; ?> btn-toggle" data-id="<?php echo $p['id_producto']; ?>" data-estado="<?php echo $toggle_action; ?>" title="<?php echo ucfirst($toggle_action); ?>">
                                                    <i class="fas fa-<?php echo $toggle_icon; ?> fa-fw"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-3"></i><br>
                                        No hay productos registrados
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="../recursos/js/formularios.js"></script>
<script src="../recursos/js/validaciones.js"></script>
</body>
</html>