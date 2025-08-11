<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../index.html");
    exit();
}

require_once '../../includes/verificar_permisos.php';
requierePermiso('revisar_logs');
require_once '../../config/database.php';
$logs_result = $conn->query("SELECT * FROM auditoria ORDER BY fecha_cambio DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logs y Auditorías</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="../recursos/css/forms.css" rel="stylesheet">
</head>
<body>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <h1 class="main-title">
                <i class="fas fa-clipboard-list"></i> Logs y Auditorías
            </h1>
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
                                <th>ID Log</th>
                                <th>ID Usuario</th>
                                <th>Tabla</th>
                                <th>Valor Anterior</th>
                                <th>Nuevo Valor</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-logs">
                            <?php if ($logs_result && $logs_result->num_rows > 0): ?>
                                <?php while ($log = $logs_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $log['id_auditoria']; ?></td>
                                        <td><?php echo htmlspecialchars($log['id_usuario']); ?></td>
                                        <td><?php echo htmlspecialchars($log['tabla_afectada']); ?></td>
                                        <td class="truncate" title="Click para ver completo">
                                            <?php echo htmlspecialchars($log['valor_anterior']); ?>
                                        </td>
                                        <td class="truncate" title="Click para ver completo">
                                            <?php echo htmlspecialchars($log['valor_nuevo']); ?>
                                        </td>
                                        <td><?php echo date('d/m/Y H:i:s', strtotime($log['fecha_cambio'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-inbox fa-2x text-muted mb-3"></i><br>
                                        No hay registros en auditoría
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

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
<script src="../recursos/js/validaciones.js"></script>
<script>
document.querySelectorAll('.truncate').forEach(cell => {
    cell.addEventListener('click', function() {
        this.classList.toggle('expanded');

        if (this.classList.contains('expanded')) {
            this.title = 'Click para contraer';
        } else {
            this.title = 'Click para expandir';
        }
    });
});
</script>
</body>
</html>

