<?php
include 'config.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // Added to validate role

    try {
        // Fetch user by username and role
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND role = ?");
        $stmt->execute([$username, $role]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($user['role'] == 'student') {
                header('Location: student_dashboard.php');
            } elseif ($user['role'] == 'faculty') {
                header('Location: faculty_dashboard.php');
            } elseif ($user['role'] == 'admin') {
                header('Location: admin_dashboard.php');
            }
            exit;
        } else {
            $error_message = "Invalid username, password, or role.";
        }
    } catch (PDOException $e) {
        $error_message = "An error occurred: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log In</title>
    <style>
        /* Your provided CSS styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: url('8.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            height: 100vh;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

.login-container {
    width: 35%; /* Increase width from 30% to 40% */
    padding: 1rem; /* Increase padding for more spacing */
    margin-left: 15%; /* Adjust margin for better centering */
    background: rgba(255, 255, 255, 0.9);
    border-radius: 12px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
}

.login-container h1 {
    font-size: 3.5rem; /* Increase font size for heading */
    margin-bottom: 4rem; /* Add more spacing below the heading */
    text-align: center;
    color: #444;
}

        .login-container .subtitle {
            font-size: 0.8rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .login-container .subtitle a {
            color: #007bff;
            text-decoration: none;
        }

        .login-container .subtitle a:hover {
            text-decoration: underline;
        }

        .login-container form {
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }

        .input-group {
            display: flex;
            flex-direction: column;
        }

        .input-group label {
            font-size: 0.85rem;
            margin-bottom: 0.4rem;
        }

        .input-group input, .input-group select {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 1rem;
        }

        .login-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.8rem;
        }

        .login-options label {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .login-options a {
            color: #007bff;
            text-decoration: none;
        }

        .login-options a:hover {
            text-decoration: underline;
        }

        .login-container button {
            padding: 0.6rem;
            background-color: #007bff;
            color: #fff;
            font-size: 0.95rem;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1>Welcome!</h1>
        <?php if (isset($error_message)): ?>
            <p style="color: red; text-align: center;"><?= $error_message ?></p>
        <?php endif; ?>
        <form action="login.php" method="POST">
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>

            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>

            <div class="input-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="student">Student</option>
                    <option value="faculty">Faculty</option>
                    <option value="admin">Admin</option>
                </select>
            </div>

            <div class="login-options">
                <label>
                    <input type="checkbox" name="remember">
                    Remember me
                </label>
                <a href="forgot-password.html">Forgot Password?</a>
            </div>

            <button type="submit">Sign In</button>
        </form>

        <p class="subtitle">
            Don't have an account? <a href="register.php">Sign Up</a>
        </p>
    </div>
</body>
</html>