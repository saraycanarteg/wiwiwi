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
            $nombre_rol = trim($_POST['nombre_rol'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $permisos = $_POST['permisos'] ?? [];

            if (empty($nombre_rol)) {
                echo json_encode(['success' => false, 'mensaje' => 'El nombre del rol es obligatorio']);
                exit();
            }

            if ($accion === 'crear') {
                // Insertar nuevo rol con estado activo
                $stmt = $conn->prepare("INSERT INTO rol (nombre_rol, descripcion, estado) VALUES (?, ?, 'activo')");
                $stmt->bind_param("ss", $nombre_rol, $descripcion);
                $success = $stmt->execute();
                $id_rol = $stmt->insert_id;
            } else {
                // Actualizar rol existente
                $id_rol = intval($_POST['id_rol'] ?? 0);
                if ($id_rol <= 0) {
                    echo json_encode(['success' => false, 'mensaje' => 'ID de rol inválido']);
                    exit();
                }
                $stmt = $conn->prepare("UPDATE rol SET nombre_rol = ?, descripcion = ? WHERE id_rol = ?");
                $stmt->bind_param("ssi", $nombre_rol, $descripcion, $id_rol);
                $success = $stmt->execute();

                // Eliminar permisos previos
                $conn->query("DELETE FROM rol_permiso WHERE id_rol = $id_rol");
            }

            // Insertar nuevos permisos seleccionados
            if (!empty($permisos)) {
                $stmt_perm = $conn->prepare("INSERT INTO rol_permiso (id_rol, id_permiso) VALUES (?, ?)");
                foreach ($permisos as $p) {
                    $p_id = intval($p);
                    $stmt_perm->bind_param("ii", $id_rol, $p_id);
                    $stmt_perm->execute();
                }
            }

            $mensaje = $success ? 'Rol guardado exitosamente' : 'Error al guardar rol';
            echo json_encode(['success' => $success, 'mensaje' => $mensaje]);
            break;


        case 'obtener':
            $id = intval($_GET['id'] ?? 0);
            if ($id <= 0) {
                echo json_encode(['success' => false, 'mensaje' => 'ID inválido']);
                exit();
            }

            $stmt = $conn->prepare("SELECT * FROM rol WHERE id_rol = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($rol = $result->fetch_assoc()) {
                // Obtener permisos
                $permisos = [];
                $res_perm = $conn->query("SELECT id_permiso FROM rol_permiso WHERE id_rol = $id");
                while ($p = $res_perm->fetch_assoc()) {
                    $permisos[] = intval($p['id_permiso']);
                }
                $rol['permisos'] = $permisos;
                echo json_encode(['success' => true, 'data' => $rol]);
            } else {
                echo json_encode(['success' => false, 'mensaje' => 'Rol no encontrado']);
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

            $stmt = $conn->prepare("UPDATE rol SET estado = ? WHERE id_rol = ?");
            $stmt->bind_param("si", $nuevo_estado, $id);
            $success = $stmt->execute();
            $mensaje = $success ? "Rol {$nuevo_estado} exitosamente" : 'Error al cambiar estado';

            echo json_encode(['success' => $success, 'mensaje' => $mensaje]);
            break;

        case 'cargar_tabla':
            $sql = "SELECT r.id_rol, r.nombre_rol, r.descripcion, r.estado,
                    GROUP_CONCAT(p.nombre_permiso ORDER BY p.nombre_permiso SEPARATOR '||') AS permisos
                    FROM rol r
                    LEFT JOIN rol_permiso rp ON r.id_rol = rp.id_rol
                    LEFT JOIN permiso p ON rp.id_permiso = p.id_permiso
                    GROUP BY r.id_rol
                    ORDER BY r.id_rol ASC";

            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0) {
                while ($r = $result->fetch_assoc()) {
                    // Badge y datos dinámicos según estado
                    $badge = $r['estado'] === 'activo' ? 'success' : 'danger';
                    $toggle_action = $r['estado'] === 'activo' ? 'desactivar' : 'activar';
                    $toggle_icon = $r['estado'] === 'activo' ? 'ban' : 'check';
                    $toggle_class = $r['estado'] === 'activo' ? 'btn-danger' : 'btn-success';

                    echo "<tr>
                        <td>{$r['id_rol']}</td>
                        <td>" . htmlspecialchars($r['nombre_rol']) . "</td>
                        <td>" . htmlspecialchars($r['descripcion']) . "</td>
                        <td><span class='badge bg-{$badge}'>" . ucfirst($r['estado']) . "</span></td>
                        <td>" . htmlspecialchars(str_replace('||', ', ', $r['permisos'] ?? '')) . "</td>
                        <td>
                        <div class='btn-group btn-group-sm'>
                            <button class='btn btn-edit btn-editar' data-id='{$r['id_rol']}' title='Editar'>
                                <i class='fas fa-edit fa-fw'></i>
                            </button>
                            <button class='btn {$toggle_class} btn-toggle' data-id='{$r['id_rol']}' data-estado='{$toggle_action}' title='" . ucfirst($toggle_action) . "'>
                                <i class='fas fa-{$toggle_icon} fa-fw'></i>
                            </button>
                        </div>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center py-4'>
                    <i class='fas fa-inbox fa-2x text-muted mb-3'></i><br>No hay roles registrados
                </td></tr>";
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