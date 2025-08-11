<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autenticado']);
    exit();
}

// Verificar permisos específicos para productos
$permisos_usuario = isset($_SESSION['usuario']['permisos']) ? $_SESSION['usuario']['permisos'] : [];
$tiene_permiso = in_array('registrar_producto', $permisos_usuario) || 
                 in_array('crud_productos', $permisos_usuario) ||
                 (isset($_SESSION['usuario']['rol_nombre']) && $_SESSION['usuario']['rol_nombre'] == 'bodeguero');

if (!$tiene_permiso) {
    echo json_encode(['success' => false, 'mensaje' => 'No tiene permisos para gestionar productos']);
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
            $nombre = trim($_POST['nombre'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio_unitario = floatval($_POST['precio_unitario'] ?? 0);
            $cantidad_disponible = intval($_POST['cantidad_disponible'] ?? 0);
            $categoria = trim($_POST['categoria'] ?? '');
            $id_proveedor = intval($_POST['id_proveedor'] ?? 0);
            
            // Validaciones
            if (empty($nombre) || empty($descripcion) || empty($categoria)) {
                echo json_encode(['success' => false, 'mensaje' => 'Los campos nombre, descripción y categoría son requeridos']);
                exit();
            }
            
            if ($precio_unitario <= 0) {
                echo json_encode(['success' => false, 'mensaje' => 'El precio unitario debe ser mayor a 0']);
                exit();
            }
            
            if ($cantidad_disponible < 0) {
                echo json_encode(['success' => false, 'mensaje' => 'La cantidad disponible no puede ser negativa']);
                exit();
            }
            
            if ($id_proveedor <= 0) {
                echo json_encode(['success' => false, 'mensaje' => 'Debe seleccionar un proveedor válido']);
                exit();
            }
            
            // Validar categorías permitidas
            $categorias_validas = ['Comida', 'Bebidas', 'Menaje y utensilios', 'Equipos y mobiliario', 'Personal y servicios', 'Decoración y ambientación'];
            if (!in_array($categoria, $categorias_validas)) {
                echo json_encode(['success' => false, 'mensaje' => 'Categoría no válida']);
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
            
            if ($accion === 'crear') {
                $stmt = $conn->prepare("INSERT INTO producto (nombre, descripcion, precio_unitario, cantidad_disponible, categoria, id_proveedor, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                $stmt->bind_param("ssdisi", $nombre, $descripcion, $precio_unitario, $cantidad_disponible, $categoria, $id_proveedor);
                $success = $stmt->execute();
                $mensaje = $success ? 'Producto creado exitosamente' : 'Error al crear producto';

            } else {
                $id = intval($_POST['id_producto'] ?? 0);
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'mensaje' => 'ID de producto inválido']);
                    exit();
                }
                
                $stmt = $conn->prepare("UPDATE producto SET nombre = ?, descripcion = ?, precio_unitario = ?, cantidad_disponible = ?, categoria = ?, id_proveedor = ? WHERE id_producto = ?");
                $stmt->bind_param("ssdisii", $nombre, $descripcion, $precio_unitario, $cantidad_disponible, $categoria, $id_proveedor, $id);
                $success = $stmt->execute();
                $mensaje = $success ? 'Producto actualizado exitosamente' : 'Error al actualizar producto';
            }
            
            echo json_encode(['success' => $success, 'mensaje' => $mensaje]);
            break;
            
        case 'obtener':
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'mensaje' => 'ID inválido']);
                exit();
            }
            
            $stmt = $conn->prepare("SELECT * FROM producto WHERE id_producto = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                echo json_encode(['success' => true, 'data' => $row]);
            } else {
                echo json_encode(['success' => false, 'mensaje' => 'Producto no encontrado']);
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
            
            $stmt = $conn->prepare("UPDATE producto SET estado = ? WHERE id_producto = ?");
            $stmt->bind_param("si", $nuevo_estado, $id);
            $success = $stmt->execute();
            $mensaje = $success ? "Producto {$nuevo_estado} exitosamente" : 'Error al cambiar estado';
            
            echo json_encode(['success' => $success, 'mensaje' => $mensaje]);
            break;
            
        case 'cargar_tabla':
            $result = $conn->query("
                SELECT p.*, pr.nombre as proveedor_nombre 
                FROM producto p 
                LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor 
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
                    
                    echo "<tr>
                        <td>{$p['id_producto']}</td>
                        <td>" . htmlspecialchars($p['nombre']) . "</td>
                        <td>" . htmlspecialchars($p['descripcion']) . "</td>
                        <td>$" . number_format($p['precio_unitario'], 2) . "</td>
                        <td>{$p['cantidad_disponible']}</td>
                        <td>" . htmlspecialchars($p['categoria']) . "</td>
                        <td>{$proveedor_info}</td>
                        <td><span class='badge bg-{$badge}'>" . ucfirst($p['estado']) . "</span></td>
                        <td>
                            <div class='btn-group btn-group-sm'>
                                <button class='btn btn-edit btn-editar' data-id='{$p['id_producto']}' title='Editar'>
                                    <i class='fas fa-edit fa-fw'></i>
                                </button>
                                <button class='btn {$toggle_class} btn-toggle' data-id='{$p['id_producto']}' data-estado='{$toggle_action}' title='" . ucfirst($toggle_action) . "'>
                                    <i class='fas fa-{$toggle_icon} fa-fw'></i>
                                </button>
                            </div>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='9' class='text-center py-4'><i class='fas fa-inbox fa-2x text-muted mb-3'></i><br>No hay productos registrados</td></tr>";
            }
            break;
            
        case 'test':
            echo json_encode(['success' => true, 'mensaje' => 'Conexión exitosa', 'datos' => ['accion' => $accion, 'session' => isset($_SESSION['usuario']) ? 'OK' : 'NO', 'conn' => isset($conn) ? 'OK' : 'NO']]);
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