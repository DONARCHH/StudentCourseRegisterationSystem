<?php
session_start();

// Include database connection
require_once "db.php";

// Make sure this is a POST request
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: login.php");
    exit();
}

// Get and sanitize user inputs
$name = isset($_POST["name"]) ? trim($_POST["name"]) : '';
$password = isset($_POST["password"]) ? trim($_POST["password"]) : '';

// Simple validation
if ($name === '' || $password === '') {
    $_SESSION['admin_error'] = "Please enter both name and password.";
    header("Location: login.php");
    exit();
}

// Prepare SQL query (plaintext password check)
$sql = "SELECT * FROM admins WHERE name = ? AND password = ? LIMIT 1";
$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    // Query preparation failed
    error_log("MySQL prepare failed: " . $mysqli->error);
    $_SESSION['admin_error'] = "An error occurred. Please try again later.";
    header("Location: login.php");
    exit();
}

// Bind parameters and execute
$stmt->bind_param("ss", $name, $password);
$stmt->execute();
$result = $stmt->get_result();

// Check if admin exists
if ($result && $result->num_rows === 1) {
    $admin = $result->fetch_assoc();

    // Set session variables
    $_SESSION['admin_id'] = $admin['admin_id'];
    $_SESSION['admin_name'] = $admin['name'];

    // Redirect to dashboard
    header("Location: dashboard.php");
    exit();
} else {
    $_SESSION['admin_error'] = "Invalid name or password!";
    header("Location: login.php");
    exit();
}
