<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: index.html');
    exit;
}

require_once '../../includes/verificar_permisos.php';
requierePermiso('metricas_bodeguero');
require_once '../../config/database.php';

$totalProductos = 0;
$totalPaquetes = 0;
$totalCategorias = 0;
$totalIngresos = 0;
$productosPorCategoria = [];
$paquetesPorProveedor = [];
$paquetesUltimosDias = [];
$actividadReciente = [];

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Total de productos activos
$result = $conn->query("SELECT COUNT(*) as total FROM producto WHERE estado = 'activo'");
if ($result) {
    $totalProductos = $result->fetch_assoc()['total'];
}

// Total de paquetes (todos los estados)
$result = $conn->query("SELECT COUNT(*) as total FROM paquete");
if ($result) {
    $totalPaquetes = $result->fetch_assoc()['total'];
}

// Total de categorías (conteo de categorías distintas en productos)
$result = $conn->query("SELECT COUNT(DISTINCT categoria) as total FROM producto WHERE categoria IS NOT NULL AND categoria != ''");
if ($result) {
    $totalCategorias = $result->fetch_assoc()['total'];
}

// Total de ingresos de paquetes
$result = $conn->query("SELECT COUNT(*) as total FROM ingreso");
if ($result) {
    $totalIngresos = $result->fetch_assoc()['total'];
}

// Productos por categoría (usando la columna categoria directamente)
$result = $conn->query("
    SELECT categoria as nombre_categoria, COUNT(*) as cantidad 
    FROM producto 
    WHERE categoria IS NOT NULL AND categoria != ''
    GROUP BY categoria
    ORDER BY cantidad DESC
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $productosPorCategoria[] = $row;
    }
}

// Cantidad de paquetes por proveedor - Intentar JOIN primero, si falla usar ID
$paquetesPorProveedorQuery = "
    SELECT 
        COALESCE(prov.nombre_proveedor, CONCAT('Proveedor ', paq.id_proveedor)) as nombre_proveedor, 
        COUNT(paq.id_paquete) as cantidad 
    FROM paquete paq
    LEFT JOIN proveedor prov ON paq.id_proveedor = prov.id_proveedor
    GROUP BY paq.id_proveedor, prov.nombre_proveedor
    ORDER BY cantidad DESC
    LIMIT 10
";

$result = $conn->query($paquetesPorProveedorQuery);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $paquetesPorProveedor[] = $row;
    }
} else {
    // Si falla el JOIN, usar solo IDs
    $result = $conn->query("
        SELECT 
            CONCAT('Proveedor ', paq.id_proveedor) as nombre_proveedor, 
            COUNT(paq.id_paquete) as cantidad 
        FROM paquete paq
        GROUP BY paq.id_proveedor
        ORDER BY cantidad DESC
        LIMIT 10
    ");
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $paquetesPorProveedor[] = $row;
        }
    }
}

// Cantidad de paquetes creados en los últimos 7 días (usando fecha_creacion de paquete)
$result = $conn->query("
    SELECT DATE(fecha_creacion) as fecha, COUNT(*) as cantidad
    FROM paquete 
    WHERE fecha_creacion >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY DATE(fecha_creacion)
    ORDER BY fecha ASC
");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $paquetesUltimosDias[] = $row;
    }
}

