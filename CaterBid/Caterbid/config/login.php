<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];
    // #FALTA PONER VALIDACIÓN CONTRA INYECCIÓN SQL
    $stmt = $conn->prepare("SELECT id_usuario, nombre, contraseña, id_rol FROM usuario WHERE correo = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $usuario = $res->fetch_assoc();
        if (password_verify($clave, $usuario['contraseña'])) {
            $_SESSION['usuario_id'] = $usuario['id_usuario'];
            $_SESSION['rol_id'] = $usuario['id_rol'];
            $_SESSION['nombre'] = $usuario['nombre'];

            // Redirigir según rol
            switch ($usuario['id_rol']) {
                case 1: header("Location: dashboard_admin.php"); break;
                case 2: header("Location: dashboard_cliente.php"); break;
                case 3: header("Location: dashboard_proveedor.php"); break;
                default: echo "Rol desconocido"; exit;
            }
            exit;
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "Usuario no encontrado";
    }

    $stmt->close();
    $conn->close();
}
?>
