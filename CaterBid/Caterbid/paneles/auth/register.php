<?php
// register.php

// Include database configuration
require_once '../../config/database.php';

// Initialize variables
$username = "";
$email = "";
$password = "";
$confirm_password = "";
$registration_error = "";

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $confirm_password = trim($_POST["confirm_password"]);

    // Validate input
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $registration_error = "Please fill in all fields.";
    } elseif ($password !== $confirm_password) {
        $registration_error = "Passwords do not match.";
    } else {
        // Insert user into database
        $sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt->bind_param("sss", $username, $email, $hashed_password);
            if ($stmt->execute()) {
                header("location: login.php");
                exit;
            } else {
                $registration_error = "Something went wrong. Please try again.";
            }
            $stmt->close();
        }
    }
}

// Include header and navbar
include '../../includes/header.php';
include '../../includes/navbar.php';
?>

<div class="container">
    <h2>Register</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <div class="form-group">
            <label>Username</label>
            <input type="text" name="username" class="form-control" value="<?php echo $username; ?>">
        </div>
        <div class="form-group">
            <label>Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo $email; ?>">
        </div>
        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="form-group">
            <label>Confirm Password</label>
            <input type="password" name="confirm_password" class="form-control">
        </div>
        <div class="form-group">
            <span class="text-danger"><?php echo $registration_error; ?></span>
        </div>
        <button type="submit" class="btn btn-primary">Register</button>
    </form>
</div>

<?php
// Include footer
include '../../includes/footer.php';
?>