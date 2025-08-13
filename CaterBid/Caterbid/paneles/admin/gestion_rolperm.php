<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../index.html");
    exit();
}

require_once '../../includes/verificar_permisos.php';

requierePermiso('gestion_rolperm');
require_once '../../config/database.php';
// Obtener roles con cantidad de permisos asignados
$roles_result = $conn->query("
    SELECT r.*, COUNT(rp.id_permiso) as cantidad_permisos 
    FROM rol r 
    LEFT JOIN rol_permiso rp ON r.id_rol = rp.id_rol 
    GROUP BY r.id_rol 
    ORDER BY r.id_rol ASC
");

// Obtener todos los permisos para el formulario
$permisos_result = $conn->query("SELECT * FROM permiso ORDER BY nombre_permiso ASC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Roles y Permisos</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="../recursos/css/forms.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="main-title">
                <i class="fas fa-user-shield"></i> Gestión de Roles y Permisos
            </h1>
        </div>
    </div>

    <!-- Formulario -->
    <div class="row">
        <div class="col-12">
            <div class="form-container">
                <h3 class="mb-4" style="color: var(--primary-blue);">Nuevo Rol</h3>
                
                <form id="rolForm">
                    <input type="hidden" name="accion" value="crear">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nombre_rol" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-user-tag me-2"></i>Nombre del Rol <span class="required">*</span>
                            </label>
                            <input type="text" class="form-control" id="nombre_rol" name="nombre_rol" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="descripcion" class="form-label" style="color: var(--primary-blue);">
                                <i class="fas fa-align-left me-2"></i>Descripción
                            </label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="2"></textarea>
                        </div>
                    </div>

                    <!-- Permisos checklist -->
                    <div class="mb-3">
                        <label class="form-label" style="color: var(--primary-blue);">
                            <i class="fas fa-shield-alt me-2"></i>Permisos Disponibles
                        </label>
                        <div class="permisos-container border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <div class="row">
                                <?php
                                $permisos = $permisos_result->fetch_all(MYSQLI_ASSOC);
                                $colCount = 3;
                                $chunked = array_chunk($permisos, ceil(count($permisos) / $colCount));
                                foreach ($chunked as $col) {
                                    echo '<div class="col-md-4">';
                                    foreach ($col as $perm) {
                                        echo '<div class="form-check mb-2">
                                                <input class="form-check-input" type="checkbox" name="permisos[]" value="'.$perm['id_permiso'].'" id="perm_'.$perm['id_permiso'].'">
                                                <label class="form-check-label small" for="perm_'.$perm['id_permiso'].'" title="'.htmlspecialchars($perm['descripcion']).'">
                                                    '.htmlspecialchars($perm['nombre_permiso']).'
                                                </label>
                                            </div>';
                                    }
                                    echo '</div>';
                                }
                                ?>
                            </div>
                        </div>
                        <small class="text-muted">Selecciona los permisos que tendrá este rol</small>
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
                                <th>Nombre del Rol</th>
                                <th>Descripción</th>
                                <th>Permisos</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-roles">
                            <!-- Los datos se cargan via AJAX -->
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i><br>
                                    Cargando roles...
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
<!-- Agregar librerías para PDF después de jQuery -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
<script src="../recursos/js/formularios.js"></script>
<script src="../recursos/js/validaciones.js"></script>
</body>
</html>