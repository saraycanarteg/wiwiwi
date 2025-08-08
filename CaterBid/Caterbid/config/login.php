<?php
session_start();
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $clave = $_POST['clave'];
    
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

            switch ($usuario['id_rol']) {
                case 1: header("Location: ../dashboardAdmin.html"); break;
                case 2: header("Location: ../dashboardCotizador.html"); break;
                case 3: header("Location: ../dashboardBodeguero.html"); break;
                default: 
                    $_SESSION['error'] = "Rol desconocido";
                    header("Location: ../index.html");
                    break;
            }
            exit;
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