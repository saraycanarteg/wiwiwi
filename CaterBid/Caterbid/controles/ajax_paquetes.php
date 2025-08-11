<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autenticado']);
    exit();
}

// Verificar permisos específicos para paquetes
$permisos_usuario = isset($_SESSION['usuario']['permisos']) ? $_SESSION['usuario']['permisos'] : [];
$tiene_permiso = in_array('gestion_paquete', $permisos_usuario) || 
                 in_array('crud_paquetes', $permisos_usuario) ||
                 (isset($_SESSION['usuario']['rol_nombre']) && $_SESSION['usuario']['rol_nombre'] == 'bodeguero');

if (!$tiene_permiso) {
    echo json_encode(['success' => false, 'mensaje' => 'No tiene permisos para gestionar paquetes']);
    exit();
}

require_once '../config/database.php';

// Verificar conexión
if (!isset($conn) || $conn->connect_error) {
    echo json_encode(['success' => false, 'mensaje' => 'Error de conexión a BD: ' . (isset($conn) ? $conn->connect_error : 'Variable $conn no definida')]);
    exit();
}

$accion = $_REQUEST['accion'] ?? '';

try {
    switch ($accion) {
        case 'crear':
            $tipo_evento = trim($_POST['tipo_evento'] ?? '');
            $id_proveedor = intval($_POST['id_proveedor'] ?? 0);
            $productos = $_POST['productos'] ?? [];
            
            // Debug para ver qué datos llegan
            error_log("Datos recibidos - tipo_evento: " . $tipo_evento . ", id_proveedor: " . $id_proveedor);
            error_log("Productos recibidos: " . print_r($productos, true));
            error_log("POST completo: " . print_r($_POST, true));
            
            // Validaciones
            if (empty($tipo_evento)) {
                echo json_encode(['success' => false, 'mensaje' => 'El tipo de evento es requerido']);
                exit();
            }
            
            if ($id_proveedor <= 0) {
                echo json_encode(['success' => false, 'mensaje' => 'Debe seleccionar un proveedor válido']);
                exit();
            }
            
            if (empty($productos)) {
                echo json_encode(['success' => false, 'mensaje' => 'Debe seleccionar al menos un producto']);
                exit();
            }
            
            // Verificar que el proveedor existe y está activo
            $stmt = $conn->prepare("SELECT id_proveedor FROM proveedor WHERE id_proveedor = ? AND estado = 'activo'");
            $stmt->bind_param("i", $id_proveedor);
            $stmt->execute();
            if (!$stmt->get_result()->fetch_assoc()) {
                echo json_encode(['success' => false, 'mensaje' => 'El proveedor seleccionado no existe o está inactivo']);
                exit();
            }
            
            // Verificar que todos los productos existen y pertenecen al proveedor
            $productos_int = array_map('intval', $productos);
            $productos_placeholders = str_repeat('?,', count($productos_int) - 1) . '?';
            
            $stmt = $conn->prepare("SELECT id_producto FROM producto WHERE id_producto IN ($productos_placeholders) AND id_proveedor = ? AND estado = 'activo'");
            $types = str_repeat('i', count($productos_int)) . 'i';
            $params = array_merge($productos_int, [$id_proveedor]);
            $stmt->bind_param($types, ...$params);
            $stmt->execute();
            $productos_validos = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            
            if (count($productos_validos) !== count($productos)) {
                echo json_encode(['success' => false, 'mensaje' => 'Algunos productos no existen o no pertenecen al proveedor seleccionado']);
                exit();
            }
            
            // Iniciar transacción
            $conn->begin_transaction();
            
            try {
                // Crear paquete
                $stmt = $conn->prepare("INSERT INTO paquete (tipo_evento, id_proveedor, fecha_creacion, estado) VALUES (?, ?, NOW(), 'activo')");
                $stmt->bind_param("si", $tipo_evento, $id_proveedor);
                
                if (!$stmt->execute()) {
                    throw new Exception("Error al crear el paquete: " . $stmt->error);
                }
                
                $id_paquete = $conn->insert_id;
                
                if ($id_paquete <= 0) {
                    throw new Exception("No se pudo obtener el ID del paquete creado");
                }
                
                // Insertar productos del paquete
                foreach ($productos as $id_producto) {
                    $id_producto = intval($id_producto);
                    
                    // Obtener la cantidad desde $_POST usando el formato correcto
                    $cantidad = 1; // valor por defecto
                    
                    // Buscar la cantidad en diferentes formatos posibles
                    if (isset($_POST["cantidades"][$id_producto])) {
                        $cantidad = intval($_POST["cantidades"][$id_producto]);
                    } elseif (isset($_POST["cantidad_" . $id_producto])) {
                        $cantidad = intval($_POST["cantidad_" . $id_producto]);
                    }
                    
                    if ($cantidad <= 0) {
                        $cantidad = 1; // Asegurar que sea al menos 1
                    }
                    
                    $stmt = $conn->prepare("INSERT INTO paquete_producto (id_paquete, id_producto, cantidad_producto) VALUES (?, ?, ?)");
                    $stmt->bind_param("iii", $id_paquete, $id_producto, $cantidad);
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Error al insertar producto en paquete: " . $stmt->error);
                    }
                }
                
                $conn->commit();
                echo json_encode([
                    'success' => true, 
                    'mensaje' => 'Paquete creado exitosamente',
                    'id_paquete' => $id_paquete
                ]);
                
            } catch (Exception $e) {
                $conn->rollback();
                error_log("Error en creación de paquete: " . $e->getMessage());
                throw $e;
            }
            break;
            
        case 'editar':
            $id_paquete = intval($_POST['id_paquete'] ?? 0);
            $tipo_evento = trim($_POST['tipo_evento'] ?? '');
            $id_proveedor = intval($_POST['id_proveedor'] ?? 0);
            $productos = $_POST['productos'] ?? [];
            $cantidades = $_POST['cantidades'] ?? [];
            
            // Validaciones
            if ($id_paquete <= 0) {
                echo json_encode(['success' => false, 'mensaje' => 'ID de paquete inválido']);
                exit();
            }
            
            if (empty($tipo_evento)) {
                echo json_encode(['success' => false, 'mensaje' => 'El tipo de evento es requerido']);
                exit();
            }
            
            if ($id_proveedor <= 0) {
                echo json_encode(['success' => false, 'mensaje' => 'Debe seleccionar un proveedor válido']);
                exit();
            }
            
            if (empty($productos)) {
                echo json_encode(['success' => false, 'mensaje' => 'Debe seleccionar al menos un producto']);
                exit();
            }
            
            // Verificar que el paquete existe
            $stmt = $conn->prepare("SELECT id_paquete FROM paquete WHERE id_paquete = ?");
            $stmt->bind_param("i", $id_paquete);
            $stmt->execute();
            if (!$stmt->get_result()->fetch_assoc()) {
                echo json_encode(['success' => false, 'mensaje' => 'El paquete no existe']);
                exit();
            }
            
            // Iniciar transacción
            $conn->begin_transaction();
            
            try {
                // Actualizar paquete
                $stmt = $conn->prepare("UPDATE paquete SET tipo_evento = ?, id_proveedor = ? WHERE id_paquete = ?");
                $stmt->bind_param("sii", $tipo_evento, $id_proveedor, $id_paquete);
                $stmt->execute();
                
                // Eliminar productos anteriores
                $stmt = $conn->prepare("DELETE FROM paquete_producto WHERE id_paquete = ?");
                $stmt->bind_param("i", $id_paquete);
                $stmt->execute();
                
                // Insertar nuevos productos
                foreach ($productos as $id_producto) {
                    $cantidad = intval($cantidades[$id_producto] ?? 1);
                    if ($cantidad <= 0) {
                        throw new Exception("La cantidad para el producto ID $id_producto debe ser mayor a 0");
                    }
                    
                    $stmt = $conn->prepare("INSERT INTO paquete_producto (id_paquete, id_producto, cantidad_producto) VALUES (?, ?, ?)");
                    $stmt->bind_param("iii", $id_paquete, intval($id_producto), $cantidad);
                    $stmt->execute();
                }
                
                $conn->commit();
                echo json_encode(['success' => true, 'mensaje' => 'Paquete actualizado exitosamente']);
                
            } catch (Exception $e) {
                $conn->rollback();
                throw $e;
            }
            break;
            
        case 'obtener':
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'mensaje' => 'ID inválido']);
                exit();
            }
            
            // Obtener datos del paquete
            $stmt = $conn->prepare("SELECT * FROM paquete WHERE id_paquete = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($paquete = $result->fetch_assoc()) {
                // Obtener productos del paquete
                $stmt = $conn->prepare("
                    SELECT pp.id_producto, pp.cantidad_producto 
                    FROM paquete_producto pp 
                    WHERE pp.id_paquete = ?
                ");
                $stmt->bind_param("i", $id);
                $stmt->execute();
                $productos_result = $stmt->get_result();
                $productos = $productos_result->fetch_all(MYSQLI_ASSOC);
                
                $paquete['productos'] = $productos;
                echo json_encode(['success' => true, 'data' => $paquete]);
            } else {
                echo json_encode(['success' => false, 'mensaje' => 'Paquete no encontrado']);
            }
            break;
            
        case 'cambiar_estado':
            $id = intval($_POST['id'] ?? 0);
            $estado = $_POST['estado'] ?? '';
            
            if ($id <= 0) {
                echo json_encode(['success' => false, 'mensaje' => 'ID inválido']);
                exit();
            }
            
            $nuevo_estado = $estado === 'activar' ? 'activo' : 'inactivo';
            
            $stmt = $conn->prepare("UPDATE paquete SET estado = ? WHERE id_paquete = ?");
            $stmt->bind_param("si", $nuevo_estado, $id);
            $success = $stmt->execute();
            $mensaje = $success ? "Paquete {$nuevo_estado} exitosamente" : 'Error al cambiar estado';
            
            echo json_encode(['success' => $success, 'mensaje' => $mensaje]);
            break;
            
        case 'cargar_productos':
            $id_proveedor = intval($_POST['id_proveedor'] ?? 0);
            if ($id_proveedor <= 0) {
                echo json_encode(['success' => false, 'mensaje' => 'ID de proveedor inválido']);
                exit();
            }
            
            $stmt = $conn->prepare("
                SELECT id_producto, nombre, descripcion, precio_unitario, cantidad_disponible, categoria
                FROM producto 
                WHERE id_proveedor = ? AND estado = 'activo' AND cantidad_disponible > 0
                ORDER BY categoria, nombre
            ");
            $stmt->bind_param("i", $id_proveedor);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $productos = $result->fetch_all(MYSQLI_ASSOC);
                echo json_encode(['success' => true, 'productos' => $productos]);
            } else {
                echo json_encode(['success' => true, 'productos' => [], 'mensaje' => 'Este proveedor no tiene productos disponibles']);
            }
            break;
            
        case 'cargar_tabla':
            $result = $conn->query("
                SELECT p.*, pr.nombre as proveedor_nombre,
                       COUNT(pp.id_producto) as total_productos,
                       COALESCE(SUM(pp.cantidad_producto), 0) as total_cantidad
                FROM paquete p 
                LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor 
                LEFT JOIN paquete_producto pp ON p.id_paquete = pp.id_paquete
                GROUP BY p.id_paquete, p.id_proveedor, p.tipo_evento, p.fecha_creacion, p.estado, pr.nombre
                ORDER BY p.fecha_creacion DESC
            ");
            
            if ($result && $result->num_rows > 0) {
                while ($p = $result->fetch_assoc()) {
                    $badge = $p['estado'] === 'activo' ? 'success' : 'danger';
                    $toggle_action = $p['estado'] === 'activo' ? 'desactivar' : 'activar';
                    $toggle_icon = $p['estado'] === 'activo' ? 'ban' : 'check';
                    $toggle_class = $p['estado'] === 'activo' ? 'btn-danger' : 'btn-success';
                    
                    $proveedor_info = $p['proveedor_nombre'] ? 
                        "<span class='text-muted'>#" . $p['id_proveedor'] . "</span><br>" . htmlspecialchars($p['proveedor_nombre']) : 
                        "<span class='text-muted'>Sin proveedor</span>";
                    
                    // Obtener lista de productos del paquete
                    $productos_stmt = $conn->prepare("
                        SELECT pr.nombre, pp.cantidad_producto 
                        FROM paquete_producto pp
                        JOIN producto pr ON pp.id_producto = pr.id_producto
                        WHERE pp.id_paquete = ?
                        ORDER BY pr.nombre
                    ");
                    $productos_stmt->bind_param("i", $p['id_paquete']);
                    $productos_stmt->execute();
                    $productos_result = $productos_stmt->get_result();
                    
                    $productos_lista = [];
                    while ($producto = $productos_result->fetch_assoc()) {
                        $productos_lista[] = $producto['nombre'] . ' (' . $producto['cantidad_producto'] . ')';
                    }
                    
                    $productos_texto = !empty($productos_lista) ? 
                        implode(', ', array_slice($productos_lista, 0, 3)) . (count($productos_lista) > 3 ? '...' : '') :
                        'Sin productos';
                    
                    echo "<tr>
                        <td>{$p['id_paquete']}</td>
                        <td>" . htmlspecialchars($p['tipo_evento']) . "</td>
                        <td>{$proveedor_info}</td>
                        <td>
                            <span class='badge bg-info'>{$p['total_productos']} productos</span>
                            <br><small class='text-muted' title='" . htmlspecialchars(implode(', ', $productos_lista)) . "'>{$productos_texto}</small>";
                            
                    if ($p['total_cantidad'] > 0) {
                        echo "<br><small class='text-success'>Total: {$p['total_cantidad']} unidades</small>";
                    }
                    
                    echo "</td>
                        <td>" . date('d/m/Y', strtotime($p['fecha_creacion'])) . "</td>
                        <td><span class='badge bg-{$badge}'>" . ucfirst($p['estado']) . "</span></td>
                        <td>
                            <div class='btn-group btn-group-sm'>
                                <button class='btn btn-info btn-ver' data-id='{$p['id_paquete']}' title='Ver detalles'>
                                    <i class='fas fa-eye fa-fw'></i>
                                </button>
                                <button class='btn btn-edit btn-editar' data-id='{$p['id_paquete']}' title='Editar'>
                                    <i class='fas fa-edit fa-fw'></i>
                                </button>
                                <button class='btn {$toggle_class} btn-toggle' data-id='{$p['id_paquete']}' data-estado='{$toggle_action}' title='" . ucfirst($toggle_action) . "'>
                                    <i class='fas fa-{$toggle_icon} fa-fw'></i>
                                </button>
                            </div>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center py-4'><i class='fas fa-inbox fa-2x text-muted mb-3'></i><br>No hay paquetes registrados</td></tr>";
            }
            break;
            
        case 'test':
            echo json_encode(['success' => true, 'mensaje' => 'Conexión exitosa', 'datos' => ['accion' => $accion, 'session' => isset($_SESSION['usuario']) ? 'OK' : 'NO', 'conn' => isset($conn) ? 'OK' : 'NO']]);
            break;
            
        default:
            echo json_encode(['success' => false, 'mensaje' => 'Acción no válida: ' . $accion]);
    }
    
} catch (Exception $e) {
    if (isset($conn) && $conn->more_results()) {
        while ($conn->next_result()) {
            if ($result = $conn->store_result()) {
                $result->free();
            }
        }
    }
    echo json_encode(['success' => false, 'mensaje' => 'Error interno: ' . $e->getMessage()]);
}

if (isset($conn)) {
    $conn->close();
}
?>