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
            $paginacion = manejarPaginacionTabla(
                $conn, 
                'proveedor', 
                'fecha_creacion DESC'
            );
            
            if (isset($paginacion['error'])) {
                echo "<tr><td colspan='8' class='text-center text-danger'><i class='fas fa-exclamation-triangle'></i> " . $paginacion['mensaje'] . "</td></tr>";
                break;
            }
            
            $result = $paginacion['result'];
            
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
                echo "<tr><td colspan='8' class='text-center py-4'><i class='fas fa-inbox fa-2x text-muted mb-3'></i><br>No hay proveedores registrados</td></tr>";
            }
            
            // Generar paginación y enviarla al JavaScript
            echo '<script>
                $("#paginacion").html(`' . addslashes(generarPaginacion($paginacion['totalPaginas'], $paginacion['paginaActual'])) . '`);
            </script>';
            break;
            
        case 'test':
            echo json_encode(['success' => true, 'mensaje' => 'Conexión exitosa', 'datos' => ['accion' => $accion, 'session' => isset($_SESSION['usuario']) ? 'OK' : 'NO', 'conn' => isset($conn) ? 'OK' : 'NO']]);
            break;
            
        case 'exportar_pdf':
            // Obtener todos los registros para exportar
            $query = "SELECT * FROM proveedor ORDER BY fecha_creacion DESC";
            $result = $conn->query($query);
            
            $datos = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $datos[] = $row;
                }
            }
            
            echo json_encode(['success' => true, 'data' => $datos]);
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