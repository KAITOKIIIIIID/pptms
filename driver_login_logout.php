<?php
session_start();
include 'db.php';

$response = ['success' => false, 'message' => 'Invalid request.'];

if (isset($_GET['license']) && !empty($_GET['license'])) {
    $license = htmlspecialchars(trim($_GET['license']));

    try {
        $stmt = $conn->prepare("SELECT * FROM drivers WHERE license_no = ?");
        $stmt->bind_param("s", $license);
        $stmt->execute();
        $driver = $stmt->get_result()->fetch_assoc();

        if ($driver) {
            $isLoggedIn = $driver['logged_in'] ?? 0;

            $conn->begin_transaction();
            try {
                if ($isLoggedIn) {
                    // Log out
                    $stmt = $conn->prepare("UPDATE drivers SET logged_in = 0, logout_time = NOW() WHERE license_no = ?");
                    $stmt->bind_param("s", $license);
                    $stmt->execute();

                    $stmt = $conn->prepare("INSERT INTO driver_logs (license_no, driver_name, logout_time) VALUES (?, ?, NOW())");
                    $stmt->bind_param("ss", $license, $driver['name']);
                    $stmt->execute();

                    $response = ['success' => true, 'message' => "Logged out successfully. Goodbye, {$driver['name']}!"];
                } else {
                    // Log in
                    $stmt = $conn->prepare("UPDATE drivers SET logged_in = 1, login_time = NOW() WHERE license_no = ?");
                    $stmt->bind_param("s", $license);
                    $stmt->execute();

                    $stmt = $conn->prepare("INSERT INTO driver_logs (license_no, driver_name, login_time) VALUES (?, ?, NOW())");
                    $stmt->bind_param("ss", $license, $driver['name']);
                    $stmt->execute();

                    $stmt = $conn->prepare("INSERT INTO driver_attendance (license_no, status, date) 
                        VALUES (?, 'present', CURDATE())
                        ON DUPLICATE KEY UPDATE status = 'present'");
                    $stmt->bind_param("s", $license);
                    $stmt->execute();


                    $response = ['success' => true, 'message' => "Welcome, {$driver['name']}! You are now logged in."];
                }

                $conn->commit();
            } catch (Exception $e) {
                $conn->rollback();
                $response = ['success' => false, 'message' => 'Login failed. Please try again.'];
            }
        } else {
            $response['message'] = 'Driver not found.';
        }
    } catch (Exception $e) {
        $response['message'] = 'An error occurred: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'License number is required.';
}

header('Content-Type: application/json');
echo json_encode($response);
