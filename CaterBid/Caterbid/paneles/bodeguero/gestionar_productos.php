<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../index.html");
    exit();
}

require_once '../../includes/verificar_permisos.php';
requierePermiso('gestionar_productos');
require_once '../../config/database.php';

// Remover la consulta directa, ahora se carga via AJAX
// $productos_result = $conn->query("..."));

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
    
    <!-- Botón de exportación -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="d-flex justify-content-end">
                <button type="button" class="btn btn-danger" id="btn-exportar-pdf" onclick="exportarTablaPDF()">
                    <i class="fas fa-file-pdf me-1"></i>Exportar PDF
                </button>
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
                            <!-- Los datos se cargan via AJAX -->
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i><br>
                                    Cargando productos...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<!-- Agregar librerías para PDF -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="../recursos/js/formularios.js"></script>
<script src="../recursos/js/validaciones.js"></script>
</body>
</html>