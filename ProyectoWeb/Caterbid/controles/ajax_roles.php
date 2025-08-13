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
        // Para consultas con GROUP BY, necesitamos contar de manera diferente
        if (strpos($tabla, 'GROUP BY') !== false) {
            // Consulta especial para roles con permisos agrupados
            $countQuery = "SELECT COUNT(DISTINCT r.id_rol) as total FROM rol r 
                          LEFT JOIN rol_permiso rp ON r.id_rol = rp.id_rol 
                          LEFT JOIN permiso p ON rp.id_permiso = p.id_permiso 
                          WHERE $condicionWhere";
        } else {
            $countQuery = "SELECT COUNT(*) as total FROM $tabla WHERE $condicionWhere";
        }
        
        $totalQuery = $conn->query($countQuery);
        if (!$totalQuery) {
            throw new Exception("Error en consulta de conteo");
        }
        
        $totalRegistros = $totalQuery->fetch_assoc()['total'];
        $totalPaginas = ceil($totalRegistros / $filas);
        
        // Obtener registros de la página actual
        if (strpos($tabla, 'GROUP BY') !== false) {
            // Query completa ya incluye SELECT, FROM, GROUP BY, etc.
            $query = $tabla . " LIMIT $filas OFFSET $offset";
        } else {
            $query = "SELECT $camposSelect FROM $tabla WHERE $condicionWhere ORDER BY $campoOrden LIMIT $filas OFFSET $offset";
        }
        
        $result = $conn->query($query);
        
        if (!$result) {
            throw new Exception("Error en consulta de datos: " . $conn->error);
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

            // Bloquear cambios en roles base
            if ($id <= 3) {
                echo json_encode(['success' => false, 'mensaje' => 'No se puede cambiar el estado de este rol predeterminado']);
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
            $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
            $filas = isset($_GET['filas']) ? (int)$_GET['filas'] : 10;
            
            // Validaciones
            if ($pagina < 1) $pagina = 1;
            if ($filas < 1) $filas = 10;
            if ($filas > 100) $filas = 100;
            
            $offset = ($pagina - 1) * $filas;
            
            // Obtener total de registros
            $totalQuery = $conn->query("SELECT COUNT(DISTINCT r.id_rol) as total FROM rol r");
            $totalRegistros = $totalQuery->fetch_assoc()['total'];
            $totalPaginas = ceil($totalRegistros / $filas);
            
            // Consulta para roles con paginación
            $query = "SELECT r.id_rol, r.nombre_rol, r.descripcion, r.estado,
                             GROUP_CONCAT(p.nombre_permiso ORDER BY p.nombre_permiso SEPARATOR ', ') AS permisos_lista
                      FROM rol r
                      LEFT JOIN rol_permiso rp ON r.id_rol = rp.id_rol
                      LEFT JOIN permiso p ON rp.id_permiso = p.id_permiso
                      GROUP BY r.id_rol
                      ORDER BY r.id_rol ASC
                      LIMIT $filas OFFSET $offset";
            
            $result = $conn->query($query);

            if ($result && $result->num_rows > 0) {
                while ($r = $result->fetch_assoc()) {
                    $badge = $r['estado'] === 'activo' ? 'success' : 'danger';
                    $toggle_action = $r['estado'] === 'activo' ? 'desactivar' : 'activar';
                    $toggle_icon = $r['estado'] === 'activo' ? 'ban' : 'check';
                    $toggle_class = $r['estado'] === 'activo' ? 'btn-danger' : 'btn-success';

                    echo "<tr>
                            <td>{$r['id_rol']}</td>
                            <td>" . htmlspecialchars($r['nombre_rol']) . "</td>
                            <td>" . htmlspecialchars($r['descripcion']) . "</td>
                            <td>" . htmlspecialchars($r['permisos_lista'] ?: 'Sin permisos') . "</td>
                            <td><span class='badge bg-{$badge}'>" . ucfirst($r['estado']) . "</span></td>
                            <td>
                                <div class='btn-group btn-group-sm'>
                                    <button class='btn btn-edit btn-editar' data-id='{$r['id_rol']}' title='Editar'>
                                        <i class='fas fa-edit fa-fw'></i>
                                    </button>";

                    if ($r['id_rol'] > 3) {
                        echo "<button class='btn {$toggle_class} btn-toggle' data-id='{$r['id_rol']}' data-estado='{$toggle_action}' title='" . ucfirst($toggle_action) . "'>
                                <i class='fas fa-{$toggle_icon} fa-fw'></i>
                            </button>";
                    }

                    echo "      </div>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center py-4'>
                    <i class='fas fa-inbox fa-2x text-muted mb-3'></i><br>No hay roles registrados
                </td></tr>";
            }
            
            // Generar paginación
            echo '<script>
                $("#paginacion").html(`' . addslashes(generarPaginacion($totalPaginas, $pagina)) . '`);
            </script>';
            break;

        case 'exportar_pdf':
            // Obtener todos los registros para exportar con permisos
            $query = "SELECT r.*, GROUP_CONCAT(p.nombre_permiso SEPARATOR ', ') as permisos_lista
                     FROM rol r 
                     LEFT JOIN rol_permiso rp ON r.id_rol = rp.id_rol 
                     LEFT JOIN permiso p ON rp.id_permiso = p.id_permiso 
                     GROUP BY r.id_rol 
                     ORDER BY r.id_rol";
            $result = $conn->query($query);
            
            $datos = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Si no tiene permisos, mostrar 'Sin permisos'
                    if (empty($row['permisos_lista'])) {
                        $row['permisos_lista'] = 'Sin permisos';
                    }
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