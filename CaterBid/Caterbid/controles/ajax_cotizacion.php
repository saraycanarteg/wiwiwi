<?php
// controles/ajax_cotizacion.php
// API para el cotizador — devolver siempre JSON.
// Guardar este archivo en la carpeta controles.

define('DEV_MODE', true); // cambiar a false en producción
if (DEV_MODE) {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
} else {
    error_reporting(0);
}

header('Content-Type: application/json; charset=utf-8');
session_start();

// ruta a tu config (archivo dentro de controles/ -> ../config/database.php)
require_once '../config/database.php';

// Helper para responder y terminar
function resp($payload) {
    echo json_encode($payload);
    exit;
}

// 1) Validaciones básicas
if (!isset($_SESSION['usuario_id'])) {
    resp(['success' => false, 'mensaje' => 'Usuario no autenticado']);
}

if (!isset($conn) || $conn->connect_error) {
    resp(['success' => false, 'mensaje' => 'Error de conexión a la base de datos']);
}

// Obtenemos acción desde POST o GET
$accion = $_POST['accion'] ?? $_GET['accion'] ?? '';
$accion = trim($accion);

// Seguridad mínima: validar acción permitida
$acciones_permitidas = ['tipos_evento','listar_paquetes','productos_paquete','buscar_cliente','preview','guardar','comparar_paquetes','generar_pdf_preview'];
if (!in_array($accion, $acciones_permitidas)) {
    resp(['success' => false, 'mensaje' => 'Acción no soportada']);
}

/* -----------------------
   ACCIÓN: tipos_evento
   Devuelve array de tipos distintos (strings).
------------------------*/
if ($accion === 'tipos_evento') {
    $sql = "SELECT DISTINCT tipo_evento FROM paquete WHERE estado = 'activo'";
    $result = $conn->query($sql);
    if ($result === false) resp(['success' => false, 'mensaje' => 'Error SQL: ' . $conn->error]);
    $tipos = [];
    while ($row = $result->fetch_assoc()) {
        $tipos[] = $row['tipo_evento'];
    }
    resp(['success' => true, 'tipos' => $tipos]);
}

/* -----------------------
   ACCIÓN: listar_paquetes
   Parámetro opcional: tipo_evento
   Devuelve lista de paquetes con 'descripcion_resumida' (AHORA: ARRAY)
------------------------*/
if ($accion === 'listar_paquetes') {
    $tipo = $_POST['tipo_evento'] ?? '';
    $tipo = trim($tipo);

    if ($tipo !== '') {
        $stmt = $conn->prepare("SELECT id_paquete, tipo_evento, fecha_creacion, estado FROM paquete WHERE estado='activo' AND tipo_evento = ? ORDER BY fecha_creacion DESC");
        if ($stmt === false) resp(['success' => false, 'mensaje' => 'Error prepare listar_paquetes: ' . $conn->error]);
        $stmt->bind_param("s", $tipo);
    } else {
        $stmt = $conn->prepare("SELECT id_paquete, tipo_evento, fecha_creacion, estado FROM paquete WHERE estado='activo' ORDER BY fecha_creacion DESC");
        if ($stmt === false) resp(['success' => false, 'mensaje' => 'Error prepare listar_paquetes: ' . $conn->error]);
    }

    if (!$stmt->execute()) resp(['success' => false, 'mensaje' => 'Error execute listar_paquetes: ' . $stmt->error]);
    $res = $stmt->get_result();
    $paquetes = [];

    // para cada paquete generamos descripcion resumida a partir de paquete_producto + producto
    $stmt_items = $conn->prepare("SELECT pr.id_producto, pr.nombre, pr.precio_unitario, pp.cantidad_producto FROM paquete_producto pp JOIN producto pr ON pp.id_producto = pr.id_producto WHERE pp.id_paquete = ?");
    if ($stmt_items === false) resp(['success' => false, 'mensaje' => 'Error prepare paquete items: ' . $conn->error]);

    while ($p = $res->fetch_assoc()) {
        $pid = intval($p['id_paquete']);
        $stmt_items->bind_param("i", $pid);
        if (!$stmt_items->execute()) {
            resp(['success' => false, 'mensaje' => 'Error execute paquete items: ' . $stmt_items->error]);
        }
        $ri = $stmt_items->get_result();
        $descArr = [];
        while ($it = $ri->fetch_assoc()) {
            $descArr[] = $it['nombre'] . ' (x' . $it['cantidad_producto'] . ')';
        }
        // <-- AHORA DEVOLVEMOS UN ARRAY, NO UN STRING
        $p['descripcion_resumida'] = $descArr;
        $paquetes[] = $p;
    }

    resp(['success' => true, 'paquetes' => $paquetes]);
}

