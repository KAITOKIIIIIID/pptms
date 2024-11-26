<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// db.php: Database connection
include 'db.php';

// Initialize arrays for data
$profitData = array_fill_keys(['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'], 0);
$attendanceData = ['present' => 0, 'absent' => 0];

// Fetch weekly profit data with fallback
$stmt = $conn->prepare("SELECT day, profit FROM weekly_profit");
if ($stmt->execute()) {
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $profitData[$row['day']] = (float) $row['profit'];
    }
}
$stmt->close();

// Fetch driver attendance data with fallback
$attendanceQuery = $conn->query("
    SELECT 
        a.status, 
        COUNT(a.status) AS count 
    FROM drivers d 
    LEFT JOIN driver_attendance a 
        ON d.license_no = a.license_no AND a.date = CURDATE() 
    GROUP BY a.status
");

while ($row = $attendanceQuery->fetch_assoc()) {
    $status = $row['status'] ?? 'absent';
    $attendanceData[$status] = (int) $row['count'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Toda Management System</title>
    <link rel="stylesheet" href="Bootstrap5/css/bootstrap.css">
    <link rel= "stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> <!-- Chart.js library -->
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
            <h4>Admin Panel</h4>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : '' ?>" href="admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="add_driver.php">Add Driver</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="view_queue.php">Queue</a>
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
            <div class="container my-4">
                <h2 class="mb-4">Dashboard Overview</h2>

                <!-- Charts Row -->
                <div class="row">
                    <!-- Profit Bar Chart -->
                    <div class="col-md-6">
                        <h4>Weekly Profit</h4>
                        <canvas id="weeklyProfitChart"></canvas>
                    </div>
                    
                    <!-- Attendance Pie Chart -->
                    <div class="col-md-6">
                        <h4>Driver Attendance</h4>
                        <canvas id="driverAttendanceChart"></canvas>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // Weekly Profit Chart
    const weeklyProfitCtx = document.getElementById('weeklyProfitChart').getContext('2d');
    new Chart(weeklyProfitCtx, {
        type: 'bar',
        data: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            datasets: [{
                label: 'Weekly Profit',
                data: <?= json_encode(array_values($profitData)) ?>,
                backgroundColor: '#17a2b8'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Driver Attendance Chart
    const driverAttendanceCtx = document.getElementById('driverAttendanceChart').getContext('2d');
    new Chart(driverAttendanceCtx, {
        type: 'pie',
        data: {
            labels: ['Present', 'Absent'],
            datasets: [{
                data: <?= json_encode(array_values($attendanceData)) ?>,
                backgroundColor: ['#28a745', '#dc3545']
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
</body>
</html>
