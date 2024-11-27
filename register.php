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

            // On success, store session info and redirect
            $_SESSION['user_id'] = $conn->lastInsertId(); // Get the last inserted ID
            $_SESSION['role'] = $role;

            // Redirect to the registration success page
            header('Location: registration_success.html');
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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Arial', sans-serif;
            background: url('10.png') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            height: 100vh;
            display: flex;
            justify-content: flex-start;
            align-items: center;
        }

        .signup-container {
            width: 40%;
            padding: 1rem;
            margin-left: 10%;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .signup-container h1 {
            font-size: 2.8rem;
            margin-bottom: 2rem;
            text-align: center;
            color: #444;
        }

        .signup-container .subtitle {
            font-size: 0.8rem;
            margin-top: 2rem;
            margin-bottom: 1rem;
            text-align: center;
        }

        .signup-container .subtitle a {
            color: #007bff;
            text-decoration: none;
        }

        .signup-container .subtitle a:hover {
            text-decoration: underline;
        }

        .signup-container form {
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

        .signup-container button {
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

        .signup-container button:hover {
            background-color: #0056b3;
        }

        /* Password Strength Indicator */
        .strength-indicator {
            font-size: 0.8rem;
            margin-top: 0.5rem;
            display: none;
        }

        .strength-indicator span {
            display: inline-block;
            width: 100%;
            height: 5px;
            margin-top: 5px;
            border-radius: 5px;
        }

        .weak {
            background-color: #ff4d4d;
        }

        .medium {
            background-color: #ffcc00;
        }

        .strong {
            background-color: #33cc33;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <h1>Create an Account</h1>
        <?php if (isset($error_message)): ?>
            <p style="color: red; text-align: center;"><?= $error_message ?></p>
        <?php endif; ?>
        <form action="register.php" method="POST">
            <div class="input-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" placeholder="Enter your username" required>
            </div>

            <div class="input-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>

            <div class="input-group">
                <label for="full_name">Full Name:</label>
                <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required>
            </div>

            <div class="input-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <div class="strength-indicator" id="password-strength-indicator">
                    <span id="password-strength-bar" class="weak"></span>
                    <p id="password-strength-text">Weak password</p>
                </div>
            </div>

            <div class="input-group">
                <label for="confirm-password">Confirm Password:</label>
                <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirm your password" required>
                <div class="strength-indicator" id="confirm-password-strength-indicator">
                    <span id="confirm-password-strength-bar" class="weak"></span>
                    <p id="confirm-password-strength-text">Weak password</p>
                </div>
            </div>

            <div class="input-group">
                <label for="role">Role:</label>
                <select id="role" name="role" required>
                    <option value="student">Student</option>
                    <option value="faculty">Faculty</option>
                </select>
            </div>

            <button type="submit">Sign Up</button>
        </form>

        <p class="subtitle">
            Already have an account? <a href="login.php">Login</a>
        </p>
    </div>

    <script>
        // Password strength check logic (same as previously provided)
        const passwordField = document.getElementById('password');
        const confirmPasswordField = document.getElementById('confirm-password');
        const passwordStrengthIndicator = document.getElementById('password-strength-indicator');
        const passwordStrengthBar = document.getElementById('password-strength-bar');
        const passwordStrengthText = document.getElementById('password-strength-text');
        
        const confirmPasswordStrengthIndicator = document.getElementById('confirm-password-strength-indicator');
        const confirmPasswordStrengthBar = document.getElementById('confirm-password-strength-bar');
        const confirmPasswordStrengthText = document.getElementById('confirm-password-strength-text');

        // Password strength check function
        function checkPasswordStrength(password, isConfirmPassword = false) {
            let strength = 0;
            const hasUpperCase = /[A-Z]/.test(password);
            const hasLowerCase = /[a-z]/.test(password);
            const hasDigits = /\d/.test(password);
            const hasSpecialChars = /[!@#$%^&*(),.?":{}|<>]/.test(password);
            const minLength = password.length >= 8;

            if (hasUpperCase) strength++;
            if (hasLowerCase) strength++;
            if (hasDigits) strength++;
            if (hasSpecialChars) strength++;
            if (minLength) strength++;

            const strengthLevel = (strength === 5) ? 'strong' :
                                  (strength >= 3) ? 'medium' : 'weak';

            if (isConfirmPassword) {
                confirmPasswordStrengthBar.className = strengthLevel;
                confirmPasswordStrengthText.textContent = strengthLevel.charAt(0).toUpperCase() + strengthLevel.slice(1) + ' password';
                confirmPasswordStrengthIndicator.style.display = (password.length > 0) ? 'block' : 'none';
            } else {
                passwordStrengthBar.className = strengthLevel;
                passwordStrengthText.textContent = strengthLevel.charAt(0).toUpperCase() + strengthLevel.slice(1) + ' password';
                passwordStrengthIndicator.style.display = (password.length > 0) ? 'block' : 'none';
            }
        }

        // Event listener for password and confirm password input fields
        passwordField.addEventListener('input', (e) => {
            checkPasswordStrength(e.target.value);
        });

        confirmPasswordField.addEventListener('input', (e) => {
            checkPasswordStrength(e.target.value, true);
        });

        // Check if passwords match on form submission
        confirmPasswordField.addEventListener('input', () => {
            if (confirmPasswordField.value !== passwordField.value) {
                confirmPasswordField.setCustomValidity("Passwords do not match");
            } else {
                confirmPasswordField.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
