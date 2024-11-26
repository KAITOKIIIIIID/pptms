<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit;
}

include 'db.php';

// Fetch logged-in drivers ordered by login time
$result = $conn->query("SELECT * FROM drivers WHERE logged_in = 1 ORDER BY login_time ASC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Queue - Toda Management System</title>
    <link rel= "stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap5/css/bootstrap.css">
</head>
<body>
    <!-- Header with System's Name and Icon -->
<header class="bg-primary text-white p-3">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
        <img src="icon.jpg" alt="System Icon" height="40"> <!-- System Icon -->
            <h1 class="h4 mb-0">Toda Management System</h1>    
        </div>
    </div>
</header>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-2 sidebar p-3">
            <h4 class="fw-bold">Admin Panel</h4>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : '' ?>" href="admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'add_driver.php' ? 'active' : '' ?>" href="add_driver.php">Add Driver</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'view_queue.php' ? 'active' : '' ?>" href="view_queue.php">Queue</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="driver_dashboard.php">Driver Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php" onclick="return confirm('Are you sure you want to logout?');">Logout</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="maps.php">Route</a>
                </li>
            </ul>
        </nav>

        <!-- Main Content -->
        <main class="col-md-10">
            <div class="container mt-5">
                <h2>Logged-in Drivers Queue</h2>
                <p>Viewing all logged-in drivers ordered by login time.</p>

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Login Time</th>
                        <th>License No</th>
                        <th>Plate No</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['name']) ?></td>
                            <td><?= htmlspecialchars($row['login_time']) ?></td>
                            <td><?= htmlspecialchars($row['license_no']) ?></td>
                            <td><?= htmlspecialchars($row['plate_no']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<script src="bootstrap5/js/bootstrap.bundle.min.js"></script>
</body>
</html>
