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
            $nombre = trim($_POST['nombre'] ?? '');
            $ruc = trim($_POST['ruc'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $telefono = trim($_POST['telefono'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            
            if (empty($nombre) || empty($ruc) || empty($correo) || empty($telefono) || empty($direccion)) {
                echo json_encode(['success' => false, 'mensaje' => 'Todos los campos son requeridos']);
                exit();
            }
            
            if (!preg_match('/^\d{10,13}$/', $ruc)) {
                echo json_encode(['success' => false, 'mensaje' => 'RUC debe tener entre 10 y 13 dígitos']);
                exit();
            }
            
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'mensaje' => 'Email inválido']);
                exit();
            }
            
            if ($accion === 'crear') {
                $stmt = $conn->prepare("INSERT INTO proveedor (nombre, ruc, correo, telefono, direccion) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $nombre, $ruc, $correo, $telefono, $direccion);
                $success = $stmt->execute();
                $mensaje = $success ? 'Proveedor creado exitosamente' : 'Error al crear proveedor';

            } else {
                $id = intval($_POST['id_proveedor'] ?? 0);
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'mensaje' => 'ID de proveedor inválido']);
                    exit();
                }
                
                $stmt = $conn->prepare("UPDATE proveedor SET nombre = ?, ruc = ?, correo = ?, telefono = ?, direccion = ? WHERE id_proveedor = ?");
                $stmt->bind_param("sssssi", $nombre, $ruc, $correo, $telefono, $direccion, $id);
                $success = $stmt->execute();
                $mensaje = $success ? 'Proveedor actualizado exitosamente' : 'Error al actualizar proveedor';
            }
            
            echo json_encode(['success' => $success, 'mensaje' => $mensaje]);
            break;
            
        case 'obtener':
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'mensaje' => 'ID inválido']);
                exit();
            }
            
            $stmt = $conn->prepare("SELECT * FROM proveedor WHERE id_proveedor = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                echo json_encode(['success' => true, 'data' => $row]);
            } else {
                echo json_encode(['success' => false, 'mensaje' => 'Proveedor no encontrado']);
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
            
            $stmt = $conn->prepare("UPDATE proveedor SET estado = ? WHERE id_proveedor = ?");
            $stmt->bind_param("si", $nuevo_estado, $id);
            $success = $stmt->execute();
            $mensaje = $success ? "Proveedor {$nuevo_estado} exitosamente" : 'Error al cambiar estado';
            
            echo json_encode(['success' => $success, 'mensaje' => $mensaje]);
            break;
            
        case 'cargar_tabla':
            $result = $conn->query("SELECT * FROM proveedor ORDER BY fecha_creacion DESC");
            
            if ($result && $result->num_rows > 0) {
                while ($p = $result->fetch_assoc()) {
                    $badge = $p['estado'] === 'activo' ? 'success' : 'danger';
                    $toggle_action = $p['estado'] === 'activo' ? 'desactivar' : 'activar';
                    $toggle_icon = $p['estado'] === 'activo' ? 'ban' : 'check';
                    $toggle_class = $p['estado'] === 'activo' ? 'btn-danger' : 'btn-success';
                    
                    echo "<tr>
                        <td>{$p['id_proveedor']}</td>
                        <td>" . htmlspecialchars($p['nombre']) . "</td>
                        <td>" . htmlspecialchars($p['ruc']) . "</td>
                        <td>" . htmlspecialchars($p['correo']) . "</td>
                        <td>" . htmlspecialchars($p['telefono']) . "</td>
                        <td><span class='badge bg-{$badge}'>" . ucfirst($p['estado']) . "</span></td>
                        <td>" . htmlspecialchars($p['direccion']) . "</td>
                        <td>
                        <div class='btn-group btn-group-sm'>
                            <button class='btn btn-edit btn-editar' data-id='{$p['id_proveedor']}' title='Editar'>
                                <i class='fas fa-edit fa-fw'></i>
                            </button>
                            <button class='btn {$toggle_class} btn-toggle' data-id='{$p['id_proveedor']}' data-estado='{$toggle_action}' title='" . ucfirst($toggle_action) . "'>
                                <i class='fas fa-{$toggle_icon} fa-fw'></i>
                            </button>
                        </div>
                    </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8' class='text-center py-4'><i class='fas fa-inbox fa-2x text-muted mb-3'></i><br>No hay proveedores</td></tr>";
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