<?php
session_start();

// Verificar sesión
if (!isset($_SESSION['usuario'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autenticado']);
    exit();
}

require_once '../config/database.php';

// Función genérica para manejar paginación
function manejarPaginacionTabla($conn, $tabla, $campoOrden, $camposSelect = '*', $condicionWhere = '1=1') {
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $filas = isset($_GET['filas']) ? (int)$_GET['filas'] : 10;
    
    // Validaciones
    if ($pagina < 1) $pagina = 1;
    if ($filas < 1) $filas = 10;
    if ($filas > 100) $filas = 100; // Límite máximo
    
    $offset = ($pagina - 1) * $filas;
    
    try {
        // Obtener total de registros
        $totalQuery = $conn->query("SELECT COUNT(*) as total FROM $tabla WHERE $condicionWhere");
        if (!$totalQuery) {
            throw new Exception("Error en consulta de conteo");
        }
        
        $totalRegistros = $totalQuery->fetch_assoc()['total'];
        $totalPaginas = ceil($totalRegistros / $filas);
        
        // Obtener registros de la página actual
        $query = "SELECT $camposSelect FROM $tabla WHERE $condicionWhere ORDER BY $campoOrden LIMIT $filas OFFSET $offset";
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception("Error en consulta de datos");
        }
        
        return [
            'result' => $result,
            'totalRegistros' => $totalRegistros,
            'totalPaginas' => $totalPaginas,
            'paginaActual' => $pagina,
            'filasPorPagina' => $filas
        ];
        
    } catch (Exception $e) {
        return [
            'error' => true,
            'mensaje' => $e->getMessage()
        ];
    }
}

// Función para generar HTML de paginación
function generarPaginacion($totalPaginas, $paginaActual, $mostrarVecinos = 2) {
    if ($totalPaginas <= 1) return '';
    
    $html = '<nav aria-label="Paginación de tabla">';
    $html .= '<ul class="pagination pagination-sm justify-content-center mb-0">';
    
    // Botón Anterior
    if ($paginaActual > 1) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link pagina-link" href="#" data-pagina="' . ($paginaActual - 1) . '" aria-label="Anterior">';
        $html .= '<span aria-hidden="true">&laquo;</span>';
        $html .= '</a></li>';
    } else {
        $html .= '<li class="page-item disabled">';
        $html .= '<span class="page-link">&laquo;</span>';
        $html .= '</li>';
    }
    
    // Números de página
    $inicio = max(1, $paginaActual - $mostrarVecinos);
    $fin = min($totalPaginas, $paginaActual + $mostrarVecinos);
    
    // Primera página si no está en el rango
    if ($inicio > 1) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link pagina-link" href="#" data-pagina="1">1</a>';
        $html .= '</li>';
        if ($inicio > 2) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Páginas del rango
    for ($i = $inicio; $i <= $fin; $i++) {
        $active = ($i == $paginaActual) ? 'active' : '';
        $html .= '<li class="page-item ' . $active . '">';
        if ($i == $paginaActual) {
            $html .= '<span class="page-link">' . $i . '</span>';
        } else {
            $html .= '<a class="page-link pagina-link" href="#" data-pagina="' . $i . '">' . $i . '</a>';
        }
        $html .= '</li>';
    }
    
    // Última página si no está en el rango
    if ($fin < $totalPaginas) {
        if ($fin < $totalPaginas - 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link pagina-link" href="#" data-pagina="' . $totalPaginas . '">' . $totalPaginas . '</a>';
        $html .= '</li>';
    }
    
    // Botón Siguiente
    if ($paginaActual < $totalPaginas) {
        $html .= '<li class="page-item">';
        $html .= '<a class="page-link pagina-link" href="#" data-pagina="' . ($paginaActual + 1) . '" aria-label="Siguiente">';
        $html .= '<span aria-hidden="true">&raquo;</span>';
        $html .= '</a></li>';
    } else {
        $html .= '<li class="page-item disabled">';
        $html .= '<span class="page-link">&raquo;</span>';
        $html .= '</li>';
    }
    
    $html .= '</ul></nav>';
    return $html;
}

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
            
            // Prevenir que el usuario se desactive a sí mismo
            if ($id == $_SESSION['usuario']['id_usuario']) {
                echo json_encode(['success' => false, 'mensaje' => 'No puedes cambiar tu propio estado']);
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
            $paginacion = manejarPaginacionTabla(
                $conn, 
                'usuario u LEFT JOIN rol r ON u.id_rol = r.id_rol', 
                'u.fecha_creacion DESC',
                'u.*, r.nombre_rol'
            );
            
            if (isset($paginacion['error'])) {
                echo "<tr><td colspan='8' class='text-center text-danger'><i class='fas fa-exclamation-triangle'></i> " . $paginacion['mensaje'] . "</td></tr>";
                break;
            }
            
            $result = $paginacion['result'];
            
            if ($result && $result->num_rows > 0) {
                while ($u = $result->fetch_assoc()) {
                    $badge = $u['estado'] === 'activo' ? 'success' : 'danger';
                    $toggle_action = $u['estado'] === 'activo' ? 'desactivar' : 'activar';
                    $toggle_icon = $u['estado'] === 'activo' ? 'ban' : 'check';
                    $toggle_class = $u['estado'] === 'activo' ? 'btn-danger' : 'btn-success';
                    $fecha_formateada = date('d/m/Y H:i', strtotime($u['fecha_creacion']));
                    
                    echo "<tr>
                        <td>{$u['id_usuario']}</td>
                        <td><strong>" . htmlspecialchars($u['nombre']) . "</strong></td>
                        <td>" . htmlspecialchars($u['correo']) . "</td>
                        <td>
                            <span class='truncate' title='" . htmlspecialchars($u['direccion'] ?: 'Sin dirección') . "'>
                                " . htmlspecialchars($u['direccion'] ?: 'Sin dirección') . "
                            </span>
                        </td>
                        <td>
                            <span class='badge bg-info'>
                                " . htmlspecialchars($u['nombre_rol'] ?: 'Sin rol') . "
                            </span>
                        </td>
                        <td><span class='badge bg-{$badge}'>" . ucfirst($u['estado']) . "</span></td>
                        <td>{$fecha_formateada}</td>
                        <td>
                            <div class='btn-group btn-group-sm'>
                                <button class='btn btn-edit btn-editar' data-id='{$u['id_usuario']}' title='Editar'>
                                    <i class='fas fa-edit fa-fw'></i>
                                </button>";
                    
                    // Solo mostrar botón de cambio de estado si no es el usuario actual
                    if ($u['id_usuario'] != $_SESSION['usuario']['id_usuario']) {
                        echo "<button class='btn {$toggle_class} btn-toggle' data-id='{$u['id_usuario']}' data-estado='{$toggle_action}' title='" . ucfirst($toggle_action) . "'>
                                <i class='fas fa-{$toggle_icon} fa-fw'></i>
                            </button>";
                    }
                    
                    echo "      </div>
                        </td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='8' class='text-center py-4'><i class='fas fa-inbox fa-2x text-muted mb-3'></i><br>No hay usuarios registrados</td></tr>";
            }
            
            // Generar paginación
            echo '<script>
                $("#paginacion").html(`' . addslashes(generarPaginacion($paginacion['totalPaginas'], $paginacion['paginaActual'])) . '`);
            </script>';
            break;
            
        case 'exportar_pdf':
            // Obtener todos los registros para exportar con información del rol
            $query = "SELECT u.id_usuario, u.nombre, u.correo, u.direccion, u.estado, u.fecha_creacion, r.nombre_rol 
                     FROM usuario u 
                     LEFT JOIN rol r ON u.id_rol = r.id_rol 
                     ORDER BY u.fecha_creacion DESC";
            $result = $conn->query($query);
            
            $datos = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Agregar nombre del rol para exportación
                    $row['rol_nombre'] = $row['nombre_rol'] ?: 'Sin rol';
                    $datos[] = $row;
                }
            }
            
            echo json_encode(['success' => true, 'data' => $datos]);
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