/* -----------------------
   ACCIÓN: productos_paquete
   POST: id_paquete
   Devuelve productos: id_producto, nombre, precio_unitario, cantidad_disponible, cantidad_default
------------------------*/
if ($accion === 'productos_paquete') {
    $id = intval($_POST['id_paquete'] ?? 0);
    if ($id <= 0) resp(['success' => false, 'mensaje' => 'id_paquete inválido']);

    $stmt = $conn->prepare("SELECT pr.id_producto, pr.nombre, pr.precio_unitario, pr.cantidad_disponible, pp.cantidad_producto FROM paquete_producto pp JOIN producto pr ON pp.id_producto = pr.id_producto WHERE pp.id_paquete = ?");
    if ($stmt === false) resp(['success' => false, 'mensaje' => 'Error prepare productos_paquete: ' . $conn->error]);
    $stmt->bind_param("i", $id);
    if (!$stmt->execute()) resp(['success' => false, 'mensaje' => 'Error execute productos_paquete: ' . $stmt->error]);
    $res = $stmt->get_result();
    $productos = [];
    while ($row = $res->fetch_assoc()) {
        $productos[] = [
            'id_producto' => (int)$row['id_producto'],
            'nombre' => $row['nombre'],
            'precio_unitario' => (float)$row['precio_unitario'],
            'cantidad_disponible' => (int)$row['cantidad_disponible'],
            'cantidad_default' => (int)$row['cantidad_producto'],
        ];
    }
    resp(['success' => true, 'productos' => $productos]);
}

/* -----------------------
   ACCIÓN: buscar_cliente
------------------------*/
if ($accion === 'buscar_cliente') {
    $ident = trim($_POST['identificacion'] ?? '');
    if ($ident === '') resp(['success' => false, 'mensaje' => 'Falta identificación']);

    $stmt = $conn->prepare("SELECT id_cliente, nombres, identificacion, celular, telefono_fijo, correo, direccion, ciudad, tipo_cliente, nombre_empresa FROM cliente WHERE identificacion = ?");
    if ($stmt === false) resp(['success' => false, 'mensaje' => 'Error prepare buscar_cliente: ' . $conn->error]);
    $stmt->bind_param("s", $ident);
    if (!$stmt->execute()) resp(['success' => false, 'mensaje' => 'Error execute buscar_cliente: ' . $stmt->error]);
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        resp(['success' => true, 'cliente' => $row]);
    } else {
        resp(['success' => true, 'cliente' => null]);
    }
}

