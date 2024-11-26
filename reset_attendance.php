<?php
include 'db.php';

// Reset attendance for the new day
$date = date('Y-m-d', strtotime('tomorrow')); // Use 'tomorrow' to schedule this for the next day
$stmt = $conn->prepare("INSERT INTO driver_attendance (license_no, status, date)
                        SELECT license_no, 'absent', ? FROM drivers
                        ON DUPLICATE KEY UPDATE status = 'absent'");
$stmt->bind_param("s", $date);
$stmt->execute();

echo "Attendance reset for $date.";
?>
