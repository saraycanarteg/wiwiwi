<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: ../../index.html");
    exit();
}

require_once '../../includes/verificar_permisos.php';
requierePermiso('revisar_logs');
require_once '../../config/database.php';
// Remover la consulta directa, ahora se carga via AJAX
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
                                <th>ID Log</th>
                                <th>ID Usuario</th>
                                <th>Tabla</th>
                                <th>Valor Anterior</th>
                                <th>Nuevo Valor</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-auditorias">
                            <!-- Los datos se cargan via AJAX -->
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    <i class="fas fa-spinner fa-spin fa-2x text-muted mb-3"></i><br>
                                    Cargando auditorías...
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
<script src="../recursos/js/validaciones.js"></script>
<script src="../recursos/js/formularios.js"></script>
<script>
// Solo mantener la funcionalidad de truncate para los valores largos
document.addEventListener('DOMContentLoaded', function() {
    // Verificar que las librerías PDF estén cargadas
    if (typeof window.jspdf === 'undefined') {
        console.error('jsPDF no está cargado correctamente');
    } else {
        console.log('jsPDF cargado correctamente');
    }
    
    // Aplicar funcionalidad de truncate a elementos que ya existen
    aplicarTruncate();
    
    // Observer para aplicar truncate a elementos que se cargan dinámicamente
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                aplicarTruncate();
            }
        });
    });
    
    observer.observe(document.getElementById('tabla-auditorias'), {
        childList: true,
        subtree: true
    });
});

function aplicarTruncate() {
    document.querySelectorAll('.truncate').forEach(cell => {
        // Remover event listeners anteriores para evitar duplicados
        cell.removeEventListener('click', toggleTruncate);
        cell.addEventListener('click', toggleTruncate);
    });
}

function toggleTruncate() {
    this.classList.toggle('expanded');
    if (this.classList.contains('expanded')) {
        this.title = 'Click para contraer';
    } else {
        this.title = 'Click para expandir';
    }
}
</script>
</body>
</html>

