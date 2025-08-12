<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once '../config/database.php';

if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success' => false, 'mensaje' => 'No autenticado']);
    exit();
}
if (!isset($conn) || !$conn || $conn->connect_error) {
    echo json_encode(['success' => false, 'mensaje' => 'Error de conexión a la base de datos']);
    exit();
}

$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

switch ($accion) {
    case 'resumen':
        // Cotizaciones por mes (mostrar todos los meses del año actual, aunque no tengan cotizaciones)
        $year = date('Y');
        $meses = [];
        for ($m = 1; $m <= 12; $m++) {
            $mes_num = str_pad($m, 2, '0', STR_PAD_LEFT);
            $meses["$year-$mes_num"] = 0;
        }
        $sql = "SELECT DATE_FORMAT(fecha_envio, '%Y-%m') AS mes, COUNT(*) AS total
                FROM cotizacion
                WHERE YEAR(fecha_envio) = YEAR(CURDATE())
                GROUP BY mes
                ORDER BY mes ASC";
        $res = $conn->query($sql);
        while ($row = $res->fetch_assoc()) {
            $meses[$row['mes']] = (int)$row['total'];
        }
        $por_mes = [];
        foreach ($meses as $mes => $total) {
            $por_mes[] = ['mes' => $mes, 'total' => $total];
        }

        // Cotizaciones por día (últimos 30 días, días sin cotizaciones = 0, extremos incluidos)
        $dias = [];
        $data_dias = [];
        $today = new DateTime();
        $start = (clone $today)->modify('-29 days');
        $period = new DatePeriod($start, new DateInterval('P1D'), (clone $today)->modify('+1 day'));
        foreach ($period as $d) {
            $dia = $d->format('Y-m-d');
            $dias[$dia] = 0;
        }
        $sql = "SELECT DATE(fecha_envio) AS dia, COUNT(*) AS total
                FROM cotizacion
                WHERE fecha_envio >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
                GROUP BY dia";
        $res = $conn->query($sql);
        while ($row = $res->fetch_assoc()) {
            $dias[$row['dia']] = (int)$row['total'];
        }
        foreach ($dias as $dia => $total) {
            $data_dias[] = ['dia' => $dia, 'total' => $total];
        }

        // Paquete más cotizado
        $sql = "SELECT p.id_paquete, p.tipo_evento, pr.nombre AS proveedor, COUNT(*) AS total
                FROM cotizacion c
                JOIN paquete p ON c.id_paquete = p.id_paquete
                LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
                GROUP BY c.id_paquete
                ORDER BY total DESC
                LIMIT 1";
        $paquete_top = $conn->query($sql)->fetch_assoc() ?: [];

        // Cliente con más cotizaciones
        $sql = "SELECT cli.nombres, cli.identificacion, COUNT(*) AS total
                FROM cotizacion c
                JOIN cliente cli ON c.id_cliente = cli.id_cliente
                GROUP BY c.id_cliente
                ORDER BY total DESC
                LIMIT 1";
        $cliente_top = $conn->query($sql)->fetch_assoc() ?: [];

        // Total cotizaciones
        $total_cotizaciones = (int)$conn->query("SELECT COUNT(*) AS total FROM cotizacion")->fetch_assoc()['total'];

        // Cotizaciones por estado (mostrar todos los posibles estados aunque sean 0)
        $estados_posibles = ['enviada', 'aceptada', 'rechazada'];
        $sql = "SELECT estado, COUNT(*) AS total FROM cotizacion GROUP BY estado";
        $res = $conn->query($sql);
        $por_estado = array_fill_keys($estados_posibles, 0);
        while ($row = $res->fetch_assoc()) {
            $estado = $row['estado'] ?: 'enviada';
            $por_estado[$estado] = (int)$row['total'];
        }

        // Proveedor con más cotizaciones
        $sql = "SELECT pr.nombre, COUNT(*) AS total
                FROM cotizacion c
                JOIN paquete p ON c.id_paquete = p.id_paquete
                LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
                GROUP BY pr.id_proveedor
                ORDER BY total DESC
                LIMIT 1";
        $proveedor_top = $conn->query($sql)->fetch_assoc() ?: [];

        // Top 5 paquetes más cotizados
        $sql = "SELECT p.id_paquete, p.tipo_evento, COUNT(*) AS total
                FROM cotizacion c
                JOIN paquete p ON c.id_paquete = p.id_paquete
                GROUP BY c.id_paquete
                ORDER BY total DESC
                LIMIT 5";
        $res = $conn->query($sql);
        $top_paquetes = [];
        while ($row = $res->fetch_assoc()) {
            $top_paquetes[] = [
                'id_paquete' => $row['id_paquete'],
                'tipo_evento' => $row['tipo_evento'],
                'total' => (int)$row['total']
            ];
        }

        echo json_encode([
            'success' => true,
            'por_mes' => $por_mes,
            'por_dia' => $data_dias,
            'paquete_top' => $paquete_top,
            'cliente_top' => $cliente_top,
            'total_cotizaciones' => $total_cotizaciones,
            'por_estado' => $por_estado,
            'proveedor_top' => $proveedor_top,
            'top_paquetes' => $top_paquetes
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'mensaje' => 'Acción inválida']);
}
?>
