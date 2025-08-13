<?php
// Mostrar errores para depuración
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo json_encode(['success'=>false, 'mensaje'=>'No autenticado']);
    exit();
}

// Ruta ajustada para /controles/
require_once '../config/database.php';
if (!isset($conn) || $conn->connect_error) {
    echo json_encode(['success'=>false, 'mensaje'=>'Error de conexión a la base de datos']);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

$accion = $_POST['accion'] ?? '';

switch ($accion) {
    case 'listar_todas':
        // Consulta para mostrar todas las cotizaciones ordenadas por fecha de creación
        $sql = "SELECT 
                    c.id_cotizacion, 
                    c.fecha_envio, 
                    cli.nombres, 
                    cli.identificacion,
                    p.tipo_evento, 
                    pr.nombre AS proveedor_nombre,
                    c.total, 
                    c.estado
                FROM cotizacion c
                JOIN cliente cli ON cli.id_cliente = c.id_cliente
                JOIN paquete p ON p.id_paquete = c.id_paquete
                LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
                ORDER BY c.fecha_envio DESC";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success'=>false, 'mensaje'=>'Error prepare: '.$conn->error]);
            exit();
        }
        if (!$stmt->execute()) {
            echo json_encode(['success'=>false, 'mensaje'=>'Error execute: '.$stmt->error]);
            exit();
        }
        $res = $stmt->get_result();
        $datos = [];
        while ($row = $res->fetch_assoc()) {
            // Formatear la fecha
            $row['fecha_formateada'] = date('d/m/Y H:i', strtotime($row['fecha_envio']));
            $datos[] = $row;
        }
        echo json_encode(['success'=>true, 'datos'=>$datos]);
    break;

    case 'obtener_cotizacion_completa':
        $id_cotizacion = intval($_POST['id_cotizacion'] ?? 0);
        if ($id_cotizacion <= 0) {
            echo json_encode(['success'=>false, 'mensaje'=>'ID cotización inválido']);
            exit();
        }

        // Obtener datos completos de la cotización
        $sql = "SELECT 
                    c.id_cotizacion, 
                    c.fecha_envio, 
                    c.total,
                    c.iva_porcentaje,
                    c.estado,
                    cli.nombres, 
                    cli.identificacion,
                    cli.correo,
                    cli.celular,
                    cli.ciudad,
                    cli.direccion,
                    p.tipo_evento,
                    p.id_paquete,
                    pr.nombre AS proveedor_nombre
                FROM cotizacion c
                JOIN cliente cli ON cli.id_cliente = c.id_cliente
                JOIN paquete p ON p.id_paquete = c.id_paquete
                LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
                WHERE c.id_cotizacion = ?";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success'=>false, 'mensaje'=>'Error prepare cotización: '.$conn->error]);
            exit();
        }
        $stmt->bind_param('i', $id_cotizacion);
        if (!$stmt->execute()) {
            echo json_encode(['success'=>false, 'mensaje'=>'Error execute cotización: '.$stmt->error]);
            exit();
        }
        $res = $stmt->get_result();
        $cotizacion = $res->fetch_assoc();
        
        if (!$cotizacion) {
            echo json_encode(['success'=>false, 'mensaje'=>'Cotización no encontrada']);
            exit();
        }

        // Obtener productos de la cotización
        $sql_productos = "SELECT cd.cantidad, cd.precio_unitario, cd.subtotal, p.nombre, p.descripcion, p.categoria
                FROM cotizacion_detalle cd
                JOIN producto p ON p.id_producto = cd.id_producto
                WHERE cd.id_cotizacion = ?
                ORDER BY p.categoria, p.nombre";
        $stmt_productos = $conn->prepare($sql_productos);
        if (!$stmt_productos) {
            echo json_encode(['success'=>false, 'mensaje'=>'Error prepare productos: '.$conn->error]);
            exit();
        }
        $stmt_productos->bind_param('i', $id_cotizacion);
        if (!$stmt_productos->execute()) {
            echo json_encode(['success'=>false, 'mensaje'=>'Error execute productos: '.$stmt_productos->error]);
            exit();
        }
        $res_productos = $stmt_productos->get_result();
        $productos = [];
        while ($row = $res_productos->fetch_assoc()) {
            $productos[] = $row;
        }

        $cotizacion['productos'] = $productos;
        $cotizacion['fecha_formateada'] = date('d/m/Y H:i', strtotime($cotizacion['fecha_envio']));
        
        echo json_encode(['success'=>true, 'cotizacion'=>$cotizacion]);
    break;

    case 'generar_pdf_cotizacion':
        $id_cotizacion = intval($_POST['id_cotizacion'] ?? 0);
        if ($id_cotizacion <= 0) {
            echo json_encode(['success'=>false, 'mensaje'=>'ID cotización inválido']);
            exit();
        }

        // Incluir la clase PDF (ajustar ruta según tu estructura)
        require_once '../includes/generar_pdf.php';
        
        try {
            $pdf_url = generarPDFCotizacionExistente($id_cotizacion, $conn);
            if ($pdf_url) {
                echo json_encode(['success'=>true, 'pdf_url'=>$pdf_url]);
            } else {
                echo json_encode(['success'=>false, 'mensaje'=>'Error al generar PDF']);
            }
        } catch (Exception $e) {
            echo json_encode(['success'=>false, 'mensaje'=>'Error al generar PDF: ' . $e->getMessage()]);
        }
    break;

    case 'buscar':
        $valor = trim($_POST['valor'] ?? '');
        if ($valor === '') {
            echo json_encode(['success'=>false, 'mensaje'=>'Parámetro vacío']);
            exit();
        }

        // Consulta: cotización + cliente + paquete + proveedor
        $sql = "SELECT 
                    c.id_cotizacion, 
                    c.fecha_envio, 
                    cli.nombres, 
                    cli.identificacion,
                    p.tipo_evento, 
                    pr.nombre AS proveedor_nombre,
                    c.total, 
                    c.estado
                FROM cotizacion c
                JOIN cliente cli ON cli.id_cliente = c.id_cliente
                JOIN paquete p ON p.id_paquete = c.id_paquete
                LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
                WHERE cli.identificacion LIKE ? OR cli.nombres LIKE ?
                ORDER BY c.fecha_envio DESC";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success'=>false, 'mensaje'=>'Error prepare: '.$conn->error]);
            exit();
        }
        $like = '%'.$valor.'%';
        $stmt->bind_param('ss', $like, $like);
        if (!$stmt->execute()) {
            echo json_encode(['success'=>false, 'mensaje'=>'Error execute: '.$stmt->error]);
            exit();
        }
        $res = $stmt->get_result();
        $datos = [];
        while ($row = $res->fetch_assoc()) {
            // Formatear la fecha
            $row['fecha_formateada'] = date('d/m/Y H:i', strtotime($row['fecha_envio']));
            $datos[] = $row;
        }
        echo json_encode(['success'=>true, 'datos'=>$datos]);
    break;

    case 'obtener_productos':
        $id_cotizacion = intval($_POST['id_cotizacion'] ?? 0);
        if ($id_cotizacion <= 0) {
            echo json_encode(['success'=>false, 'mensaje'=>'ID cotización inválido']);
            exit();
        }

        $sql = "SELECT cd.cantidad, cd.precio_unitario, cd.subtotal, p.nombre, p.descripcion, p.categoria
                FROM cotizacion_detalle cd
                JOIN producto p ON p.id_producto = cd.id_producto
                WHERE cd.id_cotizacion = ?
                ORDER BY p.categoria, p.nombre";
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            echo json_encode(['success'=>false, 'mensaje'=>'Error prepare productos: '.$conn->error]);
            exit();
        }
        $stmt->bind_param('i', $id_cotizacion);
        if (!$stmt->execute()) {
            echo json_encode(['success'=>false, 'mensaje'=>'Error execute productos: '.$stmt->error]);
            exit();
        }
        $res = $stmt->get_result();
        $productos = [];
        while ($row = $res->fetch_assoc()) {
            $productos[] = $row;
        }
        echo json_encode(['success'=>true, 'productos'=>$productos]);
    break;

    default:
        echo json_encode(['success'=>false, 'mensaje'=>'Acción inválida']);
}
?>