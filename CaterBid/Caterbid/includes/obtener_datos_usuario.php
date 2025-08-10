<?php
session_start();
header('Content-Type: application/json');

// Validar login
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit;
}

// Mapa permisos con  icono + archivo
$mapaPermisos = [
    'gestion_rolperm' => ['icon' => 'fas fa-user-shield', 'file' => '../paneles/admin/gestion_rolperm.html', 'title' => 'Gestionar Roles y Permisos', 'orden' => 3],
    'gestion_usuario' => ['icon' => 'fas fa-users-cog', 'file' => '../paneles/admin/gestion_usuario.html', 'title' => 'Gestión Usuarios','orden' => 4],
    'revisar_logs' => ['icon' => 'fas fa-clipboard-list', 'file' => '../paneles/admin/auditorias.php', 'title' => 'Logs y Auditoría', 'orden' => 1],
    'metricas_admin' => ['icon' => 'fas fa-chart-line', 'file' => '../paneles/admin/metricas_admin.html', 'title' => 'Métricas Administrador', 'orden' => 5],
    'gestionar_proveedor' => ['icon' => 'fas fa-truck', 'file' => '../paneles/admin/gestionar_proveedor.php', 'title' => 'Registrar Proveedor', 'orden' => 2],
    'gestionar_productos' => ['icon' => 'fas fa-boxes', 'file' => '../paneles/bodeguero/gestionar_productos.html', 'title' => 'Gestionar Productos'],
    'gestion_paquete' => ['icon' => 'fas fa-box-open', 'file' => '../paneles/bodeguero/gestion_paquete.html', 'title' => 'Gestionar Paquete'],
    'metricas_bodeguero' => ['icon' => 'fas fa-chart-pie', 'file' => '../paneles/bodeguero/metricas_bodeguero.html', 'title' => 'Métricas Bodeguero'],
    'cotizar_paquete' => ['icon' => 'fas fa-file-invoice-dollar', 'file' => '../paneles/cotizador/cotizacion.html', 'title' => 'Cotizar Paquete'],
    'historial_cotizaciones' => ['icon' => 'fas fa-history', 'file' => '../paneles/cotizador/historial_cotizaciones.html', 'title' => 'Historial Cotizaciones'],
    'metricas_cotizador' => ['icon' => 'fas fa-chart-pie', 'file' => '../paneles/cotizador/metricas_cotizador.html', 'title' => 'Métricas Cotizador']
];

$permisosUsuario = [];
foreach ($_SESSION['usuario']['permisos'] as $permiso) {
    if (isset($mapaPermisos[$permiso])) {
        $permisosUsuario[] = $mapaPermisos[$permiso];
    }
}
usort($permisosUsuario, function($a, $b) {
    return $a['orden'] - $b['orden'];
});

echo json_encode([
    'nombre' => $_SESSION['usuario']['nombre'],
    'rol' => $_SESSION['usuario']['rol_nombre'],
    'permisos' => $permisosUsuario
]);
