<?php
// cotizacion.php

// Include necessary files
include_once '../../config/database.php';
include_once '../../includes/header.php';
include_once '../../includes/navbar.php';

// Database connection
$database = new Database();
$db = $database->getConnection();

// Function to fetch quotes
function fetchQuotes($db) {
    $query = "SELECT * FROM quotes WHERE client_id = :client_id";
    $stmt = $db->prepare($query);
    $stmt->bindParam(':client_id', $_SESSION['client_id']);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Check if the user is logged in
session_start();
if (!isset($_SESSION['client_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch quotes for the logged-in client
$quotes = fetchQuotes($db);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../recursos/css/style.css">
    <title>Cotización</title>
</head>
<body>
    <div class="container">
        <h1>Cotizaciones</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Descripción</th>
                    <th>Monto</th>
                    <th>Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quotes as $quote): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($quote['id']); ?></td>
                        <td><?php echo htmlspecialchars($quote['description']); ?></td>
                        <td><?php echo htmlspecialchars($quote['amount']); ?></td>
                        <td><?php echo htmlspecialchars($quote['date']); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="../../recursos/js/main.js"></script>
</body>
</html>