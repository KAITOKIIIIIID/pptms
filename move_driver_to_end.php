<?php
// Assuming you have a database connection
require_once 'db_connection.php'; 

// Check if the 'id' is set and is a valid number
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $driverId = $_GET['id'];

    // Perform the logic to move the driver to the end of the queue
    // For example, updating a database field that marks the driver's position in the queue
    try {
        $sql = "UPDATE drivers SET queue_position = (SELECT MAX(queue_position) + 1 FROM drivers) WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$driverId]);

        // Check if the driver was updated
        if ($stmt->rowCount() > 0) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Driver not found or already at the end of the queue.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
} else {
    // Send an error if no valid id is provided
    echo json_encode(['success' => false, 'message' => 'Invalid driver ID.']);
}
?>
