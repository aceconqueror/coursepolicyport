<?php
// Include your database configuration
include 'config.php';  // Assuming config.php has the database connection setup

// Admin credentials
$username = 'admin';
$email = 'admin@example.com';
$password = 'admin123';  // Set the password to 'admin123'
$full_name = 'Admin User';
$role = 'admin';  // The role of the user

// Hash the password securely
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

// SQL query to insert the first admin user into the database
try {
    // Prepare the insert query
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$username, $email, $hashed_password, $full_name, $role]);

    // Success message
    echo "Admin user created successfully! Use 'admin123' as the password.";
} catch (PDOException $e) {
    // Handle any errors
    echo "Error: " . $e->getMessage();
}
?>
