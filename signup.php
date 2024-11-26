<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = $_POST['role'];


    // Validate password confirmation
    if ($password !== $confirm_password) {
        echo "<div class='alert alert-danger'>Passwords do not match!</div>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Check if the username already exists
        $check_sql = "SELECT * FROM users WHERE username=?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $check_result = $stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            echo "<div class='alert alert-danger'>Username already exists!</div>";
        } else {
            // Insert the new user into the users table
            $sql1 = "INSERT INTO users (username, password, role) VALUES (?, ?, ?)";
            $insert_stmt1 = $conn->prepare($sql1);
            $insert_stmt1->bind_param("sss", $username, $hashed_password, $role);

            if ($insert_stmt1->execute()) {
                // Get the newly inserted user_id
       
            } else {
                echo "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
            }
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Para Po</title>
    <link href="assets/bootstrap5/css/bootstrap.min.css" rel="stylesheet">
 
</head>
<body>
<div class="navbar">
    <div class="navbar-brand"> BikeTrack
        <div class="icon">
            <h2  class="logo" ></h2>
        </div>
    </div>
        </div>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
            <h2 class="text-center">Signup Form</h2>
        <form action="signup.php" method="post" class="w-50 mx-auto">
            <!-- User Input Fields -->
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="form-group mt-3">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group mt-3">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group mt-3">
                <label for="role">Role</label>
                <select class="form-control" id="role" name="role">
                    <option value="admin">Admin</option>
                    <option value="driver">Driver</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-block mt-4">Sign Up</button>
            <p class="text-center mt-3">
                Already have an account? <a href="login.php">Back to login</a>
            </p>
        </form>
            </div>
            <div class="col-md-2"></div>
        </div>
    </div>
    <script src="assets/bootstrap5/js/bootstrap.bundle.min.js"></script>
</body>
</html>
