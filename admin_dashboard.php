<?php
include 'config.php';
session_start();

// Ensure the user is logged in as admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access denied.");
}

// Fetch violations and users
$stmt_violations = $conn->query("
    SELECT u.full_name, p.policy_name, v.status, v.proof, v.created_at 
    FROM violations v
    INNER JOIN users u ON v.user_id = u.id
    INNER JOIN policies p ON v.policy_id = p.id
");
$violations = $stmt_violations->fetchAll();

$stmt_users = $conn->query("SELECT * FROM users");
$users = $stmt_users->fetchAll();

// Page variable to control content display
$page = isset($_GET['page']) ? $_GET['page'] : 'home'; // Default to 'home'

// Fetch user profile picture from database if not already set in session
if (!isset($_SESSION['profile_picture'])) {
    $stmt = $conn->prepare("SELECT profile_picture FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
    $_SESSION['profile_picture'] = $user['profile_picture']; // Store profile picture in session
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Your external CSS file -->
    <style>
        /* Global Styles */
        body {
            font-family: 'Roboto', sans-serif;
			background: url('2.png') no-repeat center center fixed;
            background-color: #f4f7fc;
            color: #333;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            padding: 15px 25px;
            background-color: #0062cc; /* Elegant deep blue */
            color: white;
            align-items: center;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }

        .nav-links a {
            text-decoration: none;
            color: white;
            padding: 12px 18px;
            font-size: 18px;
            font-weight: 500;
            margin-right: 15px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .nav-links a:hover {
            background-color: #004b99;
        }

        /* Profile Dropdown */
        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-pic {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            cursor: pointer;
            border: 2px solid white;
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: #fff;
            min-width: 160px;
            box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
            z-index: 1;
            border-radius: 5px;
            overflow: hidden;
        }

        .dropdown-content a {
            color: #333;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            font-size: 14px;
            font-weight: 400;
            transition: background-color 0.3s ease;
        }

        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }

        .dropdown-content.show {
            display: block;
        }

        /* Main Content Area */
        .content-area {
            margin-top: 30px;
            padding: 25px;
            background-color: white;
            border-radius: 15px;
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
            margin: 20px auto;
        }

        .content-area h1 {
            font-size: 32px;
            color: #333;
            margin-bottom: 25px;
            font-weight: 600;
        }

        .content-area h2 {
            font-size: 24px;
            color: #0062cc;
            margin-bottom: 20px;
            font-weight: 500;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 15px;
            text-align: left;
            font-size: 16px;
        }

        th {
            background-color: #f5f8fd;
            font-weight: 500;
        }

        tr:hover {
            background-color: #f1f5f9;
        }

        .table-actions {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            margin-top: 10px;
        }

        .table-actions a {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            margin-right: 15px;
            transition: background-color 0.3s ease;
        }

        .table-actions a:hover {
            background-color: #218838;
        }

    </style>
</head>
<body>

    <!-- Header with Navigation Links -->
    <div class="header">
        <div class="nav-links">
            <a href="admin_dashboard.php?page=home">Home</a>
            <a href="admin_dashboard.php?page=users">Users</a>
        </div>
        
        <!-- Profile Section on the Right -->
        <div class="profile-dropdown">
            <!-- Profile Picture -->
            <?php if (isset($_SESSION['profile_picture']) && $_SESSION['profile_picture']): ?>
                <img src="uploads/<?= htmlspecialchars($_SESSION['profile_picture']) ?>" alt="Profile Picture" class="profile-pic">
            <?php else: ?>
                <img src="uploads/default.jpg" alt="Default Profile Picture" class="profile-pic">
            <?php endif; ?>
            <div class="dropdown-content">
                <a href="profile.php">Edit Profile</a>
                <a href="logout.php">Log Out</a>
            </div>
        </div>
    </div>

    <!-- Content Area -->
    <div class="content-area">
        <!-- Displaying the content based on the page selection -->
        <h1>Admin Dashboard</h1>

        <?php if ($page == 'home'): ?>
            <!-- Violations Table -->
            <h2>Violations</h2>
            <table>
                <tr>
                    <th>Student Name</th>
                    <th>Policy</th>
                    <th>Status</th>
                    <th>Proof</th>
                    <th>Date</th>
                </tr>
                <?php foreach ($violations as $violation): ?>
                <tr>
                    <td><?= htmlspecialchars($violation['full_name']) ?></td>
                    <td><?= htmlspecialchars($violation['policy_name']) ?></td>
                    <td><?= ucfirst(htmlspecialchars($violation['status'])) ?></td>
                    <td>
                        <?php if ($violation['proof']): ?>
                        <a href="<?= htmlspecialchars($violation['proof']) ?>" target="_blank" style="color: #0062cc; text-decoration: underline;">View Proof</a>
                        <?php else: ?>
                        No Proof
                        <?php endif; ?>
                    </td>
                    <td><?= htmlspecialchars($violation['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>

        <?php elseif ($page == 'users'): ?>
            <!-- Users Table -->
            <h2>Users</h2>
            <table>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Profile Picture</th>
                    <th>Created At</th>
                </tr>
                <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= htmlspecialchars($user['id']) ?></td>
                    <td><?= htmlspecialchars($user['username']) ?></td>
                    <td><?= htmlspecialchars($user['full_name']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= htmlspecialchars($user['role']) ?></td>
                    <td>
                        <img src="uploads/<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile" width="50" height="50" style="border-radius: 5px;">
                    </td>
                    <td><?= htmlspecialchars($user['created_at']) ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>

    <!-- JS for Profile Dropdown -->
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const profilePic = document.querySelector('.profile-pic');
            const dropdown = document.querySelector('.dropdown-content');

            profilePic.addEventListener('click', function() {
                // Toggle dropdown visibility on click
                dropdown.classList.toggle('show');
            });

            // Close the dropdown if clicking outside
            window.addEventListener('click', function(event) {
                if (!profilePic.contains(event.target) && !dropdown.contains(event.target)) {
                    dropdown.classList.remove('show');
                }
            });
        });
    </script>

</body>
</html>
