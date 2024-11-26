<?php
session_start();
include 'db.php';

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = htmlspecialchars(trim($_POST['username']));
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user) {
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Set admin_logged_in for admins
            if ($user['role'] === 'admin') {
                $_SESSION['admin_logged_in'] = true;
                header("Location: admin_dashboard.php");
            } else {
                $_SESSION['admin_logged_in'] = false; // Optional for non-admin roles
                header("Location: driver_dashboard.php");
            }
            exit;
        } else {
            $error_message = "Invalid credentials. Please check your password.";
        }
    } else {
        $error_message = "Invalid credentials. User not found.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Toda Management System</title>
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
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <h2 class="text-center">Login</h2>
            <!-- Feedback for errors -->
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <!-- Login Form -->
            <form action="login.php" method="POST" autocomplete="off">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <input type="text" class="form-control" id="username" name="username" autocomplete="new-username" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" autocomplete="new-password" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <a href="index.html" class="btn btn-secondary mt-3 w-100">Back</a>
            </form>
        </div>
    </div>
</div>
<script src="js/bootstrap.bundle.min.js"></script>
</body>
</html>
