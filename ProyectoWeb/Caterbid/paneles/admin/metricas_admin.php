<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit;
}
require_once '../../includes/verificar_permisos.php';
requierePermiso('metricas_admin');
require_once '../../config/database.php';

$totalUsuarios = 0;
$totalProveedores = 0;
$totalRoles = 0;
$totalAuditorias = 0;
$usuariosPorRol = [];
$estadosProveedores = [];
$actividadAuditoria = [];
$actividadReciente = [];
$listaUsuarios = [];

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Total de usuarios activos
$result = $conn->query("SELECT COUNT(*) as total FROM usuario WHERE estado = 'activo'");
if ($result) {
    $totalUsuarios = $result->fetch_assoc()['total'];
}

// Total de proveedores activos
$result = $conn->query("SELECT COUNT(*) as total FROM proveedor WHERE estado = 'activo'");
if ($result) {
    $totalProveedores = $result->fetch_assoc()['total'];
}

// Total de roles activos
$result = $conn->query("SELECT COUNT(*) as total FROM rol WHERE estado = 'activo'");
if ($result) {
    $totalRoles = $result->fetch_assoc()['total'];
}

// Total de registros de auditoría
$result = $conn->query("SELECT COUNT(*) as total FROM auditoria");
if ($result) {
    $totalAuditorias = $result->fetch_assoc()['total'];
}

// Distribución de usuarios por rol
$result = $conn->query("
    SELECT r.nombre_rol, COUNT(u.id_usuario) as cantidad 
    FROM rol r 
    LEFT JOIN usuario u ON r.id_rol = u.id_rol AND u.estado = 'activo'
    WHERE r.estado = 'activo'
    GROUP BY r.id_rol, r.nombre_rol
    ORDER BY cantidad DESC
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $usuariosPorRol[] = $row;
    }
}

// Estados de proveedores
$result = $conn->query("
    SELECT estado, COUNT(*) as cantidad 
    FROM proveedor 
    GROUP BY estado
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $estadosProveedores[] = $row;
    }
}

// Actividad de auditoría de los últimos 7 días
$result = $conn->query("
    SELECT DATE(fecha_cambio) as fecha, COUNT(*) as cantidad
    FROM auditoria 
    WHERE fecha_cambio >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(fecha_cambio)
    ORDER BY fecha ASC
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $actividadAuditoria[] = $row;
    }
}

// Actividad reciente del sistema (últimos 10 registros)
$result = $conn->query("
    SELECT a.tabla_afectada, a.valor_nuevo, a.fecha_cambio, u.nombre as usuario_nombre
    FROM auditoria a
    LEFT JOIN usuario u ON a.id_usuario = u.id_usuario
    ORDER BY a.fecha_cambio DESC
    LIMIT 10
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $actividadReciente[] = $row;
    }
}

// Función para formatear tiempo relativo
function tiempoRelativo($fecha) {
    $ahora = new DateTime();
    $tiempo = new DateTime($fecha);
    $diferencia = $ahora->diff($tiempo);

    if ($diferencia->days > 0) {
        return $diferencia->days == 1 ? 'hace 1 día' : 'hace ' . $diferencia->days . ' días';
    } elseif ($diferencia->h > 0) {
        return $diferencia->h == 1 ? 'hace 1 hora' : 'hace ' . $diferencia->h . ' horas';
    } elseif ($diferencia->i > 0) {
        return $diferencia->i == 1 ? 'hace 1 minuto' : 'hace ' . $diferencia->i . ' minutos';
    } else {
        return 'hace unos momentos';
    }
}

// Función para obtener icono de actividad
function obtenerIconoActividad($tabla, $valorNuevo) {
    if ($valorNuevo === null) {
        return ['class' => 'activity-delete', 'icon' => 'fa-trash'];
    } elseif (strpos($valorNuevo, '"id_') !== false && strpos($valorNuevo, '"estado":"activo"') !== false) {
        return ['class' => 'activity-create', 'icon' => 'fa-plus'];
    } else {
        return ['class' => 'activity-update', 'icon' => 'fa-edit'];
    }
}
?>

<link rel="stylesheet" href="../recursos/css/metricas.css">

