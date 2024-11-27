<?php
include 'config.php';
session_start();

// Ensure the user is logged in as faculty
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty') {
    die("Access denied.");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $violation_id = $_POST['violation_id'];
    $status = $_POST['status']; // The new status (e.g., "resolved", "responded")

    // Update the violation status
    try {
        $stmt = $conn->prepare("UPDATE violations SET status = ? WHERE id = ?");
        $stmt->execute([$status, $violation_id]);

        echo "Violation status updated successfully!";
        header("Location: faculty_dashboard.php");
        exit;
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>