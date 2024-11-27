<?php
include 'config.php';
session_start();

// Ensure the user is logged in as a student
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'student') {
    die("Access denied.");
}

$user_id = $_SESSION['user_id'];

// Get the data from the form
$violation_id = $_POST['violation_id'];
$policy_id = $_POST['policy_id'];
$response_text = $_POST['response_text'];
$file_name = '';

// If the student is required to upload proof, handle the file upload
if (isset($_FILES['proof'])) {
    $target_dir = "uploads/";
    $file_name = basename($_FILES['proof']['name']);
    $target_file = $target_dir . $file_name;

    // Move the uploaded file to the uploads directory
    if (move_uploaded_file($_FILES['proof']['tmp_name'], $target_file)) {
        echo "The file " . htmlspecialchars($file_name) . " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

// Insert or update the student's response for the violation
$stmt = $conn->prepare("
    UPDATE violations 
    SET status = 'responded', response_text = ?, proof = ? 
    WHERE id = ? AND user_id = ?");
$stmt->execute([$response_text, $file_name, $violation_id, $user_id]);

// Redirect back to the student dashboard
header("Location: student_dashboard.php");
exit;
?>
