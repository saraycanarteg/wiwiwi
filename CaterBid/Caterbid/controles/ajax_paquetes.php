<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autenticado']);
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
        case 'editar':
            $tipo_evento = trim($_POST['tipo_evento'] ?? '');
            $id_proveedor = intval($_POST['id_proveedor'] ?? 0);
            $productos = $_POST['productos'] ?? [];
            $cantidades = $_POST['cantidades'] ?? [];

            // Validaciones básicas
            if (empty($tipo_evento)) {
                echo json_encode(['success' => false, 'mensaje' => 'El tipo de evento es obligatorio']);
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

            // Iniciar transacción
            $conn->begin_transaction();

            try {
                if ($accion === 'crear') {
                    // Insertar nuevo paquete con estado activo
                    $stmt = $conn->prepare("INSERT INTO paquete (tipo_evento, id_proveedor, fecha_creacion, estado) VALUES (?, ?, NOW(), 'activo')");
                    $stmt->bind_param("si", $tipo_evento, $id_proveedor);
                    $success = $stmt->execute();
                    $id_paquete = $stmt->insert_id;
                } else {
                    // Actualizar paquete existente
                    $id_paquete = intval($_POST['id_paquete'] ?? 0);
                    if ($id_paquete <= 0) {
                        echo json_encode(['success' => false, 'mensaje' => 'ID de paquete inválido']);
                        exit();
                    }
                    $stmt = $conn->prepare("UPDATE paquete SET tipo_evento = ?, id_proveedor = ? WHERE id_paquete = ?");
                    $stmt->bind_param("sii", $tipo_evento, $id_proveedor, $id_paquete);
                    $success = $stmt->execute();

                    // Eliminar productos previos
                    $conn->query("DELETE FROM paquete_producto WHERE id_paquete = $id_paquete");
                }

                // Insertar nuevos productos seleccionados
                if (!empty($productos)) {
                    $stmt_prod = $conn->prepare("INSERT INTO paquete_producto (id_paquete, id_producto, cantidad_producto) VALUES (?, ?, ?)");
                    foreach ($productos as $id_producto) {
                        $id_producto = intval($id_producto);
                        $cantidad = intval($cantidades[$id_producto] ?? 1);
                        
                        // Asegurar que la cantidad sea al menos 1
                        if ($cantidad <= 0) {
                            $cantidad = 1;
                        }
                        
                        $stmt_prod->bind_param("iii", $id_paquete, $id_producto, $cantidad);
                        $stmt_prod->execute();
                    }
                }

                $conn->commit();
                $mensaje = $success ? 'Paquete guardado exitosamente' : 'Error al guardar paquete';
                echo json_encode(['success' => $success, 'mensaje' => $mensaje]);

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

            $stmt = $conn->prepare("SELECT * FROM paquete WHERE id_paquete = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($paquete = $result->fetch_assoc()) {
                // Obtener productos del paquete
                $productos = [];
                $res_prod = $conn->query("SELECT id_producto, cantidad_producto FROM paquete_producto WHERE id_paquete = $id");
                while ($p = $res_prod->fetch_assoc()) {
                    $productos[] = [
                        'id_producto' => intval($p['id_producto']),
                        'cantidad' => intval($p['cantidad_producto'])
                    ];
                }
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

        case 'cargar_tabla':
            $sql = "SELECT p.id_paquete, p.tipo_evento, p.fecha_creacion, p.estado,
                    pr.nombre as proveedor_nombre, pr.id_proveedor,
                    COUNT(pp.id_producto) as total_productos,
                    COALESCE(SUM(pp.cantidad_producto), 0) as total_cantidad,
                    GROUP_CONCAT(CONCAT(prod.nombre, ' (', pp.cantidad_producto, ')') ORDER BY prod.nombre SEPARATOR '||') AS productos_detalle
                    FROM paquete p
                    LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor
                    LEFT JOIN paquete_producto pp ON p.id_paquete = pp.id_paquete
                    LEFT JOIN producto prod ON pp.id_producto = prod.id_producto
                    GROUP BY p.id_paquete
                    ORDER BY p.fecha_creacion DESC";

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($p = $result->fetch_assoc()) {
                    // Badge y datos dinámicos según estado
                    $badge = $p['estado'] === 'activo' ? 'success' : 'danger';
                    $toggle_action = $p['estado'] === 'activo' ? 'desactivar' : 'activar';
                    $toggle_icon = $p['estado'] === 'activo' ? 'ban' : 'check';
                    $toggle_class = $p['estado'] === 'activo' ? 'btn-danger' : 'btn-success';

                    // Información del proveedor
                    $proveedor_info = $p['proveedor_nombre'] ? 
                        "<span class='text-muted'>#" . $p['id_proveedor'] . "</span><br>" . htmlspecialchars($p['proveedor_nombre']) : 
                        "<span class='text-muted'>Sin proveedor</span>";

                    // Información de productos
                    $productos_texto = '';
                    if ($p['total_productos'] > 0) {
                        $productos_lista = explode('||', $p['productos_detalle'] ?? '');
                        $productos_mostrados = array_slice($productos_lista, 0, 3);
                        $productos_texto = htmlspecialchars(implode(', ', $productos_mostrados));
                        if (count($productos_lista) > 3) {
                            $productos_texto .= '...';
                        }
                    } else {
                        $productos_texto = 'Sin productos';
                    }

                    echo "<tr>
                        <td>{$p['id_paquete']}</td>
                        <td>" . htmlspecialchars($p['tipo_evento']) . "</td>
                        <td>{$proveedor_info}</td>
                        <td>
                            <span class='badge bg-info'>{$p['total_productos']} productos</span>
                            <br><small class='text-muted' title='" . htmlspecialchars(str_replace('||', ', ', $p['productos_detalle'] ?? '')) . "'>{$productos_texto}</small>";
                            
                    if ($p['total_cantidad'] > 0) {
                        echo "<br><small class='text-success'>Total: {$p['total_cantidad']} unidades</small>";
                    }
                    
                    echo "</td>
                        <td>" . date('d/m/Y H:i', strtotime($p['fecha_creacion'])) . "</td>
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
                echo "<tr><td colspan='7' class='text-center py-4'>
                    <i class='fas fa-inbox fa-2x text-muted mb-3'></i><br>No hay paquetes registrados
                </td></tr>";
            }
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

        default:
            echo json_encode(['success' => false, 'mensaje' => 'Acción no válida: ' . $accion]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'mensaje' => 'Error interno: ' . $e->getMessage()]);
}

if (isset($conn)) {
    $conn->close();
}
?>