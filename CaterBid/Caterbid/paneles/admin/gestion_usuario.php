<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../index.html");
    exit();
}

require_once '../../includes/verificar_permisos.php';
requierePermiso('gestion_usuario');
require_once '../../config/database.php';

// Obtener usuarios con información del rol
$usuarios_result = $conn->query("
    SELECT u.*, r.nombre_rol 
    FROM usuario u 
    LEFT JOIN rol r ON u.id_rol = r.id_rol 
    ORDER BY u.id_usuario ASC
");

// Obtener todos los roles activos para el formulario
$roles_result = $conn->query("SELECT * FROM rol WHERE estado = 'activo' ORDER BY nombre_rol ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="../recursos/css/forms.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="main-title">
                <i class="fas fa-users"></i> Gestión de Usuarios
            </h1>
        </div>
    </div>

    <!-- Formulario -->
    <div class="row">
        <div class="col-12">
            <div class="form-container">
                <h3 class="mb-4" style="color: var(--primary-blue);">Nuevo Usuario</h3>
                
                <form id="usuarioForm">
                    <input type="hidden" name="accion" value="crear">
                    <input type="hidden" id="id_usuario" name="id_usuario">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-user me-2"></i>Nombre Completo <span class="required">*</span>
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="correo" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-envelope me-2"></i>Correo Electrónico <span class="required">*</span>
                            </label>
                            <input type="email" class="form-control" id="correo" name="correo" required>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="direccion" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-map-marker-alt me-2"></i>Dirección
                            </label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="2" placeholder="Ingrese la dirección completa"></textarea>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contraseña" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-lock me-2"></i>Contraseña <span class="required">*</span>
                            </label>
                            <input type="password" class="form-control" id="contraseña" name="contraseña" required minlength="6">
                            <small class="text-muted">Mínimo 6 caracteres</small>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="confirmar_contraseña" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-lock me-2"></i>Confirmar Contraseña <span class="required">*</span>
                            </label>
                            <input type="password" class="form-control" id="confirmar_contraseña" name="confirmar_contraseña" required minlength="6">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="id_rol" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-user-tag me-2"></i>Rol <span class="required">*</span>
                            </label>
                            <select class="form-control" id="id_rol" name="id_rol" required>
                                <option value="">Seleccione un rol...</option>
                                <?php if ($roles_result && $roles_result->num_rows > 0): ?>
                                    <?php while ($rol = $roles_result->fetch_assoc()): ?>
                                        <option value="<?php echo $rol['id_rol']; ?>">
                                            <?php echo htmlspecialchars($rol['nombre_rol']); ?>
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
                            <button type="button" class="btn btn-secondary" onclick="limpiarFormulario()">
                                <i class="fas fa-eraser me-1"></i>Limpiar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Tabla de Usuarios -->
    <div class="row">
        <div class="col-12">
            <div class="table-container">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Correo</th>
                                <th>Dirección</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Fecha Creación</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-usuarios">
                            <?php if ($usuarios_result && $usuarios_result->num_rows > 0): ?>
                                <?php while ($u = $usuarios_result->fetch_assoc()): ?>
                                    <?php 
                                        $badge = $u['estado'] === 'activo' ? 'success' : 'danger';
                                        $toggle_action = $u['estado'] === 'activo' ? 'desactivar' : 'activar';
                                        $toggle_icon = $u['estado'] === 'activo' ? 'ban' : 'check';
                                        $toggle_class = $u['estado'] === 'activo' ? 'btn-danger' : 'btn-success';
                                        $fecha_formateada = date('d/m/Y H:i', strtotime($u['fecha_creacion']));
                                    ?>
                                    <tr>
                                        <td><?php echo $u['id_usuario']; ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($u['nombre']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($u['correo']); ?></td>
                                        <td>
                                            <span class="truncate" title="<?php echo htmlspecialchars($u['direccion'] ?? 'Sin dirección'); ?>">
                                                <?php echo htmlspecialchars($u['direccion'] ?? 'Sin dirección'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo htmlspecialchars($u['nombre_rol'] ?? 'Sin rol'); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $badge; ?>">
                                                <?php echo ucfirst($u['estado']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo $fecha_formateada; ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-edit btn-editar" 
                                                    data-id="<?php echo $u['id_usuario']; ?>" 
                                                    title="Editar">
                                                    <i class="fas fa-edit fa-fw"></i>
                                                </button>
                                                <?php if ($u['id_usuario'] != $_SESSION['usuario']['id_usuario']): ?>
                                                    <button class="btn <?php echo $toggle_class; ?> btn-toggle" 
                                                        data-id="<?php echo $u['id_usuario']; ?>" 
                                                        data-estado="<?php echo $toggle_action; ?>" 
                                                        title="<?php echo ucfirst($toggle_action); ?>">
                                                        <i class="fas fa-<?php echo $toggle_icon; ?> fa-fw"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-3"></i><br>
                                        No hay usuarios registrados
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