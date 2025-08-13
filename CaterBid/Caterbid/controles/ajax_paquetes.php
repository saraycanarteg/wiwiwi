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
function manejarPaginacionTabla($conn, $tabla, $campoOrden, $camposSelect = '*', $condicionWhere = '1=1', $joins = '') {
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $filas = isset($_GET['filas']) ? (int)$_GET['filas'] : 10;
    
    // Validaciones
    if ($pagina < 1) $pagina = 1;
    if ($filas < 1) $filas = 10;
    if ($filas > 100) $filas = 100; // Límite máximo
    
    $offset = ($pagina - 1) * $filas;
    
    try {
        // Obtener total de registros
        $countQuery = "SELECT COUNT(*) as total FROM $tabla $joins WHERE $condicionWhere";
        $totalQuery = $conn->query($countQuery);
        if (!$totalQuery) {
            throw new Exception("Error en consulta de conteo: " . $conn->error);
        }
        
        $totalRegistros = $totalQuery->fetch_assoc()['total'];
        $totalPaginas = ceil($totalRegistros / $filas);
        
        // Obtener registros de la página actual
        $query = "SELECT $camposSelect FROM $tabla $joins WHERE $condicionWhere ORDER BY $campoOrden LIMIT $filas OFFSET $offset";
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
            $joins = 'LEFT JOIN proveedor pr ON paquete.id_proveedor = pr.id_proveedor 
                     LEFT JOIN paquete_producto pp ON paquete.id_paquete = pp.id_paquete';
            
            $campos = 'paquete.*, pr.nombre as proveedor_nombre,
                      COUNT(pp.id_producto) as total_productos,
                      COALESCE(SUM(pp.cantidad_producto), 0) as total_cantidad';
            
            // Necesitamos usar GROUP BY para la agregación
            $paginacion = manejarPaginacionTablaConGroup($conn);
            
            if (isset($paginacion['error'])) {
                echo "<tr><td colspan='7' class='text-center text-danger'><i class='fas fa-exclamation-triangle'></i> " . $paginacion['mensaje'] . "</td></tr>";
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
                        <td>{$p['id_paquete']}</td>
                        <td>" . htmlspecialchars($p['tipo_evento']) . "</td>
                        <td>";
                    
                    if ($p['proveedor_nombre']) {
                        echo "<span class='text-muted'>#" . $p['id_proveedor'] . "</span><br>";
                        echo htmlspecialchars($p['proveedor_nombre']);
                    } else {
                        echo "<span class='text-muted'>Sin proveedor</span>";
                    }
                    
                    echo "</td>
                        <td>
                            <span class='badge bg-info'>" . $p['total_productos'] . " productos</span>";
                    
                    if ($p['total_cantidad'] > 0) {
                        echo "<br><small class='text-muted'>" . $p['total_cantidad'] . " unidades total</small>";
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
            
            // Generar paginación y enviarla al JavaScript
            echo '<script>
                $("#paginacion").html(`' . addslashes(generarPaginacion($paginacion['totalPaginas'], $paginacion['paginaActual'])) . '`);
            </script>';
            break;
            
        case 'cargar_productos':
            $id_proveedor = intval($_POST['id_proveedor'] ?? 0);
            
            if ($id_proveedor <= 0) {
                echo json_encode(['success' => false, 'mensaje' => 'ID de proveedor inválido']);
                exit();
            }
            
            $stmt = $conn->prepare("SELECT id_producto, nombre, descripcion, precio_unitario, cantidad_disponible, categoria 
                                   FROM producto 
                                   WHERE id_proveedor = ? AND estado = 'activo' AND cantidad_disponible > 0 
                                   ORDER BY categoria, nombre");
            $stmt->bind_param("i", $id_proveedor);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $productos = [];
            while ($row = $result->fetch_assoc()) {
                $productos[] = $row;
            }
            
            if (empty($productos)) {
                echo json_encode([
                    'success' => true, 
                    'productos' => [], 
                    'mensaje' => 'Este proveedor no tiene productos activos disponibles'
                ]);
            } else {
                echo json_encode(['success' => true, 'productos' => $productos]);
            }
            break;
            
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

        case 'exportar_pdf':
            // Obtener todos los registros para exportar
            $query = "SELECT p.*, pr.nombre as proveedor_nombre,
                             COUNT(DISTINCT pp.id_producto) as total_productos,
                             COALESCE(SUM(pp.cantidad_producto), 0) as total_cantidad
                      FROM paquete p 
                      LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor 
                      LEFT JOIN paquete_producto pp ON p.id_paquete = pp.id_paquete
                      GROUP BY p.id_paquete, p.id_proveedor, p.tipo_evento, p.fecha_creacion, p.estado, pr.nombre
                      ORDER BY p.fecha_creacion DESC";
            $result = $conn->query($query);
            
            $datos = [];
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $datos[] = $row;
                }
            }
            
            echo json_encode(['success' => true, 'data' => $datos]);
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

// Función específica para paquetes con GROUP BY
function manejarPaginacionTablaConGroup($conn) {
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $filas = isset($_GET['filas']) ? (int)$_GET['filas'] : 10;
    
    if ($pagina < 1) $pagina = 1;
    if ($filas < 1) $filas = 10;
    if ($filas > 100) $filas = 100;
    
    $offset = ($pagina - 1) * $filas;
    
    try {
        // Obtener total de registros
        $countQuery = "SELECT COUNT(DISTINCT p.id_paquete) as total 
                      FROM paquete p 
                      LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor 
                      LEFT JOIN paquete_producto pp ON p.id_paquete = pp.id_paquete";
        
        $totalQuery = $conn->query($countQuery);
        if (!$totalQuery) {
            throw new Exception("Error en consulta de conteo: " . $conn->error);
        }
        
        $totalRegistros = $totalQuery->fetch_assoc()['total'];
        $totalPaginas = ceil($totalRegistros / $filas);
        
        // Obtener registros de la página actual con GROUP BY
        $query = "SELECT p.*, pr.nombre as proveedor_nombre,
                         COUNT(pp.id_producto) as total_productos,
                         COALESCE(SUM(pp.cantidad_producto), 0) as total_cantidad
                  FROM paquete p 
                  LEFT JOIN proveedor pr ON p.id_proveedor = pr.id_proveedor 
                  LEFT JOIN paquete_producto pp ON p.id_paquete = pp.id_paquete
                  GROUP BY p.id_paquete, p.id_proveedor, p.tipo_evento, p.fecha_creacion, p.estado, pr.nombre
                  ORDER BY p.fecha_creacion DESC 
                  LIMIT $filas OFFSET $offset";
        
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

if (isset($conn)) {
    $conn->close();
}
?>