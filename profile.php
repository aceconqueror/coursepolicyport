<?php
include 'config.php';
session_start();

// Ensure the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    die("Access denied.");
}

// Get the current user's details
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle the profile update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Handle file upload for profile picture
    if ($_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $profile_picture = $upload_dir . basename($_FILES['profile_picture']['name']);
        
        // Move the uploaded file to the 'uploads' directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture)) {
            echo "File uploaded successfully.";
        } else {
            echo "Error uploading file.";
        }
    } else {
        // If no new profile picture, keep the old one
        $profile_picture = $user['profile_picture'];
    }

    // Update the user's profile in the database
    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, profile_picture = ? WHERE id = ?");
    $stmt->execute([$name, $email, $profile_picture, $user_id]);

    echo "Profile updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 20px;
            background-color: #2a9df4;
            color: #fff;
        }

        .header .back-button {
            text-decoration: none;
            background-color: #fff;
            color: #2a9df4;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: bold;
            transition: all 0.3s;
        }

        .header .back-button:hover {
            background-color: #2278b8;
            color: #fff;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        .container h1 {
            text-align: center;
            color: #2a9df4;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        label {
            font-weight: bold;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        input[type="file"] {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 100%;
        }

        button {
            padding: 15px;
            background-color: #2a9df4;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2278b8;
        }

        .profile-picture {
            display: block;
            margin: 20px auto;
            border-radius: 50%;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="student_dashboard.php" class="back-button">Back to Dashboard</a>
        <h2>Student Portal</h2>
    </div>

    <div class="container">
        <h1>Edit Profile</h1>
        <form method="POST" enctype="multipart/form-data">
            <label for="name">Full Name:</label>
            <input type="text" name="name" value="<?= htmlspecialchars($user['full_name']) ?>" required>

            <label for="email">Email:</label>
            <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>

            <label for="profile_picture">Profile Picture:</label>
            <img src="<?= htmlspecialchars($user['profile_picture']) ?>" alt="Current Profile Picture" class="profile-picture" width="100">
            <input type="file" name="profile_picture" accept="image/*">

            <button type="submit">Save Changes</button>
        </form>
    </div>
</body>
</html>
