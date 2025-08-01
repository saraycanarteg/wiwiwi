<?php
// historial.php - Historial de transacciones del cliente

// Incluir archivos necesarios
include_once '../../config/database.php';
include_once '../../includes/header.php';
include_once '../../includes/navbar.php';

// Conectar a la base de datos
$db = new Database();
$conn = $db->getConnection();

// Consultar historial de transacciones del cliente
$userId = $_SESSION['user_id']; // Suponiendo que el ID del usuario está almacenado en la sesión
$query = "SELECT * FROM transacciones WHERE user_id = :user_id ORDER BY fecha DESC";
$stmt = $conn->prepare($query);
$stmt->bindParam(':user_id', $userId);
$stmt->execute();
$transacciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h2>Historial de Transacciones</h2>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Descripción</th>
                <th>Monto</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($transacciones) > 0): ?>
                <?php foreach ($transacciones as $transaccion): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($transaccion['id']); ?></td>
                        <td><?php echo htmlspecialchars($transaccion['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($transaccion['monto']); ?></td>
                        <td><?php echo htmlspecialchars($transaccion['fecha']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No hay transacciones disponibles.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
include_once '../../includes/footer.php';
?>