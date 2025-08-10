<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../index.html");
    exit();
}

$permisos_usuario = isset($_SESSION['usuario']['permisos']) ? $_SESSION['usuario']['permisos'] : [];
$tiene_permiso = in_array('registrar_proveedor', $permisos_usuario) || 
                 in_array('crud_productos', $permisos_usuario) ||
                 (isset($_SESSION['usuario']['rol_nombre']) && $_SESSION['usuario']['rol_nombre'] == 'administrador');

if (!$tiene_permiso) {
    header("Location: ../../includes/dashboard.php");
    exit();
}
require_once '../../config/database.php';
$proveedores_result = $conn->query("SELECT * FROM proveedor ORDER BY fecha_creacion DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Proveedores</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="../recursos/css/forms.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="main-title">
                <i class="fas fa-truck"></i> Gestión de Proveedores
            </h1>
        </div>
    </div>
    
    <!-- Formulario -->
    <div class="row">
        <div class="col-12">
            <div class="form-container">
                <h3 class="mb-4" style="color: var(--primary-blue);">Nuevo Proveedor</h3>
                
                <form id="proveedorForm">
                    <input type="hidden" name="accion" value="crear">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-building me-2"></i>Nombre <span class="required">*</span>
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="ruc" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-id-card me-2"></i>RUC <span class="required">*</span>
                            </label>
                            <input type="text" class="form-control" id="ruc" name="ruc" pattern="[0-9]{10,13}" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-envelope me-2"></i>Email <span class="required">*</span>
                            </label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="telefono" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-phone me-2"></i>Teléfono <span class="required">*</span>
                            </label>
                            <input type="tel" class="form-control" id="telefono" name="telefono" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-12 mb-3">
                            <label for="direccion" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-map-marker-alt me-2"></i>Dirección <span class="required">*</span>
                            </label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="3" required></textarea>
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
                                <th>ID</th><th>Nombre</th><th>RUC</th><th>Email</th><th>Teléfono</th><th>Estado</th><th>Dirección</th><th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-proveedores">
                            <?php if ($proveedores_result && $proveedores_result->num_rows > 0): ?>
                                <?php while ($p = $proveedores_result->fetch_assoc()): ?>
                                    <?php 
                                        $badge = $p['estado'] === 'activo' ? 'success' : 'danger';
                                        $toggle_action = $p['estado'] === 'activo' ? 'inactivo' : 'activar';
                                        $toggle_icon = $p['estado'] === 'activo' ? 'ban' : 'check';
                                        $toggle_class = $p['estado'] === 'activo' ? 'btn-danger' : 'btn-success';
                                    ?>
                                    <tr>
                                        <td><?php echo $p['id_proveedor']; ?></td>
                                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                                        <td><?php echo htmlspecialchars($p['ruc']); ?></td>
                                        <td><?php echo htmlspecialchars($p['correo']); ?></td>
                                        <td><?php echo htmlspecialchars($p['telefono']); ?></td>
                                        <td><span class="badge bg-<?php echo $badge; ?>"><?php echo ucfirst($p['estado']); ?></span></td>
                                        <td><?php echo htmlspecialchars($p['direccion']); ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-edit btn-editar" data-id="<?php echo $p['id_proveedor']; ?>" title="Editar">
                                                    <i class="fas fa-edit fa-fw"></i>
                                                </button>
                                                <button class="btn <?php echo $toggle_class; ?> btn-toggle" data-id="<?php echo $p['id_proveedor']; ?>" data-estado="<?php echo $toggle_action; ?>" title="<?php echo ucfirst($toggle_action); ?>">
                                                    <i class="fas fa-<?php echo $toggle_icon; ?> fa-fw"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-3"></i><br>
                                        No hay proveedores registrados
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
</body>
</html>