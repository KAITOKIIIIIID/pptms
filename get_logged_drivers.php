<?php
session_start();
include 'db.php';

// Fetch logged-in drivers ordered by login time
$result = $conn->query("SELECT name, login_time, max_cap, plate_no FROM drivers WHERE logged_in = 1 ORDER BY login_time ASC");

$drivers = [];
while ($row = $result->fetch_assoc()) {
    $drivers[] = $row;
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($drivers);
?>
