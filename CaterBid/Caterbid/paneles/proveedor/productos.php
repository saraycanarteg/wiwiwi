<?php
// productos.php

// Include database configuration
include_once '../../config/database.php';

// Create a connection to the database
$database = new Database();
$db = $database->getConnection();

// Check if the user is logged in as a provider
session_start();
if (!isset($_SESSION['provider_id'])) {
    header("Location: ../auth/login.php");
    exit();
}

// Fetch products from the database
$query = "SELECT * FROM products WHERE provider_id = :provider_id";
$stmt = $db->prepare($query);
$stmt->bindParam(':provider_id', $_SESSION['provider_id']);
$stmt->execute();

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Include header and navbar
include_once '../../includes/header.php';
include_once '../../includes/navbar.php';
?>

<div class="container">
    <h1>Manage Your Products</h1>
    <a href="add_product.php" class="btn btn-primary">Add New Product</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Description</th>
                <th>Price</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($products as $product): ?>
                <tr>
                    <td><?php echo htmlspecialchars($product['id']); ?></td>
                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                    <td><?php echo htmlspecialchars($product['description']); ?></td>
                    <td><?php echo htmlspecialchars($product['price']); ?></td>
                    <td>
                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-warning">Edit</a>
                        <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php
// Include footer
include_once '../../includes/footer.php';
?>