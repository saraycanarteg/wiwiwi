<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../index.html");
    exit();
}
require_once '../../includes/verificar_permisos.php';
requierePermiso('historial_cotizaciones');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Métricas de Cotizaciones</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        body { background: #fff; }
        .metricas-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            grid-template-rows: auto auto auto;
            gap: 1.2rem;
            max-width: 1200px;
            margin: 2rem auto;
        }
        .kpi-card {
            background: #fff;
            color: #111 !important;
            border-radius: 7px;
            display: flex;
            align-items: center;
            min-height: 60px;
            font-size: 0.95rem;
            padding: 0.7rem 1.2rem;
            border: 1.5px solid #e3e3e3;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .kpi-icon {
            font-size: 1.6rem;
            margin-right: 1.1rem;
            color: #3498db !important; /* Azul para destacar sobre blanco */
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 36px;
        }
        .kpi-divider {
            border-left: 2px solid #e3e3e3;
            height: 36px;
            margin: 0 1.1rem 0 0.2rem;
        }
        .kpi-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            font-size: 0.92rem;
            color: #111 !important;
        }
        .kpi-value {
            font-size: 1.1rem;
            font-weight: bold;
            margin-bottom: 0.1rem;
            color: #111 !important;
        }
        .kpi-label {
            font-size: 0.93rem;
            color: #111 !important;
        }
        .kpi-details {
            font-size: 0.85rem;
            color: #111 !important;
            margin-top: 0.2rem;
        }
        .metricas-section {
            background: #fff;
            color: #111 !important;
            border-radius: 7px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 1.2rem 1rem 1.5rem 1rem;
            min-height: 180px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border: 1.5px solid #e3e3e3;
        }
        .metricas-section h4 {
            margin-bottom: 1rem;
            color: #111 !important;
            font-size: 1.08rem;
            font-weight: 500;
        }
        .top-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 0.7rem;
            background: #fff;
            color: #111 !important;
            border-radius: 6px;
            overflow: hidden;
        }
        .top-table th, .top-table td {
            border: 1px solid #e1e1e1;
            padding: 0.4rem 0.7rem;
            text-align: left;
            font-size: 0.93rem;
            color: #111 !important;
            background: #fff !important;
        }
        .top-table th {
            background: #f3f8fc !important;
            font-weight: 600;
            color: #156080 !important;
        }
        .top-table td {
            background: #fff !important;
        }
        .chart-container {
            width: 100%;
            height: 220px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        /* Responsive: tablets */
        @media (max-width: 1100px) {
            .metricas-grid { grid-template-columns: 1fr 1fr; grid-template-rows: auto auto auto auto; }
            .metricas-grid > div { min-width: 0; }
        }
        /* Responsive: móviles (apilar todo en columna) */
        @media (max-width: 900px) {
            .metricas-grid {
                display: flex !important;
                flex-direction: column !important;
                gap: 0.7rem;
                margin: 0.5rem auto;
                max-width: 100vw;
            }
            .metricas-grid > * {
                width: 100% !important;
                min-width: 0 !important;
                max-width: 100vw !important;
            }
            .kpi-card, .metricas-section {
                min-width: 0;
                padding: 0.7rem 0.2rem;
                font-size: 0.97em;
            }
            .kpi-icon {
                font-size: 1.2rem;
                min-width: 28px;
                margin-right: 0.7rem;
            }
            .kpi-divider {
                height: 28px;
                margin: 0 0.7rem 0 0.1rem;
            }
            .top-table, .chart-container {
                display: block;
                overflow-x: auto;
                width: 100%;
                min-width: 350px;
            }
            .top-table th, .top-table td {
                font-size: 0.93em;
                padding: 0.3rem 0.2rem;
            }
        }
        @media (max-width: 600px) {
            .metricas-grid {
                gap: 0.5rem;
                margin: 0.2rem auto;
                max-width: 100vw;
            }
            .kpi-card, .metricas-section {
                padding: 0.4rem 0.05rem;
                font-size: 0.95em;
            }
            .kpi-icon {
                font-size: 1em;
                min-width: 20px;
                margin-right: 0.4rem;
            }
            .kpi-divider {
                height: 18px;
                margin: 0 0.4rem 0 0.05rem;
            }
            .top-table th, .top-table td {
                font-size: 0.92em;
                padding: 0.2rem 0.1rem;
            }
            .top-table, .chart-container {
                display: block;
                overflow-x: auto;
                width: 100%;
                min-width: 260px;
            }
        }
        /* Forzar scroll horizontal en tablas y gráficos en cualquier pantalla pequeña */
        @media (max-width: 700px) {
            .top-table, .chart-container {
                min-width: 200px;
            }
        }
        /* Iconos de colores para distinguir sobre fondo blanco */
        .kpi-card .fa-file-invoice-dollar { color: #3498db !important; }
        .kpi-card .fa-user { color: #e67e22 !important; }
        .kpi-card .fa-box { color: #27ae60 !important; }
        .kpi-card .fa-industry { color: #8e44ad !important; }
        /* Forzar todos los textos a negro dentro de métricas */
        .metricas-grid, .metricas-grid *, .metricas-section, .metricas-section *, .kpi-card, .kpi-card * {
            color: #111 !important;
            text-shadow: none !important;
        }
        /* Extra: fuerza el scroll horizontal si es necesario */
        .top-table, .chart-container {
            max-width: 100vw;
            overflow-x: auto;
        }
    </style>
</head>
<body>
<div style="max-width:1240px;margin:auto;">
    <h2 style="color:#156080;margin-bottom:1.2rem;font-size:1.3rem;text-align:center;">
        <i class="fas fa-chart-bar mr-2"></i>Métricas de Cotizaciones
    </h2>
    <div class="metricas-grid">
        <!-- KPIs fila 1 -->
        <div class="kpi-card">
            <span class="kpi-icon"><i class="fas fa-file-invoice-dollar"></i></span>
            <div class="kpi-divider"></div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpi-total-cotizaciones">-</div>
                <div class="kpi-label">Total cotizaciones</div>
            </div>
        </div>
        <div class="kpi-card">
            <span class="kpi-icon"><i class="fas fa-user"></i></span>
            <div class="kpi-divider"></div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpi-cliente-top">-</div>
                <div class="kpi-label">Cliente con más cotizaciones</div>
            </div>
        </div>
        <div class="kpi-card">
            <span class="kpi-icon"><i class="fas fa-box"></i></span>
            <div class="kpi-divider"></div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpi-paquete-top">-</div>
                <div class="kpi-label">Paquete más cotizado</div>
            </div>
        </div>
        <div class="kpi-card">
            <span class="kpi-icon"><i class="fas fa-industry"></i></span>
            <div class="kpi-divider"></div>
            <div class="kpi-content">
                <div class="kpi-value" id="kpi-proveedor-top">-</div>
                <div class="kpi-label">Proveedor más cotizado</div>
            </div>
        </div>
        <!-- Gráfica cotizaciones por mes -->
        <div class="metricas-section" style="grid-column: 1 / 3;">
            <h4>Gráfica de cotizaciones por mes</h4>
            <div class="chart-container"><canvas id="chartMes"></canvas></div>
        </div>
        <!-- Top 5 paquetes -->
        <div class="metricas-section" style="grid-column: 3 / 5;">
            <h4>Top 5 paquetes</h4>
            <table class="top-table" id="tabla-top-paquetes">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Paquete</th>
                        <th>Tipo Evento</th>
                        <th>Cotizaciones</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- JS -->
                </tbody>
            </table>
        </div>
        <!-- Gráfica cotizaciones por día -->
        <div class="metricas-section" style="grid-column: 1 / 3;">
            <h4>Gráfica de cotizaciones por Día</h4>
            <div class="chart-container"><canvas id="chartDia"></canvas></div>
        </div>
        <!-- % Cotizaciones por Estado -->
        <div class="metricas-section" style="grid-column: 3 / 5;">
            <h4>% Cotizaciones por Estado</h4>
            <div class="chart-container"><canvas id="chartEstado"></canvas></div>
        </div>
    </div>
</div>
<script>
(function($){
    const AJAX_URL = '../controles/ajax_metricas.php';
    let chartMes = null, chartDia = null, chartEstado = null;

    function renderMetricas(data) {
        // KPIs
        $('#kpi-total-cotizaciones').text(data.total_cotizaciones ?? '-');
        $('#kpi-cliente-top').html(data.cliente_top?.nombres
            ? `<span style="font-size:1em;"><span style="font-weight:600;">${data.cliente_top.nombres}</span><br><span style="font-size:0.95em;color:#e0e0e0;">${data.cliente_top.identificacion ?? ''}</span><br><span style="color:#fff;font-size:1.08em;font-weight:600;">${data.cliente_top.total} cotizaciones</span></span>`
            : '-');
        $('#kpi-paquete-top').html(data.paquete_top?.id_paquete
            ? `<span style="font-size:1em;"><span style="font-weight:600;">#${data.paquete_top.id_paquete}</span><br><span style="font-size:0.95em;color:#e0e0e0;">${data.paquete_top.tipo_evento ?? ''}</span><br><span style="color:#fff;font-size:1.08em;font-weight:600;">${data.paquete_top.total} cotizaciones</span></span>`
            : '-');
        $('#kpi-proveedor-top').html(data.proveedor_top?.nombre
            ? `<span style="font-size:1em;"><span style="font-weight:600;">${data.proveedor_top.nombre}</span><br><span style="color:#fff;font-size:1.08em;font-weight:600;">${data.proveedor_top.total} cotizaciones</span></span>`
            : '-');

        // Top 5 paquetes
        let topHtml = '';
        if (Array.isArray(data.top_paquetes)) {
            data.top_paquetes.forEach(function(p, idx){
                topHtml += `<tr>
                    <td>${idx+1}</td>
                    <td>#${p.id_paquete}</td>
                    <td>${p.tipo_evento}</td>
                    <td><strong>${p.total}</strong></td>
                </tr>`;
            });
        }
        $('#tabla-top-paquetes tbody').html(topHtml);

        // Charts
        if (chartMes) chartMes.destroy();
        if (chartDia) chartDia.destroy();
        if (chartEstado) chartEstado.destroy();

        // Cotizaciones por mes
        const meses = data.por_mes.map(x => x.mes);
        const valoresMes = data.por_mes.map(x => x.total);
        chartMes = new Chart(document.getElementById('chartMes'), {
            type: 'bar',
            data: { 
                labels: meses, 
                datasets: [{
                    label: 'Cotizaciones', 
                    data: valoresMes, 
                    backgroundColor: '#fff', 
                    borderColor: '#111', 
                    borderWidth: 2
                }] 
            },
            options: { 
                responsive: true, 
                plugins: { 
                    legend: { display: false, labels: { color: '#111' } } 
                }, 
                scales: { 
                    y: { beginAtZero: true, ticks: { color: '#111' }, grid: { color: '#bbb' } }, 
                    x: { ticks: { color: '#111' }, grid: { color: '#bbb' } } 
                }
            }
        });

        // Cotizaciones por día
        const dias = data.por_dia.map(x => x.dia);
        const valoresDia = data.por_dia.map(x => x.total);
        chartDia = new Chart(document.getElementById('chartDia'), {
            type: 'line',
            data: { 
                labels: dias, 
                datasets: [{
                    label: 'Cotizaciones', 
                    data: valoresDia, 
                    borderColor: '#111', 
                    backgroundColor: 'rgba(22,22,22,0.12)', 
                    fill: true 
                }] 
            },
            options: { 
                responsive: true, 
                plugins: { 
                    legend: { display: false, labels: { color: '#111' } } 
                }, 
                scales: { 
                    y: { beginAtZero: true, ticks: { color: '#111' }, grid: { color: '#bbb' } }, 
                    x: { ticks: { color: '#111' }, grid: { color: '#bbb' } } 
                }
            }
        });

        // Cotizaciones por estado
        // Ordenar y colorear: enviada (amarillo), aceptada (verde), rechazada (rojo)
        const estados = ['enviada', 'aceptada', 'rechazada'];
        const labelsEstado = estados.map(e => e.charAt(0).toUpperCase() + e.slice(1));
        const valoresEstado = estados.map(e => data.por_estado[e] ?? 0);
        const colores = ['#f1c40f', '#2ecc71', '#e74c3c'];

        chartEstado = new Chart(document.getElementById('chartEstado'), {
            type: 'doughnut',
            data: { labels: labelsEstado, datasets: [{ data: valoresEstado, backgroundColor: colores }] },
            options: { 
                responsive: true, 
                plugins: { 
                    legend: { 
                        position: 'bottom', 
                        labels: { color: '#111' } 
                    } 
                }
            }
        });
    }

    function cargarMetricas() {
        $.ajax({
            url: AJAX_URL,
            method: 'GET',
            data: { accion: 'resumen' },
            dataType: 'json',
            success: function(data) {
                if (data.success) {
                    renderMetricas(data);
                } else {
                    alert(data.mensaje || 'Error al cargar métricas');
                }
            },
            error: function(xhr) {
                alert('Error de conexión: ' + (xhr.responseText || xhr.statusText));
            }
        });
    }

    $(function(){
        cargarMetricas();
    });
})(jQuery);
</script>
</body>
</html>