<div class="container-fluid py-4">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <h1 class="main-title">
                <i class="fas fa-chart-line"></i>
                Métricas del Administrador
            </h1>
        </div>
    </div>

    <!-- Métricas principales -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="metric-card text-center">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="metric-number"><?php echo $totalUsuarios; ?></div>
                        <div class="metric-label">Usuarios Registrados</div>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-users metric-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="metric-card text-center">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="metric-number"><?php echo $totalProveedores; ?></div>
                        <div class="metric-label">Proveedores Activos</div>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-truck metric-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="metric-card text-center">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="metric-number"><?php echo $totalRoles; ?></div>
                        <div class="metric-label">Roles del Sistema</div>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-user-shield metric-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="metric-card text-center">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="metric-number"><?php echo $totalAuditorias; ?></div>
                        <div class="metric-label">Total Auditorías</div>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-clipboard-list metric-icon"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="chart-container">
                <h3 class="chart-title">
                    <i class="fas fa-chart-bar"></i>
                    Distribución de Usuarios por Rol
                </h3>
                <div id="userRoleChartContainer">
                    <div class="chart-canvas">
                        <canvas id="userRoleChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="chart-container">
                <h3 class="chart-title">
                    <i class="fas fa-chart-pie"></i>
                    Estados de Proveedores
                </h3>
                <div id="providerStatusChartContainer">
                    <div class="chart-canvas">
                        <canvas id="providerStatusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="chart-container">
                <h3 class="chart-title">
                    <i class="fas fa-timeline"></i>
                    Actividad de Auditoría (Últimos 7 días)
                </h3>
                <div id="auditActivityChartContainer">
                    <div class="chart-canvas">
                        <canvas id="auditActivityChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="chart-container">
                <h3 class="chart-title">
                    <i class="fas fa-history"></i>
                    Actividad Reciente del Sistema
                </h3>
                <div style="max-height: 300px; overflow-y: auto;">
                    <?php if (empty($actividadReciente)): ?>
                        <div class="no-data-message">
                            <i class="fas fa-info-circle"></i>
                            No hay actividad reciente registrada
                        </div>
                    <?php else: ?>
                        <?php foreach($actividadReciente as $actividad): 
                            $iconoInfo = obtenerIconoActividad($actividad['tabla_afectada'], $actividad['valor_nuevo']);
                            $nombreTabla = ucfirst(str_replace('_', ' ', $actividad['tabla_afectada']));
                        ?>
                        <div class="activity-item">
                            <div class="activity-icon <?php echo $iconoInfo['class']; ?>">
                                <i class="fas <?php echo $iconoInfo['icon']; ?>"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">
                                    <?php echo $nombreTabla; ?>
                                    <?php if($actividad['valor_nuevo'] === null): ?>
                                        Eliminado
                                    <?php elseif(strpos($actividad['valor_nuevo'], '"id_') !== false): ?>
                                        Modificado
                                    <?php else: ?>
                                        Actualizado
                                    <?php endif; ?>
                                </div>
                                <div class="activity-time">
                                    <?php echo $actividad['usuario_nombre'] ? $actividad['usuario_nombre'] . ' - ' : ''; ?>
                                    <?php echo tiempoRelativo($actividad['fecha_cambio']); ?>
                                </div>
                            </div>
                            <span class="status-badge <?php echo $actividad['valor_nuevo'] ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo $actividad['valor_nuevo'] ? 'Completado' : 'Eliminado'; ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Función para cargar Chart.js si no está disponible
function loadChartsAfterLibrary() {
    if (typeof Chart !== 'undefined') {
        initializeCharts();
        return;
    }

    const script = document.createElement('script');
    script.src = 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js';
    script.onload = function() {
        initializeCharts();
    };
    script.onerror = function() {
        showErrorMessage();
    };
    document.head.appendChild(script);
}

function showErrorMessage() {
    document.querySelectorAll('.chart-canvas').forEach(function(canvas) {
        canvas.innerHTML = '<div class="no-data-message"><i class="fas fa-exclamation-triangle"></i>Error al cargar las gráficas</div>';
    });
}

