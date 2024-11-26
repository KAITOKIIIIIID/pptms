<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Driver Dashboard - Toda Management System</title>
    <link rel="stylesheet" href="Bootstrap5/css/bootstrap.min.css">
    <style>
        /* Retain the original header size */
        header {
            padding: 15px 0;
        }

        /* Custom styling for the two sections */
        .container {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .login-container {
            width: 40%; /* Make the login form slightly smaller */
        }

        .queue-container {
            width: 55%; /* Make the queue view a little smaller */
            text-align: center;
        }

        .message-container {
            margin-top: 20px;
        }

        /* Adjust the table width to fit better on the page */
        .queue-container table {
            width: 100%;
            margin-top: 20px;
        }
    </style>
</head>
<body>
<!-- Header with System's Name and Icon -->
<header class="bg-primary text-white">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <img src="icon.jpg" alt="System Icon" height="40"> <!-- System Icon -->
            <h1 class="h4 mb-0">Toda Management System</h1>
        </div>
    </div>
</header>

<!-- Navigation Tab -->
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="#">Dashboard</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="maps_passenger.php">Route</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php" onclick="return confirmLogout()">Back</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container">
    <!-- Login View (Left Side) -->
    <div class="login-container">
        <h2>Driver Login/Logout</h2>
        <p>Enter your license number to log in or log out.</p>
        <form id="driverForm" autocomplete="off">
            <input type="text" id="licenseNumber" class="form-control" placeholder="Enter License Number" required>
            <div id="message" class="message-container"></div>
        </form>
    </div>

    <!-- Queue View (Center) -->
    <div class="queue-container">
        <h2>Queue View</h2>
        <p>Displaying the queued drivers...</p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Login Time</th>
                    <th>Max. Capacity</th>
                    <th>Plate No</th>
                </tr>
            </thead>
            <tbody id="queueTableBody">
                <!-- Dynamic data will be inserted here -->
            </tbody>
        </table>
    </div>
</div>

<!-- Notification for login -->
<div id="notification" class="alert alert-warning" style="display: none; position: fixed; top: 20px; left: 50%; transform: translateX(-50%); z-index: 1000;">
    <strong>Please login to continue!</strong>
</div>

<script>
// Idle timeout logic to switch views
let idleTime = 0;
const idleLimit = 3; // Idle time limit in seconds (adjust as needed)

// Increment idle time every second
const idleInterval = setInterval(() => {
    idleTime++;
    if (idleTime >= idleLimit) {
        fetchLoggedDrivers(); // Refresh the queue after idle timeout
    }
}, 1000);

// Reset idle timer on user interaction
function resetIdleTimer() {
    idleTime = 0;
}

// Add event listeners for user interactions
document.addEventListener("mousemove", resetIdleTimer);
document.addEventListener("keypress", resetIdleTimer);
document.addEventListener("click", resetIdleTimer);


// Function to fetch and display logged-in drivers
function fetchLoggedDrivers() {
    fetch('get_logged_drivers.php')
        .then(response => response.json())
        .then(drivers => {
            const queueTableBody = document.getElementById('queueTableBody');
            queueTableBody.innerHTML = ''; // Clear existing data

            if (drivers.length > 0) {
                drivers.forEach((driver, index) => {
                    const isFirstDriver = index === 0;
                    const row = `
                                <tr data-id="${driver.id}">
                                <td>${driver.name}</td>
                                <td>${driver.login_time}</td>
                                <td>${driver.max_cap}</td>
                                <td>${driver.plate_no}</td>
                                <td>
                                ${isFirstDriver ? `<button class="btn btn-warning btn-sm" onclick="moveDriverToEnd(${driver.id})">Full</button>` : ''}
                                </td>
                                </tr>
                                `;
                                queueTableBody.innerHTML += row;

                });
            } else {
                queueTableBody.innerHTML = '<tr><td colspan="5" class="text-center">No logged-in drivers.</td></tr>';
            }
        })
        .catch(error => console.error('Error fetching drivers:', error));
}

function moveDriverToEnd(driverId) {
    fetch(`move_driver_to_end.php?id=${driverId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                fetchLoggedDrivers(); // Refresh the queue after moving the driver
            } else {
                alert(data.message); // Handle any error messages
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred. Please try again later.');
        });
}



document.getElementById('driverForm').addEventListener('submit', function (e) {
    e.preventDefault(); // Prevent form submission

    const licenseNumber = document.getElementById('licenseNumber').value.trim();
    const messageDiv = document.getElementById('message');
    messageDiv.innerHTML = ''; // Clear any previous messages

    if (licenseNumber === '') {
        messageDiv.innerHTML = '<div class="alert alert-danger">License number is required.</div>';

        setTimeout(() => {
            messageDiv.innerHTML = '';
        }, 3000);
        return;
    }

    // Send a request to the backend
    fetch(`driver_login_logout.php?license=${licenseNumber}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageDiv.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                document.getElementById('licenseNumber').value = ''; // Clear input field
            } else {
                messageDiv.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
                document.getElementById('licenseNumber').value = '';
            }

            setTimeout(() => {
                messageDiv.innerHTML = '';
            }, 3000);

            // Focus on the license number input field after the error
            document.getElementById('licenseNumber').focus();

            // Optionally refresh the queue view
            fetchLoggedDrivers();
        })
        .catch(error => {
            console.error('Error:', error);
            messageDiv.innerHTML = '<div class="alert alert-danger">An error occurred. Please try again later.</div>';
        });
});

// Focus on the license number input field after the request
document.getElementById('licenseNumber').focus();

// Update the queue every 5 seconds
setInterval(fetchLoggedDrivers, 5000);
</script>

<script src="bootstrap5/js/bootstrap.bundle.min.js"></script>
</body>
</html>
