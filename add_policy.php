<?php
include 'config.php';
session_start();

// Ensure the user is logged in as faculty
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty') {
    die("Access denied.");
}

// Process the form submission for adding a new policy
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input data
    $policy_name = $_POST['policy_name'];
    $description = $_POST['description'];
    $sanction = $_POST['sanction'];
    $proof_type = $_POST['proof_type'];

    // Insert the policy into the database
    $stmt = $conn->prepare("INSERT INTO policies (policy_name, description, sanction, proof_type) VALUES (?, ?, ?, ?)");
    if ($stmt->execute([$policy_name, $description, $sanction, $proof_type])) {
        $_SESSION['message'] = "Policy added successfully!";
        header('Location: faculty_dashboard.php?page=home');
        exit();
    } else {
        $_SESSION['message'] = "Failed to add policy!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Policy</title>
    <link rel="stylesheet" href="style.css"> <!-- Your CSS file -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
        }

        .form-container {
            width: 40%;
            padding: 30px;
            margin: 50px auto;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        label {
            font-size: 14px;
            color: #555;
            margin-bottom: 10px;
            display: block;
        }

        input[type="text"], textarea {
            width: 100%;
            padding: 12px;
            margin: 8px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        textarea {
            resize: vertical;
        }

        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 15px;
            width: 100%;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .back-btn {
            position: absolute;
            top: 20px;
            left: 20px;
            background-color: #007bff;
            color: white;
            padding: 10px 15px;
            font-size: 14px;
            border-radius: 5px;
            text-decoration: none;
            display: inline-block;
        }

        .error-message {
            color: red;
            text-align: center;
            font-size: 14px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

    <!-- Back Button -->
    <a href="faculty_dashboard.php?page=home" class="back-btn">Back to Dashboard</a>

    <!-- Form Container -->
    <div class="form-container">
        <h1>Add a New Policy</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="error-message"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <form action="add_policy.php" method="POST">
            <label for="policy_name">Policy Name:</label>
            <input type="text" name="policy_name" required>

            <label for="description">Description:</label>
            <textarea name="description" required></textarea>

            <label for="sanction">Sanction:</label>
            <textarea name="sanction" required></textarea>

            <label for="proof_type">Proof Type:</label>
            <input type="text" name="proof_type" required>

            <button type="submit" class="btn add-btn">Add Policy</button>
        </form>
    </div>

</body>
</html>
