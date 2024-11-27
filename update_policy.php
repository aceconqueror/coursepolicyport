<?php
include 'config.php';
session_start();

// Ensure the user is logged in as faculty
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'faculty') {
    die("Access denied.");
}

// Fetch the policy to be updated
if (isset($_GET['id'])) {
    $policy_id = $_GET['id'];
    $stmt = $conn->prepare("SELECT * FROM policies WHERE id = ?");
    $stmt->execute([$policy_id]);
    $policy = $stmt->fetch();

    if (!$policy) {
        die("Policy not found.");
    }
}

// Process the form submission for updating the policy
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $policy_name = $_POST['policy_name'];
    $description = $_POST['description'];
    $sanction = $_POST['sanction'];
    $proof_type = $_POST['proof_type'];

    $stmt = $conn->prepare("UPDATE policies SET policy_name = ?, description = ?, sanction = ?, proof_type = ? WHERE id = ?");
    if ($stmt->execute([$policy_name, $description, $sanction, $proof_type, $policy_id])) {
        $_SESSION['message'] = "Policy updated successfully!";
        header('Location: faculty_dashboard.php?page=home');
        exit();
    } else {
        $_SESSION['message'] = "Failed to update policy!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Policy</title>
    <link rel="stylesheet" href="style.css"> <!-- Your CSS file -->
</head>
<body>
    <div class="form-container">
        <h1>Update Policy</h1>
        <?php if (isset($_SESSION['message'])): ?>
            <div class="error-message"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form action="update_policy.php?id=<?= $policy['id'] ?>" method="POST">
            <label for="policy_name">Policy Name:</label>
            <input type="text" name="policy_name" value="<?= htmlspecialchars($policy['policy_name']) ?>" required>

            <label for="description">Description:</label>
            <textarea name="description" required><?= htmlspecialchars($policy['description']) ?></textarea>

            <label for="sanction">Sanction:</label>
            <textarea name="sanction" required><?= htmlspecialchars($policy['sanction']) ?></textarea>

            <label for="proof_type">Proof Type:</label>
            <input type="text" name="proof_type" value="<?= htmlspecialchars($policy['proof_type']) ?>" required>

            <button type="submit" class="btn update-btn">Update Policy</button>
        </form>
    </div>
</body>
</html>
