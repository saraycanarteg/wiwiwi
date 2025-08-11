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
            $correo = trim($_POST['correo'] ?? '');
            $direccion = trim($_POST['direccion'] ?? '');
            $id_rol = intval($_POST['id_rol'] ?? 0);
            $contraseña = $_POST['contraseña'] ?? '';
            $confirmar_contraseña = $_POST['confirmar_contraseña'] ?? '';
            
            // Validaciones básicas
            if (empty($nombre) || empty($correo) || empty($id_rol)) {
                echo json_encode(['success' => false, 'mensaje' => 'Nombre, correo y rol son obligatorios']);
                exit();
            }
            
            if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                echo json_encode(['success' => false, 'mensaje' => 'Correo inválido']);
                exit();
            }
            
            if ($accion === 'crear') {
                // Para creación, la contraseña es obligatoria
                if (empty($contraseña)) {
                    echo json_encode(['success' => false, 'mensaje' => 'La contraseña es obligatoria']);
                    exit();
                }
                
                if (strlen($contraseña) < 6) {
                    echo json_encode(['success' => false, 'mensaje' => 'La contraseña debe tener al menos 6 caracteres']);
                    exit();
                }
                
                if ($contraseña !== $confirmar_contraseña) {
                    echo json_encode(['success' => false, 'mensaje' => 'Las contraseñas no coinciden']);
                    exit();
                }
                
                $password_hash = password_hash($contraseña, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("INSERT INTO usuario (nombre, correo, direccion, contraseña, id_rol, estado, fecha_creacion) VALUES (?, ?, ?, ?, ?, 'activo', NOW())");
                $stmt->bind_param("ssssi", $nombre, $correo, $direccion, $password_hash, $id_rol);
                $success = $stmt->execute();
                $mensaje = $success ? 'Usuario creado exitosamente' : 'Error al crear usuario';
                
            } else {
                // Para edición
                $id = intval($_POST['id_usuario'] ?? 0);
                if ($id <= 0) {
                    echo json_encode(['success' => false, 'mensaje' => 'ID de usuario inválido']);
                    exit();
                }
                
                // Si se proporciona contraseña, validarla y actualizarla
                if (!empty($contraseña)) {
                    if (strlen($contraseña) < 6) {
                        echo json_encode(['success' => false, 'mensaje' => 'La contraseña debe tener al menos 6 caracteres']);
                        exit();
                    }
                    
                    if ($contraseña !== $confirmar_contraseña) {
                        echo json_encode(['success' => false, 'mensaje' => 'Las contraseñas no coinciden']);
                        exit();
                    }
                    
                    $password_hash = password_hash($contraseña, PASSWORD_DEFAULT);
                    $stmt = $conn->prepare("UPDATE usuario SET nombre = ?, correo = ?, direccion = ?, contraseña = ?, id_rol = ? WHERE id_usuario = ?");
                    $stmt->bind_param("ssssii", $nombre, $correo, $direccion, $password_hash, $id_rol, $id);
                } else {
                    // Actualizar sin cambiar contraseña
                    $stmt = $conn->prepare("UPDATE usuario SET nombre = ?, correo = ?, direccion = ?, id_rol = ? WHERE id_usuario = ?");
                    $stmt->bind_param("sssii", $nombre, $correo, $direccion, $id_rol, $id);
                }
                
                $success = $stmt->execute();
                $mensaje = $success ? 'Usuario actualizado exitosamente' : 'Error al actualizar usuario';
            }
            
            echo json_encode(['success' => $success, 'mensaje' => $mensaje]);
            break;
            
        case 'obtener':
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'mensaje' => 'ID inválido']);
                exit();
            }
            
            $stmt = $conn->prepare("SELECT u.*, r.nombre_rol FROM usuario u LEFT JOIN rol r ON u.id_rol = r.id_rol WHERE u.id_usuario = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($row = $result->fetch_assoc()) {
                // No devolver la contraseña
                unset($row['contraseña']);
                echo json_encode(['success' => true, 'data' => $row]);
            } else {
                echo json_encode(['success' => false, 'mensaje' => 'Usuario no encontrado']);
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
            
            $stmt = $conn->prepare("UPDATE usuario SET estado = ? WHERE id_usuario = ?");
            $stmt->bind_param("si", $nuevo_estado, $id);
            $success = $stmt->execute();
            $mensaje = $success ? "Usuario {$nuevo_estado} exitosamente" : 'Error al cambiar estado';
            
            echo json_encode(['success' => $success, 'mensaje' => $mensaje]);
            break;
            
        case 'cargar_tabla':
            $sql = "
                SELECT u.*, r.nombre_rol 
                FROM usuario u 
                LEFT JOIN rol r ON u.id_rol = r.id_rol 
                ORDER BY u.fecha_creacion DESC
            ";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while ($u = $result->fetch_assoc()) {
                    $badge = $u['estado'] === 'activo' ? 'success' : 'danger';
                    $toggle_action = $u['estado'] === 'activo' ? 'desactivar' : 'activar';
                    $toggle_icon = $u['estado'] === 'activo' ? 'ban' : 'check';
                    $toggle_class = $u['estado'] === 'activo' ? 'btn-danger' : 'btn-success';
                    
                    echo "<tr>
                        <td>{$u['id_usuario']}</td>
                        <td>" . htmlspecialchars($u['nombre']) . "</td>
                        <td>" . htmlspecialchars($u['correo']) . "</td>
                        <td>" . htmlspecialchars($u['direccion'] ?? '') . "</td>
                        <td>" . htmlspecialchars($u['nombre_rol'] ?? 'Sin rol') . "</td>
                        <td><span class='badge bg-{$badge}'>" . ucfirst($u['estado']) . "</span></td>
                        <td>" . htmlspecialchars($u['fecha_creacion'] ?? 'Sin rol') . "</td>
                        <td>
                        <div class='btn-group btn-group-sm'>
                            <button class='btn btn-edit btn-editar' data-id='{$u['id_usuario']}' title='Editar'>
                                <i class='fas fa-edit fa-fw'></i>
                            </button>
                            <button class='btn {$toggle_class} btn-toggle' data-id='{$u['id_usuario']}' data-estado='{$toggle_action}' title='" . ucfirst($toggle_action) . "'>
                                <i class='fas fa-{$toggle_icon} fa-fw'></i>
                            </button>
                        </div>
                    </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='text-center py-4'><i class='fas fa-inbox fa-2x text-muted mb-3'></i><br>No hay usuarios</td></tr>";
            }
            break;
            
        case 'test':
            echo json_encode([
                'success' => true, 
                'mensaje' => 'Conexión exitosa',
                'datos' => [
                    'accion' => $accion,
                    'session' => isset($_SESSION['usuario']) ? 'OK' : 'NO',
                    'conn' => isset($conn) ? 'OK' : 'NO'
                ]
            ]);
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