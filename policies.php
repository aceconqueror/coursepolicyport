<?php
include 'config.php';
session_start();

// Ensure the user is logged in as a student or admin (based on your design)
if (!isset($_SESSION['role']) || ($_SESSION['role'] != 'student' && $_SESSION['role'] != 'admin')) {
    die("Access denied.");
}

// Fetch all policies from the database
$stmt = $conn->prepare("SELECT * FROM policies");
$stmt->execute();
$policies = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Policies</title>
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
            padding: 20px 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-size: 18px;
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

        /* Main Content Section */
        .main-content {
            max-width: 1200px;
            margin: 50px auto;
            padding: 30px;
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

        /* Table Styling */
        .table-container {
            margin: 30px auto;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 16px;
        }

        table th, table td {
            padding: 15px;
            text-align: left;
            border-bottom: 2px solid #f4f4f9;
        }

        table th {
            background-color: #2a9df4;
            color: white;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        table tr:hover {
            background-color: #f4f4f9;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
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
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="main-content">
        <h1>Institutional Policies</h1>
        
        <!-- Policies Table -->
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Policy Name</th>
                        <th>Description</th>
                        <th>Sanction</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($policies)): ?>
                        <?php foreach ($policies as $policy): ?>
                        <tr>
                            <td><?= htmlspecialchars($policy['policy_name']) ?></td>
                            <td><?= htmlspecialchars($policy['description']) ?></td>
                            <td><?= htmlspecialchars($policy['sanction']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" style="text-align: center;">No policies available.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>
