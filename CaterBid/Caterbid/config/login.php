<?php 
session_start(); 
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];
    
    // Verificar conexión
    if (!$conn) {
        die("Error de conexión a la base de datos: " . mysqli_connect_error());
    }
    
    // Consulta completa con información del rol
    $stmt = $conn->prepare("
        SELECT u.id_usuario, u.nombre, u.contraseña, u.id_rol, r.nombre_rol 
        FROM usuario u 
        JOIN rol r ON u.id_rol = r.id_rol 
        WHERE u.correo = ?
    ");
    
    if (!$stmt) {
        die("Error preparando consulta: " . $conn->error);
    }
    
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $res = $stmt->get_result();
    
    if ($res->num_rows === 1) {
        $usuario = $res->fetch_assoc();
        
        if (password_verify($clave, $usuario['contraseña'])) {
            // Obtener todos los permisos del rol
            $stmt_permisos = $conn->prepare("
                SELECT p.nombre_permiso 
                FROM rol_permiso rp 
                JOIN permiso p ON rp.id_permiso = p.id_permiso 
                WHERE rp.id_rol = ?
            ");
            
            if (!$stmt_permisos) {
                die("Error preparando consulta de permisos: " . $conn->error);
            }
            $stmt_permisos->bind_param("i", $usuario['id_rol']);
            $stmt_permisos->execute();
            $res_permisos = $stmt_permisos->get_result();
            
            // Crear array de permisos
            $permisos = [];
            while ($permiso = $res_permisos->fetch_assoc()) {
                $permisos[] = $permiso['nombre_permiso'];
            }
            
            // Guardar toda la información en la sesión
            $_SESSION['usuario'] = [
                'id' => $usuario['id_usuario'],
                'nombre' => $usuario['nombre'],
                'correo' => $correo,
                'rol_id' => $usuario['id_rol'],
                'rol_nombre' => $usuario['nombre_rol'],
                'permisos' => $permisos
            ];
            
            // Registrar login en auditoría (opcional - comentar si no tienes tabla auditoria)
            /*
            $stmt_auditoria = $conn->prepare("
                INSERT INTO auditoria (id_usuario, accion, tabla_afectada, fecha_accion) 
                VALUES (?, 'LOGIN', 'usuario', NOW())
            ");
            if ($stmt_auditoria) {
                $stmt_auditoria->bind_param("i", $usuario['id_usuario']);
                $stmt_auditoria->execute();
                $stmt_auditoria->close();
            }
            */
            
            // Redirigir a dashboard único (no por rol)
            header("Location: ../includes/dashboard.php");
            exit;
            
            $stmt_permisos->close();
        } else {
            $_SESSION['error'] = "Contraseña incorrecta";
            header("Location: ../index.html");
        }
    } else {
        $_SESSION['error'] = "Usuario no encontrado";
        header("Location: ../index.html");
    }
    
    $stmt->close();
    $conn->close();
}
?>