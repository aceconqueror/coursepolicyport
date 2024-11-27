<?php
include 'config.php';
session_start();

// Ensure the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    die("Access denied.");
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("
    SELECT v.id AS violation_id, p.policy_name, p.description, p.sanction, v.status, v.proof 
    FROM violations v
    INNER JOIN policies p ON v.policy_id = p.id
    WHERE v.user_id = ?");
$stmt->execute([$user_id]);
$violations = $stmt->fetchAll();

// Fetch user details for the profile (name, profile picture)
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
			background: url('2.png') no-repeat center center fixed;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            color: #333;
        }

        /* Navigation Bar */
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #2a9df4;
            padding: 20px 30px; /* Increased padding for size */
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-size: 18px; /* Bigger font size */
        }

        .top-bar .nav-links a {
            text-decoration: none;
            color: white;
            margin-right: 20px;
            font-weight: bold;
            transition: color 0.3s, transform 0.3s;
        }

        .top-bar .nav-links a:hover {
            color: #ffdd57;
            transform: scale(1.1);
        }

        .profile-dropdown {
            position: relative;
            display: inline-block;
        }

        .profile-pic {
            width: 50px; /* Increased size */
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .dropdown-content {
            display: none;
            position: absolute;
            right: 0;
            background-color: white;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
            border-radius: 10px;
            overflow: hidden;
            z-index: 100;
        }

        .dropdown-content a {
            text-decoration: none;
            display: block;
            padding: 15px; /* More spacious links */
            color: #333;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        .dropdown-content a:hover {
            background-color: #f4f4f9;
        }

        .dropdown-content.show {
            display: block;
        }

        /* Main Content */
        .main-content {
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px; /* Added more padding */
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #2a9df4;
            font-size: 36px;
            margin-bottom: 20px;
        }

        h2 {
            text-align: center;
            color: #555;
            font-size: 24px;
        }

        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px; /* Increased spacing above */
            font-size: 16px; /* Larger text */
        }

        table th, table td {
            padding: 15px; /* Added padding for bigger cells */
            text-align: left;
            border-bottom: 2px solid #f4f4f9; /* Thicker divider */
        }

        table th {
            background-color: #2a9df4;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        table tr:hover {
            background-color: #f4f4f9; /* Hover effect */
        }

        table a {
            text-decoration: none;
            color: #2a9df4;
            font-weight: bold;
            transition: color 0.3s, transform 0.3s;
        }

        table a:hover {
            color: #2278b8;
            transform: scale(1.1);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .top-bar {
                flex-direction: column;
                align-items: flex-start;
            }

            .nav-links a {
                margin: 10px 0;
            }

            .main-content {
                padding: 20px;
            }

            table th, table td {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="top-bar">
        <div class="nav-links">
            <a href="student_dashboard.php">Home</a>
            <a href="policies.php">Policies</a>
        </div>
        <div class="user-info">
            <div class="profile-dropdown">
                <img src="uploads/Kehl.webp<?= htmlspecialchars($user['profile_picture']) ?>" alt="Profile Picture" class="profile-pic">
                <div class="dropdown-content">
                    <a href="profile.php">Edit Profile</a>
                    <a href="logout.php">Log Out</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="main-content">
        <h1>Welcome, <?= htmlspecialchars($user['full_name']) ?></h1>
        <h2>Your Violations</h2>
        <table>
            <tr>
                <th>Policy Name</th>
                <th>Description</th>
                <th>Sanction</th>
                <th>Status</th>
                <th>Proof</th>
                <th>Action</th>
            </tr>
            <?php foreach ($violations as $violation): ?>
            <tr>
                <td><?= htmlspecialchars($violation['policy_name']) ?></td>
                <td><?= htmlspecialchars($violation['description']) ?></td>
                <td><?= htmlspecialchars($violation['sanction']) ?></td>
                <td><?= ucfirst(htmlspecialchars($violation['status'])) ?></td>
                <td>
                    <?php if ($violation['proof']): ?>
                    <a href="uploads/<?= htmlspecialchars($violation['proof']) ?>" target="_blank">View Proof</a>
                    <?php else: ?>
                    No Proof
                    <?php endif; ?>
                </td>
                <td>
                    <?php if ($violation['status'] == 'pending'): ?>
                    <a href="respond.php?violation_id=<?= $violation['violation_id'] ?>">Respond</a>
                    <?php else: ?>
                    Resolved
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>

    <script>
        // Dropdown interactivity
        const profilePic = document.querySelector('.profile-pic');
        const dropdownContent = document.querySelector('.dropdown-content');

        profilePic.addEventListener('click', () => {
            dropdownContent.classList.toggle('show');
        });

        window.addEventListener('click', (event) => {
            if (!profilePic.contains(event.target) && !dropdownContent.contains(event.target)) {
                dropdownContent.classList.remove('show');
            }
        });
    </script>
</body>
</html>
