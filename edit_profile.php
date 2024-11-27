<?php
include 'config.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Access denied.");
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];

    $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ? WHERE id = ?");
    $stmt->execute([$full_name, $email, $user_id]);
    echo "Profile updated.";
}

// Fetch user info
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();
?>
<h1>Edit Profile</h1>
<form method="POST" action="">
    <input type="text" name="full_name" value="<?= $user['full_name'] ?>" required><br>
    <input type="email" name="email" value="<?= $user['email'] ?>" required><br>
    <button type="submit">Update</button>
</form>
