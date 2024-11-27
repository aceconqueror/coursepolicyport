<?php
include 'config.php';
session_start();

// Ensure the user is logged in as faculty
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty') {
    die("Access denied.");
}

// Delete a policy if the ID is provided
if (isset($_GET['id'])) {
    $policy_id = $_GET['id'];
    $stmt = $conn->prepare("DELETE FROM policies WHERE id = ?");
    if ($stmt->execute([$policy_id])) {
        $_SESSION['message'] = "Policy removed successfully!";
    } else {
        $_SESSION['message'] = "Failed to remove policy!";
    }
    header('Location: faculty_dashboard.php?page=home');
    exit();
} else {
    die("Policy ID not provided.");
}
