<?php
include 'config.php';  // Database connection
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get user input from the signup form
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm-password'];
    $full_name = $_POST['full_name']; // Get full name from form
    $role = $_POST['role'];

    // Check if passwords match
    if ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Hash the password securely
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the new user data into the database
        try {
            // Prepare the insert query
            $stmt = $conn->prepare("INSERT INTO users (username, email, password, full_name, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$username, $email, $hashed_password, $full_name, $role]);

            // Redirect to success page after successful registration
            header("Location: registration_success.php");
            exit; // Ensure no further code is executed after the redirect
        } catch (PDOException $e) {
            $error_message = "Error occurred: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <style>
        /* Your provided CSS styles here */
        /* Keep the same styles you previously defined */
    </style>
</head>
<body>
    <div class="signup-container">
        <h1>Create an Account</h1>
        <?php if (isset($error_message)): ?>
            <p style="color: red; text-align: center;"><?= $error_message ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <!-- Form fields as previously defined -->
        </form>

        <p class="subtitle">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>
</body>
</html>
