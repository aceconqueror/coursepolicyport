<?php
include 'config.php';
session_start();

// Ensure the user is logged in as faculty
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty') {
    die("Access denied.");
}

$faculty_id = $_SESSION['user_id'];

// Fetch available policies
$stmt_policies = $conn->prepare("SELECT * FROM policies");
$stmt_policies->execute();
$policies = $stmt_policies->fetchAll();

// Fetch list of students
$stmt_students = $conn->prepare("SELECT id, full_name FROM users WHERE role = 'student'");
$stmt_students->execute();
$students = $stmt_students->fetchAll();

// Fetch faculty details for profile dropdown
$stmt_faculty = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt_faculty->execute([$faculty_id]);
$faculty = $stmt_faculty->fetch();

// Determine which section to show: Home or Assign Violation
$page = isset($_GET['page']) ? $_GET['page'] : 'home'; // Default to 'home'
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Faculty Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to external CSS -->
    <style>
	
	        body {
            font-family: 'Roboto', sans-serif;
			background: url('2.png') no-repeat center center fixed;
            background-color: #f4f7fc;
            color: #333;
        }
		
        /* Navigation Bar */
        .top-bar {
            background-color: #00aaff; 
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
        }

        .top-bar .nav-links a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            font-size: 16px;
        }

        .top-bar .user-info .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-pic {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            cursor: pointer;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #fff;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
        }

        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }

        .dropdown-content a:hover {
            background-color: #ddd;
        }

        /* Main Content */
        .main-content {
            padding: 20px;
            margin-top: 100px; /* Space for the fixed navbar */
			background: url('2.png') no-repeat center center fixed;
        }

        h1, h2 {
            color: #333;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;

        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        .btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            display: inline-block;
            font-size: 14px;
            margin-top: 10px;
            cursor: pointer;
        }

        .btn:hover {
            background-color: #45a049;
        }

        .action-box {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

        .assign-form-container {
            background-color: white;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 50%;
            margin: 0 auto;
        }

        .assign-form-container h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .assign-form-container form label {
            font-size: 16px;
            margin-bottom: 10px;
            display: block;
            color: #333;
            text-transform: uppercase; /* Title is now in uppercase */
        }

        .assign-form-container form select,
        .assign-form-container form textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .assign-form-container form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        .assign-form-container form button:hover {
            background-color: #0056b3;
        }

        .success-message {
            color: green;
            text-align: center;
            font-size: 16px;
            margin-bottom: 20px;
        }

    </style>
</head>
<body>

<!-- Navigation Bar -->
<div class="top-bar">
    <div class="nav-links">
        <a href="faculty_dashboard.php?page=home">Home</a>
        <a href="faculty_dashboard.php?page=assign_violation">Assign Violation</a>
    </div>
    <div class="user-info">
        <div class="profile-dropdown">
            <img src="uploads/<?= htmlspecialchars($faculty['profile_picture']) ?>" alt="Profile Picture" class="profile-pic">
            <div class="dropdown-content">
                <a href="profile.php">Edit Profile</a>
                <a href="logout.php">Log Out</a>
            </div>
        </div>
    </div>
</div>

<!-- Main Content -->
<div class="main-content">
    <?php if (isset($_SESSION['message'])): ?>
        <div class="success-message"><?= $_SESSION['message'] ?></div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if ($page == 'home'): ?>
        <!-- Home Section -->
        <h1>Welcome, <?= htmlspecialchars($faculty['full_name']) ?></h1>
        <h2>Policies</h2>
        <table>
            <tr>
                <th>Policy Name</th>
                <th>Description</th>
                <th>Sanction</th>
                <th>Proof Type</th>
            </tr>
            <?php foreach ($policies as $policy): ?>
            <tr>
                <td><?= htmlspecialchars($policy['policy_name']) ?></td>
                <td><?= htmlspecialchars($policy['description']) ?></td>
                <td><?= htmlspecialchars($policy['sanction']) ?></td>
                <td><?= htmlspecialchars($policy['proof_type']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>

        <div class="action-box">
            <!-- Align Add Policy to the left -->
            <a href="add_policy.php" class="btn add-btn">Add Policy</a>

            <!-- Align Update and Remove Policy to the right -->
            <div>
                <a href="update_policy.php" class="btn update-btn">Update Policy</a>
                <a href="delete_policy.php" class="btn delete-btn">Remove Policy</a>
            </div>
        </div>

    <?php elseif ($page == 'assign_violation'): ?>
        <!-- Assign Violation Section -->
        <div class="assign-form-container">
            <h1>Assign a Violation</h1>
            <form action="assign_violation.php" method="POST">
                <label for="user_id">SELECT STUDENT:</label>
                <select name="user_id" required>
                    <?php foreach ($students as $student): ?>
                        <option value="<?= $student['id'] ?>"><?= htmlspecialchars($student['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="policy_id">SELECT POLICY:</label>
                <select name="policy_id" required>
                    <?php foreach ($policies as $policy): ?>
                        <option value="<?= $policy['id'] ?>"><?= htmlspecialchars($policy['policy_name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="sanction">SANCTION (OPTIONAL):</label>
                <textarea name="sanction" rows="4"></textarea>

                <button type="submit" class="btn assign-btn">Assign Violation</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<!-- JavaScript for Profile Dropdown -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const profilePic = document.querySelector('.profile-pic');
        const dropdownContent = document.querySelector('.dropdown-content');

        if (!profilePic || !dropdownContent) {
            console.error("Dropdown elements not found!");
            return;
        }

        profilePic.addEventListener('click', function (event) {
            event.stopPropagation();
            dropdownContent.style.display =
                dropdownContent.style.display === 'block' ? 'none' : 'block';
        });

        window.addEventListener('click', function (event) {
            if (!profilePic.contains(event.target) && !dropdownContent.contains(event.target)) {
                dropdownContent.style.display = 'none';
            }
        });
    });
</script>
</body>
</html>

