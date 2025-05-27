<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php';

// Redirect if not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['admin_id'];
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $message = '<div class="alert alert-danger">All fields are required.</div>';
    } elseif ($newPassword !== $confirmPassword) {
        $message = '<div class="alert alert-danger">New password and confirmation do not match.</div>';
    } elseif (strlen($newPassword) < 6) {
        $message = '<div class="alert alert-danger">New password must be at least 6 characters long.</div>';
    } else {
        $stmt = $conn->prepare("SELECT password FROM admins WHERE id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $userId);
            $stmt->execute();
            $stmt->bind_result($hashedPassword);

            if ($stmt->fetch()) {
                if (!password_verify($currentPassword, $hashedPassword)) {
                    $message = '<div class="alert alert-danger">Current password is incorrect.</div>';
                } else {
                    $stmt->close(); // ✅ Close only after successful fetch

                    $newHashed = password_hash($newPassword, PASSWORD_DEFAULT);
                    $updateStmt = $conn->prepare("UPDATE admins SET password = ? WHERE id = ?");
                    if ($updateStmt) {
                        $updateStmt->bind_param("si", $newHashed, $userId);
                        if ($updateStmt->execute()) {
                            $message = '<div class="alert alert-success">Password updated successfully.</div>';
                        } else {
                            $message = '<div class="alert alert-danger">Error updating password.</div>';
                        }
                        $updateStmt->close();
                    } else {
                        $message = '<div class="alert alert-danger">Failed to prepare update statement.</div>';
                    }
                }
            } else {
                $message = '<div class="alert alert-danger">User not found.</div>';
                $stmt->close(); // ✅ Still close if fetch failed
            }
        } else {
            $message = '<div class="alert alert-danger">Failed to prepare fetch statement.</div>';
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Change Password - GurkhaMind Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
<div class="container my-5" style="max-width: 500px;">
    <h3 class="mb-4">Change Password</h3>

    <?= $message ?>

    <form method="POST" action="">
        <div class="mb-3">
            <label for="current_password" class="form-label">Current Password</label>
            <input type="password" id="current_password" name="current_password" class="form-control" required />
        </div>

        <div class="mb-3">
            <label for="new_password" class="form-label">New Password</label>
            <input type="password" id="new_password" name="new_password" class="form-control" required minlength="6" />
        </div>

        <div class="mb-3">
            <label for="confirm_password" class="form-label">Confirm New Password</label>
            <input type="password" id="confirm_password" name="confirm_password" class="form-control" required minlength="6" />
        </div>

        <button type="submit" class="btn btn-primary">Update Password</button>
    </form>
</div>
</body>
</html>