/* -----------------------
   ACCIÓN: preview
------------------------*/
if ($accion === 'preview') {
    $paquete_id = intval($_POST['paquete_id'] ?? 0);
    $cliente = json_decode($_POST['cliente'] ?? '{}', true);
    $items = json_decode($_POST['items'] ?? '[]', true);
    $paquete_nombre = $_POST['paquete_nombre'] ?? '';
    $paquete_proveedor = $_POST['paquete_proveedor'] ?? '';

    if ($paquete_id <= 0) resp(['success' => false, 'mensaje' => 'Paquete inválido']);
    if (!is_array($items) || count($items) === 0) resp(['success' => false, 'mensaje' => 'No hay items']);

    // obtener info paquete
    $stmt = $conn->prepare("SELECT id_paquete, tipo_evento FROM paquete WHERE id_paquete = ?");
    if ($stmt === false) resp(['success' => false, 'mensaje' => 'Error prepare preview paquete: ' . $conn->error]);
    $stmt->bind_param("i", $paquete_id);
    if (!$stmt->execute()) resp(['success' => false, 'mensaje' => 'Error execute preview paquete: ' . $stmt->error]);
    $pinfo = $stmt->get_result()->fetch_assoc() ?: null;
    if (!$pinfo) resp(['success' => false, 'mensaje' => 'Paquete no encontrado']);

    // Build HTML seguro (escapar datos cliente)
    $cn = htmlspecialchars($cliente['nombres'] ?? 'N/D', ENT_QUOTES, 'UTF-8');
    $ci = htmlspecialchars($cliente['identificacion'] ?? '', ENT_QUOTES, 'UTF-8');
    $mail = htmlspecialchars($cliente['correo'] ?? '', ENT_QUOTES, 'UTF-8');
    $cel = htmlspecialchars($cliente['celular'] ?? '', ENT_QUOTES, 'UTF-8');
    $dir = htmlspecialchars($cliente['direccion'] ?? '', ENT_QUOTES, 'UTF-8');

    $html = "<h4>Ficha de cotización</h4>";
    $html .= "<p><strong>Paquete:</strong> " . htmlspecialchars($paquete_nombre, ENT_QUOTES, 'UTF-8') . "</p>";
    $html .= "<p><strong>Proveedor:</strong> " . htmlspecialchars($paquete_proveedor, ENT_QUOTES, 'UTF-8') . "</p>";
    $html .= "<p><strong>Tipo de evento:</strong> " . htmlspecialchars($pinfo['tipo_evento'], ENT_QUOTES, 'UTF-8') . "</p>";
    $html .= "<h5>Cliente</h5>";
    $html .= "<p>{$cn}<br>Identificación: {$ci}<br>Correo: {$mail}<br>Tel: {$cel}<br>Dirección: {$dir}</p>";

    $html .= "<h5>Productos</h5><table class='table'><thead><tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Subtotal</th></tr></thead><tbody>";
    $base = 0.0;

    // Para cada item, obtener nombre + precio desde DB (evita confiar en precio enviado por cliente)
    $stmtp = $conn->prepare("SELECT nombre, precio_unitario, cantidad_disponible FROM producto WHERE id_producto = ?");
    if ($stmtp === false) resp(['success' => false, 'mensaje' => 'Error prepare preview producto: ' . $conn->error]);

    foreach ($items as $it) {
        $idp = intval($it['id_producto'] ?? 0);
        $cant = intval($it['cantidad'] ?? 0);
        if ($idp <= 0 || $cant <= 0) continue;

        $stmtp->bind_param("i", $idp);
        if (!$stmtp->execute()) resp(['success' => false, 'mensaje' => 'Error execute preview producto: ' . $stmtp->error]);
        $prow = $stmtp->get_result()->fetch_assoc();
        if (!$prow) {
            $html .= "<tr><td>Producto #{$idp} (no encontrado)</td><td>{$cant}</td><td>0.00</td><td>0.00</td></tr>";
            continue;
        }
        $precio = (float)$prow['precio_unitario'];
        $subtotal = $precio * $cant;
        $base += $subtotal;
        $html .= "<tr><td>".htmlspecialchars($prow['nombre'], ENT_QUOTES, 'UTF-8')."</td><td>{$cant}</td><td>".number_format($precio,2)."</td><td>".number_format($subtotal,2)."</td></tr>";
    }

    $iva = round($base * 0.12, 2);
    $total = round($base + $iva, 2);
    $html .= "</tbody></table>";
    $html .= "<p><strong>Base:</strong> ".number_format($base,2)." <br><strong>IVA (12%):</strong> ".number_format($iva,2)." <br><strong>Total:</strong> ".number_format($total,2)."</p>";

    resp(['success' => true, 'html' => $html]);
}

