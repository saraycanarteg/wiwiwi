<?php
require 'database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $direccion = $_POST['direccion'];
    $contraseña = $_POST['contraseña'];
    $confirmar = $_POST['confirmar_contraseña'];

    // Validación servidor: contraseña y confirmación
    if ($contraseña !== $confirmar) {
        echo "Las contraseñas no coinciden.";
        exit;
    }

    if (strlen($contraseña) < 6) {
        echo "La contraseña debe tener al menos 6 caracteres.";
        exit;
    }

    // Hashear contraseña
    $hash = password_hash($contraseña, PASSWORD_DEFAULT);

    // Obtener id del rol cliente #FALTA PONER VALIDACIÓN CONTRA INYECCIÓN SQL
    $sqlRol = "SELECT id_rol FROM rol WHERE nombre_rol = 'cliente' LIMIT 1";
    $resRol = $conn->query($sqlRol);
    if (!$resRol || $resRol->num_rows == 0) {
        echo "Error: rol cliente no encontrado.";
        exit;
    }
    $rol = $resRol->fetch_assoc()['id_rol'];

    // Insertar nuevo usuario #FALTA PONER VALIDACIÓN CONTRA INYECCIÓN SQL
    $stmt = $conn->prepare("INSERT INTO usuario (nombre, correo, contraseña, direccion, id_rol) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nombre, $correo, $hash, $direccion, $rol);

    if ($stmt->execute()) {
        echo "Registro exitoso. Ahora puedes iniciar sesión.";
    } else {
        if (str_contains($stmt->error, 'Duplicate entry')) {
            echo " Este correo ya está registrado.";
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    $stmt->close();
    $conn->close();
}
?>

