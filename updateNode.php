<?php
session_start(); // Start the session
include('config/dbconnect.php'); // Include the database connection

header('Content-Type: application/json');

// Get the JSON data from the request
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['id']) && isset($data['pid'])) {
    $id = $data['id']; // The ID of the moved node
    $pid = $data['pid']; // The new parent ID

    // Update the database
    $stmt = $con->prepare("UPDATE facultytb SET pid = ? WHERE faculty_id = ?");
    $stmt->bind_param("ii", $pid, $id);

    if ($stmt->execute()) {
        $_SESSION['alert'] = ['type' => 'success', 'message' => 'Node updated successfully.']; // Set session message
        echo json_encode(['success' => true]);
    } else {
        $_SESSION['alert'] = ['type' => 'error', 'message' => 'Database update failed']; // Set session message for failure
        echo json_encode(['success' => false]);
    }

    $stmt->close();
} else {
    $_SESSION['alert'] = ['type' => 'error', 'message' => 'Invalid data']; // Set session message for invalid data
    echo json_encode(['success' => false]);
}

$con->close();
?>
