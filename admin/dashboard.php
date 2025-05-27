<?php
require 'config.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>GurkhaMind - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: #f2f5f9;
        }
        .sidebar {
            width: 240px;
            height: 100vh;
            background: #0d6efd;
            position: fixed;
            padding: 30px 15px;
            color: #fff;
        }
        .sidebar h4 {
            font-weight: 700;
            margin-bottom: 30px;
            text-decoration: underline;
        }
        .sidebar a {
            color: #cfd8dc;
            padding: 10px 15px;
            display: block;
            border-radius: 6px;
            text-decoration: none;
            margin-bottom: 8px;
        }
        .sidebar a:hover, .sidebar a.active {
            background: #ffffff22;
            color: #fff;
        }
        .main {
            margin-left: 240px;
            padding: 30px;
        }
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 0 12px rgba(0,0,0,0.05);
        }
        .stat {
            font-size: 1.25rem;
            font-weight: 600;
        }
        .navbar {
            margin-left: 240px;
            background: #fff;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar">
    <h4>GurkhaMind</h4>
    <a href="dashboard.php" class="<?= !isset($_GET['page']) ? 'active' : '' ?>"><i class="fas fa-chart-line me-2"></i>Dashboard</a>
    <a href="dashboard.php?page=services" class="<?= (isset($_GET['page']) && $_GET['page'] == 'services') ? 'active' : '' ?>"><i class="fas fa-briefcase me-2"></i>Services</a>

    <a href="dashboard.php?page=team" class="<?= (isset($_GET['page']) && $_GET['page'] == 'team') ? 'active' : '' ?>"><i class="fas fa-users-cog me-2"></i>Team</a>

    <a href="dashboard.php?page=contact_messages" class="<?= (isset($_GET['page']) && $_GET['page'] == 'contact_messages') ? 'active' : '' ?>">
    <i class="fas fa-envelope-open-text me-2"></i>Contact Messages
</a>

    <a href="dashboard.php?page=edit_home" class="<?= (isset($_GET['page']) && $_GET['page'] == 'edit_home') ? 'active' : '' ?>"><i class="fas fa-cogs me-2"></i>Edit System Info</a>
    <a href="dashboard.php?page=change_password" class="<?= (isset($_GET['page']) && $_GET['page'] == 'change_password') ? 'active' : '' ?>">
    <i class="fas fa-key me-2"></i>Change Password
</a>

    <a href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <span class="navbar-text ms-auto me-4">
            Welcome, <strong><?= htmlspecialchars($_SESSION['admin_username']) ?></strong>
        </span>
    </div>
</nav>

<!-- Main Content -->
<div class="main">
    
<?php
if (isset($_GET['page']) && $_GET['page'] === 'edit_home') {
    include 'edit_home.php';
} elseif (isset($_GET['page']) && $_GET['page'] === 'team') {
    include 'team.php';
} elseif (isset($_GET['page']) && $_GET['page'] === 'services') {
    include 'services.php';
} elseif (isset($_GET['page']) && $_GET['page'] === 'contact_messages') {
    include 'view_contacts.php'; // this file will list all contact form submissions
}elseif (isset($_GET['page']) && $_GET['page'] === 'change_password') {
    include 'change_password.php';
} elseif (isset($_GET['page']) && $_GET['page'] === 'edit_home') {
    include 'edit_home.php';
}
 else {
    
    // default dashboard content
?>

    
        <h2 class="mb-4">Hello Gurkha<span class="text-success">Mind</span></h2>

        <div class="row g-4">
            <div class="col-md-3">
                <div class="card p-3 text-center bg-light">
                    <i class="fas fa-code fa-2x text-primary mb-2"></i>
                    <div class="stat">12</div>
                    <small>Active Projects</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 text-center bg-light">
                    <i class="fas fa-cloud-upload-alt fa-2x text-success mb-2"></i>
                    <div class="stat">8</div>
                    <small>Live Deployments</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 text-center bg-light">
                    <i class="fas fa-headset fa-2x text-info mb-2"></i>
                    <div class="stat">24</div>
                    <small>Open Tickets</small>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card p-3 text-center bg-light">
                    <i class="fas fa-user-clock fa-2x text-warning mb-2"></i>
                    <div class="stat">320</div>
                    <small>Dev Hours Logged</small>
                </div>
            </div>
        </div>

        <div class="mt-5">
            <h5>Recent Updates</h5>
            <ul class="list-group">
                <li class="list-group-item">🔄 New sprint started for “Client CRM Portal”</li>
                <li class="list-group-item">✅ Project “E-commerce API” marked as complete</li>
                <li class="list-group-item">🛠️ DevOps team deployed version 2.1.3</li>
            </ul>
        </div>
    <?php } ?>
</div>

</body>
</html>
