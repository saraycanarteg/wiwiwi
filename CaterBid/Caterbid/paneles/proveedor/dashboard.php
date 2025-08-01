<?php
// dashboard.php for the provider panel

// Include necessary files
include_once '../../config/database.php';
include_once '../../includes/header.php';
include_once '../../includes/navbar.php';

// Database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch provider-specific data (e.g., products, orders)
$query = "SELECT * FROM products WHERE provider_id = :provider_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':provider_id', $_SESSION['provider_id']);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h1>Dashboard Proveedor</h1>
    <div class="row">
        <div class="col-md-12">
            <h2>Mis Productos</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($product['id']); ?></td>
                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                            <td><?php echo htmlspecialchars($product['price']); ?></td>
                            <td>
                                <a href="productos.php?id=<?php echo htmlspecialchars($product['id']); ?>" class="btn btn-primary">Editar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
include_once '../../includes/footer.php';
?>