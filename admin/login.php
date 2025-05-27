<?php
require 'config.php';

if (isset($_SESSION['admin_logged_in'])) {
    header("Location: dashboard.php");
    exit();
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM admins WHERE username=? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $user = $res->fetch_assoc();
        if (password_verify($password, $user['password'])) {
              $_SESSION['admin_logged_in'] = true;
$_SESSION['admin_username'] = $username;
$_SESSION['admin_id'] = $user['id']; // ✅ This stores the admin ID
header("Location: dashboard.php");
exit();

            header("Location: dashboard.php");
            exit();
        }
    }
    $error = "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GurkhaMind</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/a2e0e9ad79.js" crossorigin="anonymous"></script>
    <style>
        body {
            background: linear-gradient(135deg, #dbeafe, #f8fafc);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .login-box {
            max-width: 420px;
            margin: 80px auto;
            padding: 40px;
            border-radius: 16px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(0,0,0,0.1);
        }
        .form-control:focus {
            box-shadow: none;
            border-color: #0d6efd;
        }
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 38px;
            cursor: pointer;
            color: #6c757d;
        }
        .forgot-link {
            text-align: right;
            display: block;
            margin-top: -10px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-box">
       <h3 class="mb-3 text-center text-dark">
    Hello Gurkha<span style="color: green;">Mind</span> 👋
</h3>

        <p class="text-center text-muted mb-4">Welcome back! Please log in to continue.</p>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username">Username</label>
                <input type="text" name="username" class="form-control" required autofocus>
            </div>
            <div class="mb-3 position-relative">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
                <span class="password-toggle" onclick="togglePassword()">
                    <i id="toggle-icon" class="fas fa-eye"></i>
                </span>
            </div>
            <a href="forgot-password.php" class="forgot-link">Forgot password?</a>
            <button class="btn btn-primary w-100">Login</button>
        </form>
    </div>

    <script>
        function togglePassword() {
            const password = document.getElementById("password");
            const icon = document.getElementById("toggle-icon");
            if (password.type === "password") {
                password.type = "text";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            } else {
                password.type = "password";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            }
        }
    </script>
</body>
</html>
