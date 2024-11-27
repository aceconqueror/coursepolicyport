<?php
include 'config.php';
session_start();

// Ensure the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    die("Access denied.");
}

// Check if there's a violation ID passed in the URL
if (isset($_GET['violation_id'])) {
    $violation_id = $_GET['violation_id']; // Get violation ID from URL

    // Fetch the violation based on the violation_id
    $stmt = $conn->prepare("SELECT v.id AS violation_id, p.policy_name, p.description, p.sanction, v.status
                            FROM violations v
                            INNER JOIN policies p ON v.policy_id = p.id
                            WHERE v.id = ? AND v.status = 'pending' AND v.user_id = ?");
    $stmt->execute([$violation_id, $_SESSION['user_id']]);
    $violation = $stmt->fetch();

    if ($violation) {
        // Violation found, show the violation details and response form
        ?>
        <form action="respond.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="violation_id" value="<?= $violation['violation_id'] ?>">

            <h3>Policy: <?= htmlspecialchars($violation['policy_name']) ?></h3>
            <p>Description: <?= htmlspecialchars($violation['description']) ?></p>
            <p>Sanction: <?= htmlspecialchars($violation['sanction']) ?></p>
            <p>Status: <?= htmlspecialchars($violation['status']) ?></p>

            <textarea name="response_text" rows="5" required></textarea>
            <button type="submit">Submit Response</button>
        </form>
        <?php
    } else {
        // If no pending violation is found or invalid violation ID
        echo "No pending violations to respond to.";
    }
} else {
    echo "Violation ID not provided.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['violation_id'])) {
    $violation_id = $_POST['violation_id']; // Get violation ID from the form
    $response_text = $_POST['response_text']; // Get the response text

    // Insert the student's response into the database and update the violation status
    try {
        $stmt = $conn->prepare("UPDATE violations SET response_text = ?, status = 'responded' WHERE id = ?");
        $stmt->execute([$response_text, $violation_id]);

        $_SESSION['message'] = "Response submitted successfully!";
        header("Location: student_dashboard.php"); // Redirect to the student dashboard
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
