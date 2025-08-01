<?php
// control.php

// Include database configuration
require_once '../config/database.php';

// Start session
session_start();

// Function to handle user login
function loginUser($username, $password) {
    $db = new Database();
    $conn = $db->getConnection();

    $query = "SELECT * FROM users WHERE username = :username LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            return true;
        }
    }
    return false;
}

// Function to handle user registration
function registerUser($username, $password) {
    $db = new Database();
    $conn = $db->getConnection();

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $query = "INSERT INTO users (username, password) VALUES (:username, :password)";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':password', $hashedPassword);

    return $stmt->execute();
}

// Function to log out user
function logoutUser() {
    session_start();
    session_destroy();
    header("Location: ../index.php");
    exit();
}

// Additional control functions can be added here
?>