/* -----------------------
   ACCIÓN: guardar
   POST:
     - paquete_id
     - cliente (JSON)
     - items (JSON array)
     - generar_pdf (0/1) opcional
------------------------*/
if ($accion === 'guardar') {
    $paquete_id = intval($_POST['paquete_id'] ?? 0);
    $cliente = json_decode($_POST['cliente'] ?? '{}', true);
    $items = json_decode($_POST['items'] ?? '[]', true);
    $generar_pdf = (isset($_POST['generar_pdf']) && intval($_POST['generar_pdf']) === 1) ? true : false;

    if ($paquete_id <= 0) resp(['success' => false, 'mensaje' => 'Paquete inválido']);
    if (!is_array($items) || count($items) === 0) resp(['success' => false, 'mensaje' => 'No hay items para cotizar']);
    if (!is_array($cliente) || empty(trim($cliente['identificacion'] ?? ''))) resp(['success' => false, 'mensaje' => 'Falta identificación del cliente']);

    // 1) Validar stock y obtener precio real por producto (evitar confiar en precio enviado)
    $stmtProd = $conn->prepare("SELECT nombre, precio_unitario, cantidad_disponible FROM producto WHERE id_producto = ?");
    if ($stmtProd === false) resp(['success' => false, 'mensaje' => 'Error prepare validar producto: ' . $conn->error]);

    $lineas = []; // vamos a acumular [{id_producto, cantidad, precio_unitario, subtotal}]
    foreach ($items as $it) {
        $idp = intval($it['id_producto'] ?? 0);
        $cant = intval($it['cantidad'] ?? 0);
        if ($idp <= 0 || $cant <= 0) resp(['success' => false, 'mensaje' => 'Item inválido en la solicitud']);
        $stmtProd->bind_param("i", $idp);
        if (!$stmtProd->execute()) resp(['success' => false, 'mensaje' => 'Error execute validar producto: ' . $stmtProd->error]);
        $prow = $stmtProd->get_result()->fetch_assoc();
        if (!$prow) resp(['success' => false, 'mensaje' => "Producto ID {$idp} no encontrado"]);
        if ($cant > intval($prow['cantidad_disponible'])) {
            resp(['success' => false, 'mensaje' => "Stock insuficiente para {$prow['nombre']}. Disponible: {$prow['cantidad_disponible']}, pedido: {$cant}"]);
        }
        $precio = (float)$prow['precio_unitario'];
        $subtotal = round($precio * $cant, 2);
        $lineas[] = [
            'id_producto' => $idp,
            'cantidad' => $cant,
            'precio_unitario' => $precio,
            'subtotal' => $subtotal
        ];
    }

    // 2) Iniciar transacción
    $conn->begin_transaction();
    try {
        // 2.1) Cliente: buscar por identificacion; si existe actualizar; si no insertar
        $ident = trim($cliente['identificacion']);
        $stmtC = $conn->prepare("SELECT id_cliente FROM cliente WHERE identificacion = ?");
        if ($stmtC === false) throw new Exception('Error prepare cliente select: ' . $conn->error);
        $stmtC->bind_param("s", $ident);
        if (!$stmtC->execute()) throw new Exception('Error execute cliente select: ' . $stmtC->error);
        $resC = $stmtC->get_result();
        if ($rowC = $resC->fetch_assoc()) {
            $id_cliente = (int)$rowC['id_cliente'];
            // actualizar campos relevantes (evita sobreescribir campos no enviados)
            $stmtUpd = $conn->prepare("UPDATE cliente SET nombres = ?, correo = ?, celular = ?, direccion = ? WHERE id_cliente = ?");
            if ($stmtUpd === false) throw new Exception('Error prepare cliente update: ' . $conn->error);
            $nombres = trim($cliente['nombres'] ?? '');
            $correo = trim($cliente['correo'] ?? '');
            $cel = trim($cliente['celular'] ?? '');
            $direccion = trim($cliente['direccion'] ?? '');
            $stmtUpd->bind_param("ssssi", $nombres, $correo, $cel, $direccion, $id_cliente);
            if (!$stmtUpd->execute()) throw new Exception('Error execute cliente update: ' . $stmtUpd->error);
        } else {
            // insertar cliente. tabla cliente requiere ciudad, tipo_cliente; ponemos valores por defecto si no vienen
            $stmtIns = $conn->prepare("INSERT INTO cliente (nombres, identificacion, celular, correo, direccion, ciudad, tipo_cliente, estado) VALUES (?, ?, ?, ?, ?, ?, ?, 'activo')");
            if ($stmtIns === false) throw new Exception('Error prepare cliente insert final: ' . $conn->error);
            $nombres = trim($cliente['nombres'] ?? '');
            $correo = trim($cliente['correo'] ?? '');
            $cel = trim($cliente['celular'] ?? '');
            $direccion = trim($cliente['direccion'] ?? '');
            $ciudad = trim($cliente['ciudad'] ?? 'N/D');
            $tipo_cliente = trim($cliente['tipo_cliente'] ?? 'persona_natural');
            $stmtIns->bind_param("sssssss", $nombres, $ident, $cel, $correo, $direccion, $ciudad, $tipo_cliente);
            if (!$stmtIns->execute()) throw new Exception('Error execute cliente insert: ' . $stmtIns->error);
            $id_cliente = $conn->insert_id;
        }

        // 2.2) Insertar cotizacion (calcular base, iva, total con las lineas)
        $base = 0.0;
        foreach ($lineas as $l) $base += $l['subtotal'];
        $iva = round($base * 0.12, 2);
        $total = round($base + $iva, 2);

        $sql = "INSERT INTO cotizacion (id_cliente, id_paquete, base_imponible, iva, total, estado, pdf_url) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        // Estado siempre 'enviada' al crear
        $estado = 'enviada';
        $pdf_url = null;
        $stmt->bind_param("iiddsss", $id_cliente, $paquete_id, $base, $iva, $total, $estado, $pdf_url);
        if (!$stmt->execute()) throw new Exception('Error execute cotizacion insert: ' . $stmt->error);
        $id_cotizacion = $conn->insert_id;

        // 2.3) Insertar detalles
        $stmt_det = $conn->prepare("INSERT INTO cotizacion_detalle (id_cotizacion, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
        if ($stmt_det === false) throw new Exception('Error prepare cotizacion_detalle insert: ' . $conn->error);
        foreach ($lineas as $l) {
            $id_producto = $l['id_producto'];
            $cantidad = $l['cantidad'];
            $precio_unitario = $l['precio_unitario'];
            $subtotal = $l['subtotal'];
            // CORREGIDO: 5 tipos y 5 variables
            $stmt_det->bind_param("iiidd", $id_cotizacion, $id_producto, $cantidad, $precio_unitario, $subtotal);
            if (!$stmt_det->execute()) throw new Exception('Error execute cotizacion_detalle insert: ' . $stmt_det->error);
        }

        // 2.4) (Opcional) generar PDF y actualizar campo pdf_url en cotizacion
        if ($generar_pdf) {
            // Aquí debes integrar tu librería favorita (dompdf/tcpdf). Como ejemplo dejamos null.
            // Si quieres puedo integrarlo después.
        }

        $conn->commit();
        resp(['success' => true, 'id_cotizacion' => $id_cotizacion, 'pdf_url' => $pdf_url]);

    } catch (Exception $e) {
        $conn->rollback();
        $msg = DEV_MODE ? $e->getMessage() : 'Error al guardar cotización';
        resp(['success' => false, 'mensaje' => $msg]);
    }
}

/* -----------------------
   ACCIÓN: comparar_paquetes
   POST: ids (JSON array de id_paquete)
   Devuelve: {categorias: [...], paquetes: {id_paquete: {categoria: [productos]}}}
------------------------*/
if ($accion === 'comparar_paquetes') {
    $ids = json_decode($_POST['ids'] ?? '[]', true);
    if (!is_array($ids) || count($ids) === 0) resp(['success' => false, 'mensaje' => 'No hay paquetes para comparar']);
    // Limitar a 3 paquetes
    $ids = array_slice($ids, 0, 3);
    $paquetes = [];
    $categorias_set = [];
    $proveedores = [];
    foreach ($ids as $pid) {
        $pid = intval($pid);
        // Obtener nombre del proveedor
        $stmtProv = $conn->prepare("SELECT pr.nombre AS proveedor FROM paquete p LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor WHERE p.id_paquete = ?");
        if ($stmtProv === false) resp(['success' => false, 'mensaje' => 'Error prepare proveedor: ' . $conn->error]);
        $stmtProv->bind_param("i", $pid);
        if (!$stmtProv->execute()) resp(['success' => false, 'mensaje' => 'Error execute proveedor: ' . $stmtProv->error]);
        $provRow = $stmtProv->get_result()->fetch_assoc();
        $proveedores[$pid] = $provRow ? $provRow['proveedor'] : 'Sin proveedor';

        // Obtener productos y categorías
        $stmt = $conn->prepare("SELECT pr.id_producto, pr.nombre, pr.precio_unitario, pr.cantidad_disponible, pp.cantidad_producto, pr.categoria
            FROM paquete_producto pp
            JOIN producto pr ON pp.id_producto = pr.id_producto
            WHERE pp.id_paquete = ?");
        if ($stmt === false) resp(['success' => false, 'mensaje' => 'Error prepare comparar_paquetes: ' . $conn->error]);
        $stmt->bind_param("i", $pid);
        if (!$stmt->execute()) resp(['success' => false, 'mensaje' => 'Error execute comparar_paquetes: ' . $stmt->error]);
        $res = $stmt->get_result();
        $cat_map = [];
        while ($row = $res->fetch_assoc()) {
            $cat = $row['categoria'] ?: 'Sin categoría';
            $categorias_set[$cat] = true;
            if (!isset($cat_map[$cat])) $cat_map[$cat] = [];
            $cat_map[$cat][] = [
                'id_producto' => (int)$row['id_producto'],
                'nombre' => $row['nombre'],
                'precio_unitario' => (float)$row['precio_unitario'],
                'cantidad_disponible' => (int)$row['cantidad_disponible'],
                'cantidad_default' => (int)$row['cantidad_producto'],
                'categoria' => $cat
            ];
        }
        $paquetes[$pid] = $cat_map;
    }
    $categorias = array_keys($categorias_set);
    // Ordenar categorías como en el select de frontend
    $orden = [
        "Comida","Bebidas","Menaje y utensilios","Equipos y mobiliario",
        "Personal y servicios","Decoración y ambientación","Sin categoría"
    ];
    usort($categorias, function($a,$b) use($orden){
        $ia = array_search($a,$orden); $ib = array_search($b,$orden);
        if ($ia === false) $ia = 99;
        if ($ib === false) $ib = 99;
        return $ia - $ib;
    });
    resp(['success'=>true,'categorias'=>$categorias,'paquetes'=>$paquetes,'proveedores'=>$proveedores]);
}

/* -----------------------
   ACCIÓN: generar_pdf_preview
   POST: datos (JSON)
   Genera PDF temporal para previsualización sin guardar en BD
------------------------*/
if ($accion === 'generar_pdf_preview') {
    $datos = json_decode($_POST['datos'] ?? '{}', true);
    
    if (!is_array($datos) || empty($datos)) {
        resp(['success' => false, 'mensaje' => 'Datos inválidos para generar PDF']);
    }
    
    $paquete_id = intval($datos['paquete_id'] ?? 0);
    $items = $datos['items'] ?? [];
    $cliente_temporal = $datos['cliente_temporal'] ?? [];
    $iva_porcentaje = floatval($datos['iva_porcentaje'] ?? 15);
    
    if ($paquete_id <= 0) resp(['success' => false, 'mensaje' => 'Paquete inválido']);
    if (!is_array($items) || count($items) === 0) resp(['success' => false, 'mensaje' => 'No hay items para el PDF']);
    
    try {
        // Validar productos y calcular totales
        $stmtProd = $conn->prepare("SELECT nombre, precio_unitario FROM producto WHERE id_producto = ?");
        if ($stmtProd === false) throw new Exception('Error prepare validar producto: ' . $conn->error);
        
        $lineas_pdf = [];
        $base = 0.0;
        
        foreach ($items as $it) {
            $idp = intval($it['id_producto'] ?? 0);
            $cant = intval($it['cantidad'] ?? 0);
            if ($idp <= 0 || $cant <= 0) continue;
            
            $stmtProd->bind_param("i", $idp);
            if (!$stmtProd->execute()) throw new Exception('Error execute validar producto: ' . $stmtProd->error);
            $prow = $stmtProd->get_result()->fetch_assoc();
            if (!$prow) continue;
            
            $precio = (float)$prow['precio_unitario'];
            $subtotal = round($precio * $cant, 2);
            $base += $subtotal;
            
            $lineas_pdf[] = [
                'nombre' => $prow['nombre'],
                'cantidad' => $cant,
                'precio_unitario' => $precio,
                'subtotal' => $subtotal
            ];
        }
        
        $iva = round($base * ($iva_porcentaje/100), 2);
        $total = round($base + $iva, 2);
        
        // Obtener información del paquete
        $stmtPaq = $conn->prepare("SELECT tipo_evento FROM paquete WHERE id_paquete = ?");
        if ($stmtPaq === false) throw new Exception('Error prepare paquete info: ' . $conn->error);
        $stmtPaq->bind_param("i", $paquete_id);
        if (!$stmtPaq->execute()) throw new Exception('Error execute paquete info: ' . $stmtPaq->error);
        $paq_info = $stmtPaq->get_result()->fetch_assoc();
        
        // Generar nombre único para el archivo temporal
        $timestamp = date('Y-m-d_H-i-s');
        $filename = "cotizacion_preview_{$timestamp}_{$paquete_id}.pdf";
        $filepath = "../recursos/pdf/temp/{$filename}";
        
        // Crear directorio si no existe
        $temp_dir = "../recursos/pdf/temp";
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
        
        // Generar contenido HTML para el PDF
        // Agregar el indicador de preview a los datos
        $datos['es_preview'] = true;
        $html_content = generarHTMLParaPDF($datos, $lineas_pdf, $base, $iva, $total, $paq_info);
        
        // Aquí deberías integrar tu librería de PDF (DOMPDF, TCPDF, etc.)
        // Por ahora, crearemos un archivo HTML temporal como ejemplo
        file_put_contents($filepath . '.html', $html_content);
        
        // Si tienes DOMPDF instalado, descomenta y ajusta estas líneas:
        /*
        require_once '../vendor/autoload.php';
        use Dompdf\Dompdf;
        use Dompdf\Options;
        
        $options = new Options();
        $options->set('defaultFont', 'Arial');
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html_content);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        file_put_contents($filepath, $dompdf->output());
        */
        
        // Por ahora retornamos la URL del HTML
        $pdf_url = str_replace("../", "", $filepath . '.html');
        
        resp(['success' => true, 'pdf_url' => $pdf_url, 'mensaje' => 'PDF de previsualización generado']);
        
    } catch (Exception $e) {
        $msg = DEV_MODE ? $e->getMessage() : 'Error al generar PDF de previsualización';
        resp(['success' => false, 'mensaje' => $msg]);
    }
}

function generarHTMLParaPDF($datos, $lineas, $base, $iva, $total, $paq_info) {
    $cliente = $datos['cliente_temporal'];
    $paquete_nombre = htmlspecialchars($datos['paquete_nombre'] ?? '', ENT_QUOTES, 'UTF-8');
    $paquete_proveedor = htmlspecialchars($datos['paquete_proveedor'] ?? '', ENT_QUOTES, 'UTF-8');
    $tipo_evento = htmlspecialchars($paq_info['tipo_evento'] ?? '', ENT_QUOTES, 'UTF-8');
    $iva_porcentaje = floatval($datos['iva_porcentaje'] ?? 15);
    $es_preview = $datos['es_preview'] ?? true;
    
    // Determinar el tipo de documento
    $titulo_documento = $es_preview ? 'COTIZACIÓN - PREVISUALIZACIÓN' : 'COTIZACIÓN CON DATOS REALES';
    $nota_cliente = $es_preview ? 
        'Esta es una previsualización. Los datos del cliente se completarán al generar la cotización final.' :
        'Cotización generada con datos reales del cliente.';
    
    $html = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <title>Cotización - {$titulo_documento}</title>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; }
            .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3498db; padding-bottom: 15px; }
            .info-section { margin-bottom: 20px; }
            .info-section h3 { color: #2c3e50; margin-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
            th { background-color: #f8f9fa; font-weight: bold; }
            .totals { float: right; width: 300px; }
            .total-row { font-weight: bold; background-color: #e3f2fd; }
            .preview-notice { background-color: #fff3cd; border: 1px solid #ffeaa7; padding: 15px; border-radius: 5px; margin-top: 20px; }
            .cliente-real { background-color: #d4edda; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h1>{$titulo_documento}</h1>
            <p>Documento generado el " . date('d/m/Y H:i:s') . "</p>
        </div>
        
        <div class='info-section'>
            <h3>Información del Paquete</h3>
            <p><strong>Paquete:</strong> {$paquete_nombre}</p>
            <p><strong>Proveedor:</strong> {$paquete_proveedor}</p>
            <p><strong>Tipo de Evento:</strong> {$tipo_evento}</p>
        </div>
        
        <div class='info-section'>
            <h3>Datos del Cliente</h3>";
    
    if (!$es_preview) {
        $html .= "<div class='cliente-real'>";
    }
    
    $html .= "
            <table style='border: none; margin-bottom: 10px;'>
                <tr>
                    <td style='border: none; width: 120px;'><strong>Nombre/Empresa:</strong></td>
                    <td style='border: none;'>" . htmlspecialchars($cliente['nombres'], ENT_QUOTES, 'UTF-8') . "</td>
                </tr>
                <tr>
                    <td style='border: none;'><strong>Identificación:</strong></td>
                    <td style='border: none;'>" . htmlspecialchars($cliente['identificacion'], ENT_QUOTES, 'UTF-8') . "</td>
                </tr>
                <tr>
                    <td style='border: none;'><strong>Correo:</strong></td>
                    <td style='border: none;'>" . htmlspecialchars($cliente['correo'], ENT_QUOTES, 'UTF-8') . "</td>
                </tr>
                <tr>
                    <td style='border: none;'><strong>Celular:</strong></td>
                    <td style='border: none;'>" . htmlspecialchars($cliente['celular'], ENT_QUOTES, 'UTF-8') . "</td>
                </tr>
                <tr>
                    <td style='border: none;'><strong>Ciudad:</strong></td>
                    <td style='border: none;'>" . htmlspecialchars($cliente['ciudad'] ?? 'No especificada', ENT_QUOTES, 'UTF-8') . "</td>
                </tr>
                <tr>
                    <td style='border: none;'><strong>Dirección:</strong></td>
                    <td style='border: none;'>" . htmlspecialchars($cliente['direccion'] ?? 'No especificada', ENT_QUOTES, 'UTF-8') . "</td>
                </tr>
            </table>";
    
    if (!$es_preview) {
        $html .= "</div>";
    } else {
        $html .= "<p><em>{$nota_cliente}</em></p>";
    }
    
    $html .= "
        </div>
        
        <div class='info-section'>
            <h3>Detalle de Productos</h3>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>";
    
    foreach ($lineas as $linea) {
        $html .= "
                    <tr>
                        <td>" . htmlspecialchars($linea['nombre'], ENT_QUOTES, 'UTF-8') . "</td>
                        <td>{$linea['cantidad']}</td>
                        <td>$" . number_format($linea['precio_unitario'], 2) . "</td>
                        <td>$" . number_format($linea['subtotal'], 2) . "</td>
                    </tr>";
    }
    
    $html .= "
                </tbody>
            </table>
        </div>
        
        <div class='totals'>
            <table>
                <tr>
                    <td><strong>Subtotal:</strong></td>
                    <td>$" . number_format($base, 2) . "</td>
                </tr>
                <tr>
                    <td><strong>IVA ({$iva_porcentaje}%):</strong></td>
                    <td>$" . number_format($iva, 2) . "</td>
                </tr>
                <tr class='total-row'>
                    <td><strong>TOTAL:</strong></td>
                    <td><strong>$" . number_format($total, 2) . "</strong></td>
                </tr>
            </table>
        </div>
        
        <div style='clear: both;'></div>";
    
    if ($es_preview) {
        $html .= "
        <div class='preview-notice'>
            <strong>NOTA:</strong> Este es un documento de previsualización. Para generar una cotización oficial, 
            complete los datos del cliente y use la opción 'Cotizar' en el sistema.
        </div>";
    }
    
    $html .= "
    </body>
    </html>";
    
    return $html;
}

// fallback
resp(['success' => false, 'mensaje' => 'Acción no encontrada']);