function initializeCharts() {
    // Configuración de Chart.js
    Chart.defaults.font.family = 'Segoe UI, Tahoma, Geneva, Verdana, sans-serif';
    Chart.defaults.color = '#666';

    // Datos dinámicos de PHP
    const usuariosPorRol = <?php echo json_encode($usuariosPorRol); ?>;
    const estadosProveedores = <?php echo json_encode($estadosProveedores); ?>;
    const actividadAuditoria = <?php echo json_encode($actividadAuditoria); ?>;

    console.log('Datos cargados:', {
        usuariosPorRol,
        estadosProveedores,
        actividadAuditoria
    });

    // Función para mostrar mensaje cuando no hay datos
    function mostrarMensajeSinDatos(containerId, mensaje = 'No hay datos disponibles para mostrar.') {
        const container = document.getElementById(containerId);
        if (container) {
            container.innerHTML = 
                `<div class="no-data-message">
                    <i class="fas fa-info-circle"></i>
                    ${mensaje}
                </div>`;
        }
    }

    // Gráfico de distribución de usuarios por rol
    const userRoleCanvas = document.getElementById('userRoleChart');
    if (userRoleCanvas) {
        if (usuariosPorRol.length === 0) {
            mostrarMensajeSinDatos('userRoleChartContainer', 'No hay usuarios registrados para mostrar.');
        } else {
            const userRoleCtx = userRoleCanvas.getContext('2d');
            new Chart(userRoleCtx, {
                type: 'bar',
                data: {
                    labels: usuariosPorRol.map(item => item.nombre_rol.charAt(0).toUpperCase() + item.nombre_rol.slice(1)),
                    datasets: [{
                        label: 'Cantidad de Usuarios',
                        data: usuariosPorRol.map(item => parseInt(item.cantidad)),
                        backgroundColor: [
                            '#1a365d',
                            '#4a90e2',
                            '#28a745',
                            '#ffc107',
                            '#dc3545'
                        ],
                        borderWidth: 1,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    }

    // Gráfico de estados de proveedores
    const providerStatusCanvas = document.getElementById('providerStatusChart');
    if (providerStatusCanvas) {
        if (estadosProveedores.length === 0) {
            mostrarMensajeSinDatos('providerStatusChartContainer', 'No hay proveedores registrados para mostrar.');
        } else {
            const providerStatusCtx = providerStatusCanvas.getContext('2d');
            new Chart(providerStatusCtx, {
                type: 'doughnut',
                data: {
                    labels: estadosProveedores.map(item => item.estado.charAt(0).toUpperCase() + item.estado.slice(1) + 's'),
                    datasets: [{
                        data: estadosProveedores.map(item => parseInt(item.cantidad)),
                        backgroundColor: [
                            '#28a745',
                            '#dc3545'
                        ],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
        }
    }

    // Preparar datos de actividad de auditoría para los últimos 7 días
    const hoy = new Date();
    const ultimosSieteDias = [];
    const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    
    for (let i = 6; i >= 0; i--) {
        const fecha = new Date(hoy);
        fecha.setDate(fecha.getDate() - i);
        const fechaStr = fecha.toISOString().split('T')[0];
        const nombreDia = diasSemana[fecha.getDay()];
        
        const registro = actividadAuditoria.find(item => item.fecha === fechaStr);
        ultimosSieteDias.push({
            dia: nombreDia,
            cantidad: registro ? parseInt(registro.cantidad) : 0
        });
    }

    // Gráfico de actividad de auditoría
    const auditActivityCanvas = document.getElementById('auditActivityChart');
    if (auditActivityCanvas) {
        if (actividadAuditoria.length === 0) {
            mostrarMensajeSinDatos('auditActivityChartContainer', 'No hay actividad de auditoría en los últimos 7 días.');
        } else {
            const auditActivityCtx = auditActivityCanvas.getContext('2d');
            new Chart(auditActivityCtx, {
                type: 'line',
                data: {
                    labels: ultimosSieteDias.map(item => item.dia),
                    datasets: [{
                        label: 'Registros de Auditoría',
                        data: ultimosSieteDias.map(item => item.cantidad),
                        borderColor: '#1a365d',
                        backgroundColor: 'rgba(143, 171, 197, 0.5)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#1a365d',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                stepSize: 1
                            }
                        }
                    }
                }
            });
        }
    }
}

// Ejecutar cuando el contenido esté listo
document.addEventListener('DOMContentLoaded', function() {
    loadChartsAfterLibrary();
});

// Si el DOM ya está cargado, ejecutar inmediatamente
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadChartsAfterLibrary);
} else {
    loadChartsAfterLibrary();
}
</script>