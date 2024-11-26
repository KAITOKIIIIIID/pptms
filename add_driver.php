<?php
session_start();
include 'db.php'; // Connect to the database

// Initialize message
$message = '';

// Generate CSRF token if not set
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Process form when submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        die("Invalid CSRF token.");
    }

    // Sanitize and validate input fields
    $name = htmlspecialchars(trim($_POST['name']));
    $address = htmlspecialchars(trim($_POST['address']));
    $license_no = htmlspecialchars(trim($_POST['license_no']));
    $plate_no = htmlspecialchars(trim($_POST['plate_no']));
    $contact_info = htmlspecialchars(trim($_POST['contact_info']));

    if (empty($name) || empty($address) || empty($license_no) || empty($plate_no) || empty($contact_info)) {
        $message = '<div class="alert alert-danger">All fields are required.</div>';
    } elseif (strlen($name) > 50 || strlen($address) > 100) {
        $message = '<div class="alert alert-danger">Name or address exceeds allowed length.</div>';
    } elseif (!preg_match("/^[A-Z0-9]{1,15}$/", $license_no)) {
        $message = '<div class="alert alert-danger">Invalid license number format.</div>';
    } elseif (!preg_match("/^[A-Z0-9]{1,10}$/", $plate_no)) {
        $message = '<div class="alert alert-danger">Invalid plate number format.</div>';
    } else {
        // Check for duplicates
        $checkStmt = $conn->prepare("SELECT 1 FROM drivers WHERE license_no = ? OR plate_no = ?");
        $checkStmt->bind_param("ss", $license_no, $plate_no);
        $checkStmt->execute();

        if ($checkStmt->fetch()) {
            $message = '<div class="alert alert-danger">Driver with this license or plate number already exists.</div>';
            $checkStmt->close();
        } else {
            // Insert driver details
            $stmt = $conn->prepare("INSERT INTO drivers (name, address, license_no, plate_no, contact_info) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $address, $license_no, $plate_no, $contact_info);

            if ($stmt->execute()) {
                header("Location: add_driver.php?success=true");
                exit();
            } else {
                $message = '<div class="alert alert-danger">Failed to add driver. Please try again.</div>';
            }
            $stmt->close();
        }
    }
}

$conn->close();

// Display success message if redirected
if (isset($_GET['success']) && $_GET['success'] === 'true') {
    $message = '<div class="alert alert-success">Driver added successfully!</div>';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Driver - Toda Management System</title>
    <link rel= "stylesheet" href="style.css">
    <link rel="stylesheet" href="bootstrap5/css/bootstrap.min.css">

</head>
<body>
<header class="bg-primary text-white p-3">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <img src="icon.jpg" alt="System Icon" height="40">
            <h1 class="h4 mb-0">Toda Management System</h1>
        </div>
    </div>
</header>

<div class="container-fluid">
    <div class="row">
        <nav class="col-md-2 sidebar p-3">
            <h4>Admin Panel</h4>
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'admin_dashboard.php' ? 'active' : '' ?>" href="admin_dashboard.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'add_driver.php' ? 'active' : '' ?>" href="add_driver.php">Add Driver</a>
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
        <main class="col-md-10">
            <div class="container mt-5">
                <h2 class="text-center">Add Driver</h2>

                <div id="message"><?php echo $message; ?></div>

                <div class="col-md-6 mx-auto">
                    <form method="POST" action="add_driver.php" autocomplete="off">
                        <div class="form-group mb-3">
                            <label for="name">Driver's Name</label>
                            <input type="text" id="name" name="name" class="form-control" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="address">Driver's Address</label>
                            <input type="text" id="address" name="address" class="form-control" value="<?php echo isset($_POST['address']) ? $_POST['address'] : ''; ?>" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="license_no">License Number</label>
                            <input type="text" id="license_no" name="license_no" class="form-control" value="<?php echo isset($_POST['license_no']) ? $_POST['license_no'] : ''; ?>" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="plate_no">Plate Number</label>
                            <input type="text" id="plate_no" name="plate_no" class="form-control" value="<?php echo isset($_POST['plate_no']) ? $_POST['plate_no'] : ''; ?>" required>
                        </div>

                        <div class="form-group mb-3">
                            <label for="contact_info">Contact Info</label>
                            <input type="text" id="contact_info" name="contact_info" class="form-control" value="<?php echo isset($_POST['contact_info']) ? $_POST['contact_info'] : ''; ?>" required>
                        </div>

                        <button type="submit" class="btn btn-primary">Add Driver</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="bootstrap5/js/bootstrap.bundle.min.js"></script>
<script>
    setTimeout(() => {
        document.getElementById('message').style.display = 'none';
    }, 3000);

    document.querySelector('form').addEventListener('submit', function(event) {
        const name = document.getElementById('name').value;
        const licenseNo = document.getElementById('license_no').value;
        const plateNo = document.getElementById('plate_no').value;
        const address = document.getElementById('address').value;
        const contactInfo = document.getElementById('contact_info').value;

        if (!name || !licenseNo || !plateNo || !address || !contactInfo) {
            event.preventDefault();
            alert('Please fill in all fields.');
        }
    });
</script>
</body>
</html>
