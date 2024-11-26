<?php
$host = 'localhost';      // Database host (usually localhost)
$dbname = 'pptms';   // Name of your database
$username = 'root';        // Database username
$password = '';            // Database password (leave blank if no password for local servers)

// Create a connection
$conn = new mysqli($host, $username, $password, $dbname);

// Check if connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
