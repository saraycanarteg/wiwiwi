<?php
// dashboard.php - Client Dashboard

// Include necessary files
include_once '../../config/database.php';
include_once '../../includes/header.php';
include_once '../../includes/navbar.php';

// Database connection
$db = new Database();
$conn = $db->getConnection();

// Fetch client data (example query)
$query = "SELECT * FROM clients WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $_SESSION['client_id']);
$stmt->execute();
$client = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if client data is retrieved
if ($client) {
    echo "<h1>Welcome, " . htmlspecialchars($client['name']) . "</h1>";
    echo "<p>Your email: " . htmlspecialchars($client['email']) . "</p>";
    // Additional client dashboard information can be displayed here
} else {
    echo "<p>No client data found.</p>";
}

// Include footer if necessary
include_once '../../includes/footer.php';
?>