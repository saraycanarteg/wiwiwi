<?php
// dashboard.php for the admin panel

// Include necessary files
include_once '../../config/database.php';
include_once '../../includes/header.php';
include_once '../../includes/navbar.php';

// Check user permissions (admin)
session_start();
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: ../../index.php');
    exit();
}

// Fetch data for the dashboard (example: user statistics, recent activities)
function fetchDashboardData() {
    // Database connection
    $db = new Database();
    $conn = $db->getConnection();

    // Example query to fetch user statistics #FALTA PONER VALIDACIÓN CONTRA INYECCIÓN SQL
    $query = "SELECT COUNT(*) as total_users FROM users";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

$dashboardData = fetchDashboardData();
?>

<div class="container">
    <h1>Admin Dashboard</h1>
    <div class="dashboard-stats">
        <h2>User Statistics</h2>
        <p>Total Users: <?php echo $dashboardData['total_users']; ?></p>
    </div>
    <!-- Additional dashboard features can be added here -->
</div>

<?php
include_once '../../includes/footer.php';
?>