// Actividad reciente del sistema - usando tabla paquete directamente
$result = $conn->query("
    SELECT 
        CASE 
            WHEN p.estado = 'activo' THEN 'Ingreso'
            ELSE 'Actualización'
        END as tipo_actividad,
        p.fecha_creacion as fecha_cambio,
        CONCAT('Paquete ID: ', p.id_paquete, ' - Proveedor ID: ', p.id_proveedor) as descripcion,
        'Sistema' as usuario_nombre,
        p.estado as estado_actividad
    FROM paquete p
    ORDER BY p.fecha_creacion DESC
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

// Función para obtener icono de actividad de bodega
function obtenerIconoActividad($tipo, $estado) {
    switch ($tipo) {
        case 'Ingreso':
            return ['class' => 'activity-create', 'icon' => 'fa-arrow-down'];
        case 'Salida':
            return ['class' => 'activity-delete', 'icon' => 'fa-arrow-up'];
        default:
            return ['class' => 'activity-update', 'icon' => 'fa-box'];
    }
}
?>

<link rel="stylesheet" href="../recursos/css/forms.css">

<div class="container-fluid py-4">
    <div class="row g-4 mb-4">
        <div class="col-12">
            <h1 class="main-title">
                <i class="fas fa-warehouse"></i>
                Métricas del Bodeguero
            </h1>
        </div>
    </div>

    <!-- Métricas principales -->
    <div class="row g-4 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="metric-card text-center">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="metric-number"><?php echo $totalProductos; ?></div>
                        <div class="metric-label">Productos Activos</div>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-boxes metric-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="metric-card text-center">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="metric-number"><?php echo $totalPaquetes; ?></div>
                        <div class="metric-label">Paquetes Registrados</div>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-box metric-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="metric-card text-center">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="metric-number"><?php echo $totalCategorias; ?></div>
                        <div class="metric-label">Total Categorías</div>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-tags metric-icon"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-3 col-md-6">
            <div class="metric-card text-center">
                <div class="row align-items-center">
                    <div class="col-8">
                        <div class="metric-number"><?php echo $totalIngresos; ?></div>
                        <div class="metric-label">Total Ingresos</div>
                    </div>
                    <div class="col-4">
                        <i class="fas fa-arrow-down metric-icon"></i>
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
                    Productos por Categoría
                </h3>
                <div id="productCategoryChartContainer">
                    <div class="chart-canvas" style="height: 400px;">
                        <canvas id="productCategoryChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="chart-container">
                <h3 class="chart-title">
                    <i class="fas fa-chart-pie"></i>
                    Paquetes por Proveedor
                </h3>
                <div id="packageProviderChartContainer">
                    <div class="chart-canvas">
                        <canvas id="packageProviderChart"></canvas>
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
                    Paquetes Creados (Últimos 7 días)
                </h3>
                <div id="packageIngressChartContainer">
                    <div class="chart-canvas">
                        <canvas id="packageIngressChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6">
            <div class="chart-container">
                <h3 class="chart-title">
                    <i class="fas fa-history"></i>
                    Actividad Reciente de Bodega
                </h3>
                <div style="max-height: 300px; overflow-y: auto;">
                    <?php if (empty($actividadReciente)): ?>
                        <div class="no-data-message">
                            <i class="fas fa-info-circle"></i>
                            No hay actividad reciente registrada
                        </div>
                    <?php else: ?>
                        <?php foreach($actividadReciente as $actividad): 
                            $iconoInfo = obtenerIconoActividad($actividad['tipo_actividad'], $actividad['estado_actividad']);
                        ?>
                        <div class="activity-item">
                            <div class="activity-icon <?php echo $iconoInfo['class']; ?>">
                                <i class="fas <?php echo $iconoInfo['icon']; ?>"></i>
                            </div>
                            <div class="activity-content">
                                <div class="activity-title">
                                    <?php echo $actividad['tipo_actividad']; ?>: <?php echo $actividad['descripcion']; ?>
                                </div>
                                <div class="activity-time">
                                    <?php echo $actividad['usuario_nombre'] ? $actividad['usuario_nombre'] . ' - ' : ''; ?>
                                    <?php echo tiempoRelativo($actividad['fecha_cambio']); ?>
                                </div>
                            </div>
                            <span class="status-badge status-active">
                                Procesado
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
    const productosPorCategoria = <?php echo json_encode($productosPorCategoria); ?>;
    const paquetesPorProveedor = <?php echo json_encode($paquetesPorProveedor); ?>;
    const paquetesUltimosDias = <?php echo json_encode($paquetesUltimosDias); ?>;

    console.log('Datos cargados:', {
        productosPorCategoria,
        paquetesPorProveedor,
        paquetesUltimosDias
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

    // Gráfico de productos por categoría (barras)
    const productCategoryCanvas = document.getElementById('productCategoryChart');
    if (productCategoryCanvas) {
        if (productosPorCategoria.length === 0) {
            mostrarMensajeSinDatos('productCategoryChartContainer', 'No hay productos por categoría para mostrar.');
        } else {
            const productCategoryCtx = productCategoryCanvas.getContext('2d');
            new Chart(productCategoryCtx, {
                type: 'bar',
                data: {
                    labels: productosPorCategoria.map(item => item.nombre_categoria.charAt(0).toUpperCase() + item.nombre_categoria.slice(1)),
                    datasets: [{
                        label: 'Cantidad de Productos',
                        data: productosPorCategoria.map(item => parseInt(item.cantidad)),
                        backgroundColor: [
                            '#2d5aa0',
                            '#4a90e2',
                            '#28a745',
                            '#ffc107',
                            '#5a359cff',
                            '#17a2b8',
                            '#6c757d',
                            '#343a40'
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

    // Gráfico de paquetes por proveedor (pastel)
    const packageProviderCanvas = document.getElementById('packageProviderChart');
    if (packageProviderCanvas) {
        if (paquetesPorProveedor.length === 0) {
            mostrarMensajeSinDatos('packageProviderChartContainer', 'No hay paquetes por proveedor para mostrar.');
        } else {
            const packageProviderCtx = packageProviderCanvas.getContext('2d');
            new Chart(packageProviderCtx, {
                type: 'doughnut',
                data: {
                    labels: paquetesPorProveedor.map(item => item.nombre_proveedor),
                    datasets: [{
                        data: paquetesPorProveedor.map(item => parseInt(item.cantidad)),
                        backgroundColor: [
                            '#2d5aa0',
                            '#4a90e2',
                            '#28a745',
                            '#ffc107',
                            '#dc3545',
                            '#17a2b8',
                            '#6c757d',
                            '#343a40',
                            '#fd7e14',
                            '#e83e8c'
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

    // Preparar datos de ingresos de paquetes para los últimos 7 días
    const hoy = new Date();
    const ultimosSieteDias = [];
    const diasSemana = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'];
    
    for (let i = 6; i >= 0; i--) {
        const fecha = new Date(hoy);
        fecha.setDate(fecha.getDate() - i);
        const fechaStr = fecha.toISOString().split('T')[0];
        const nombreDia = diasSemana[fecha.getDay()];
        
        const registro = paquetesUltimosDias.find(item => item.fecha === fechaStr);
        ultimosSieteDias.push({
            dia: nombreDia,
            cantidad: registro ? parseInt(registro.cantidad) : 0
        });
    }

    // Gráfico de ingresos de paquetes (líneal)
    const packageIngressCanvas = document.getElementById('packageIngressChart');
    if (packageIngressCanvas) {
        if (paquetesUltimosDias.length === 0) {
            mostrarMensajeSinDatos('packageIngressChartContainer', 'No hay paquetes creados en los últimos 7 días.');
        } else {
            const packageIngressCtx = packageIngressCanvas.getContext('2d');
            new Chart(packageIngressCtx, {
                type: 'line',
                data: {
                    labels: ultimosSieteDias.map(item => item.dia),
                    datasets: [{
                        label: 'Paquetes Creados',
                        data: ultimosSieteDias.map(item => item.cantidad),
                        borderColor: '#2d5aa0',
                        backgroundColor: 'rgba(45, 90, 160, 0.3)',
                        tension: 0.4,
                        fill: true,
                        pointBackgroundColor: '#2d5aa0',
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