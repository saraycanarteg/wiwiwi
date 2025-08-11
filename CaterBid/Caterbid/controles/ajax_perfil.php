<?php
session_start();
require_once '../config/database.php';

// Verificar que el usuario esté autenticado
if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'mensaje' => 'Usuario no autenticado']);
    exit();
}

// Verificar conexión a la base de datos
if (!isset($conn) || $conn->connect_error) {
    echo json_encode(['success' => false, 'mensaje' => 'Error de conexión a la base de datos']);
    exit();
}

$accion = $_POST['accion'] ?? '';

if ($accion === 'cambiar_password') {
    $usuario_id = intval($_SESSION['usuario_id']);
    $password_actual = $_POST['password_actual'] ?? '';
    $password_nueva = $_POST['password_nueva'] ?? '';

    try {
        // 1. Obtener contraseña actual desde la BD
        $stmt = $conn->prepare("SELECT `contraseña` FROM usuario WHERE id_usuario = ?");
        $stmt->bind_param("i", $usuario_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if (!$result || $result->num_rows === 0) {
            echo json_encode(['success' => false, 'mensaje' => 'Usuario no encontrado']);
            exit();
        }

        $row = $result->fetch_assoc();
        $hash_actual = $row['contraseña'];

        // 2. Verificar contraseña actual
        if (!password_verify($password_actual, $hash_actual)) {
            echo json_encode(['success' => false, 'mensaje' => 'La contraseña actual es incorrecta']);
            exit();
        }

        // 3. Hashear nueva contraseña
        $nuevo_hash = password_hash($password_nueva, PASSWORD_DEFAULT);

        // 4. Actualizar en la BD
        $stmt = $conn->prepare("UPDATE usuario SET `contraseña` = ? WHERE id_usuario = ?");
        $stmt->bind_param("si", $nuevo_hash, $usuario_id);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'mensaje' => 'Contraseña actualizada correctamente']);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'Error al actualizar la contraseña']);
        }

    } catch (Exception $e) {
        echo json_encode(['success' => false, 'mensaje' => 'Error: ' . $e->getMessage()]);
    }
}
?>
