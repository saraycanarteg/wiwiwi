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
?>
