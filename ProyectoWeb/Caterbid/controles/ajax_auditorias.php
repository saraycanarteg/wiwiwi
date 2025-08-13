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
            throw new Exception("Error en consulta de conteo: " . $conn->error);
        }
        
        $totalRegistros = $totalQuery->fetch_assoc()['total'];
        $totalPaginas = ceil($totalRegistros / $filas);
        
        // Obtener registros de la página actual
        $query = "SELECT $camposSelect FROM $tabla WHERE $condicionWhere ORDER BY $campoOrden LIMIT $filas OFFSET $offset";
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
        case 'cargar_tabla':
            $paginacion = manejarPaginacionTabla(
                $conn, 
                'auditoria', 
                'fecha_cambio DESC'
            );
            
            if (isset($paginacion['error'])) {
                echo "<tr><td colspan='6' class='text-center text-danger'><i class='fas fa-exclamation-triangle'></i> " . $paginacion['mensaje'] . "</td></tr>";
                break;
            }
            
            $result = $paginacion['result'];
            
            if ($result && $result->num_rows > 0) {
                while ($log = $result->fetch_assoc()) {
                    // Truncar valores largos para la vista de tabla
                    $valor_anterior = strlen($log['valor_anterior']) > 50 ? substr($log['valor_anterior'], 0, 50) . '...' : $log['valor_anterior'];
                    $valor_nuevo = strlen($log['valor_nuevo']) > 50 ? substr($log['valor_nuevo'], 0, 50) . '...' : $log['valor_nuevo'];
                    
                    echo "<tr>
                        <td>{$log['id_auditoria']}</td>
                        <td>" . htmlspecialchars($log['id_usuario']) . "</td>
                        <td>" . htmlspecialchars($log['tabla_afectada']) . "</td>
                        <td class='truncate' title='" . htmlspecialchars($log['valor_anterior']) . "'>" . htmlspecialchars($valor_anterior) . "</td>
                        <td class='truncate' title='" . htmlspecialchars($log['valor_nuevo']) . "'>" . htmlspecialchars($valor_nuevo) . "</td>
                        <td>" . date('d/m/Y H:i:s', strtotime($log['fecha_cambio'])) . "</td>
                    </tr>";
                }
            } else {
                echo "<tr><td colspan='6' class='text-center py-4'><i class='fas fa-inbox fa-2x text-muted mb-3'></i><br>No hay registros en auditoría</td></tr>";
            }
            
            // Generar paginación y enviarla al JavaScript
            echo '<script>
                $("#paginacion").html(`' . addslashes(generarPaginacion($paginacion['totalPaginas'], $paginacion['paginaActual'])) . '`);
            </script>';
            break;
            
        case 'exportar_pdf':
            // IMPORTANTE: Para exportar PDF, solo devolver JSON sin HTML adicional
            header('Content-Type: application/json');
            
            // Obtener todos los registros para exportar
            $query = "SELECT id_auditoria, id_usuario, tabla_afectada, valor_anterior, valor_nuevo, fecha_cambio
                      FROM auditoria ORDER BY fecha_cambio DESC";
            $result = $conn->query($query);
            
            $datos = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Crear campos truncados para el PDF
                    $row['valor_anterior_corto'] = strlen($row['valor_anterior']) > 50 ? 
                        substr($row['valor_anterior'], 0, 50) . '...' : $row['valor_anterior'];
                    $row['valor_nuevo_corto'] = strlen($row['valor_nuevo']) > 50 ? 
                        substr($row['valor_nuevo'], 0, 50) . '...' : $row['valor_nuevo'];
                    
                    $datos[] = $row;
                }
            }
            
            echo json_encode(['success' => true, 'data' => $datos]);
            exit(); // IMPORTANTE: Salir inmediatamente después de enviar JSON
            
        case 'test':
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'mensaje' => 'Conexión exitosa', 'datos' => ['accion' => $accion, 'session' => isset($_SESSION['usuario']) ? 'OK' : 'NO', 'conn' => isset($conn) ? 'OK' : 'NO']]);
            exit();
            
        default:
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'mensaje' => 'Acción no válida: ' . $accion]);
            exit();
    }
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'mensaje' => 'Error interno: ' . $e->getMessage()]);
    exit();
}

if (isset($conn)) {
    $conn->close();
}
?>
