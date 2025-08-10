<?php
$servername = "localhost";
$username = "admin";
$password = "admin";
$base = "sistema_cotizaciones";

// Conexión
$conn = new mysqli($servername, $username, $password, $base);

// Verificación
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
if (isset($_SESSION['usuario_id'])) {
    $usuario_id = intval($_SESSION['usuario_id']);
    mysqli_query($conn, "SET @usuario_id = {$usuario_id}");
}
?>
