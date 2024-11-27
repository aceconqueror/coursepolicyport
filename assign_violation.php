<?php
include 'config.php';
session_start();

// Ensure the user is logged in as faculty
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty') {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];  // Student ID
    $policy_id = $_POST['policy_id'];  // Policy ID
    $sanction = !empty($_POST['sanction']) ? $_POST['sanction'] : NULL; // Optional sanction

    // Insert the violation into the database with a "pending" status
    try {
        $stmt = $conn->prepare("INSERT INTO violations (user_id, policy_id, faculty_id, sanction, status, created_at) 
                                VALUES (?, ?, ?, ?, 'pending', NOW())");
        // Insert the faculty's ID from session
        $stmt->execute([$user_id, $policy_id, $_SESSION['user_id'], $sanction]);

        $_SESSION['message'] = "Violation assigned successfully!";
        header("Location: faculty_dashboard.php");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Violation</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
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
            max-width: 800px;
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

        /* Form Styling */
        form {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        label {
            font-size: 18px;
            font-weight: bold;
        }

        input, select, textarea {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            outline: none;
            transition: border-color 0.3s;
        }

        input:focus, select:focus, textarea:focus {
            border-color: #2a9df4;
        }

        button {
            background-color: #2a9df4;
            color: white;
            font-size: 18px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #1a7dc4;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-content {
                padding: 20px;
            }

            label, input, select, button {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <div class="top-bar">
        <div class="nav-links">
            <a href="faculty_dashboard.php">Home</a>
        </div>
    </div>

    <!-- Main Content Section -->
    <div class="main-content">
        <h1>Assign Violation</h1>
        <form method="POST">
            <!-- Student ID -->
            <label for="user_id">Student ID:</label>
            <input type="text" id="user_id" name="user_id" placeholder="Enter student ID" required>

            <!-- Policy Selection -->
            <label for="policy_id">Policy:</label>
            <select id="policy_id" name="policy_id" required>
                <option value="">Select a policy</option>
                <!-- Populate with policies dynamically -->
                <?php
                $stmt = $conn->prepare("SELECT * FROM policies");
                $stmt->execute();
                $policies = $stmt->fetchAll();
                foreach ($policies as $policy) {
                    echo '<option value="' . htmlspecialchars($policy['id']) . '">' . htmlspecialchars($policy['policy_name']) . '</option>';
                }
                ?>
            </select>

            <!-- Sanction (Optional) -->
            <label for="sanction">Sanction (Optional):</label>
            <textarea id="sanction" name="sanction" rows="4" placeholder="Enter sanction if applicable"></textarea>

            <!-- Submit Button -->
            <button type="submit">Assign Violation</button>
        </form>
    </div>
</body>
</html>

