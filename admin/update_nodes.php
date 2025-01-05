<?php
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');

$response = array(); // Initialize the response array

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if required POST fields are set
    if (isset($_POST['nodeId']) && isset($_POST['pid'])) {
        $node_id = intval($_POST['nodeId']);
        $parent_id = intval($_POST['pid']);

        // Debugging output
        error_log("Node ID: " . $node_id); // Log to PHP error log
        error_log("Parent ID: " . $parent_id); // Log to PHP error log

        $query = "UPDATE facultytb SET pid = ? WHERE faculty_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('ii', $parent_id, $node_id);

        if ($stmt->execute()) {
            // If the update is successful, send success response
            $response['success'] = true;
            $response['message'] = "Node updated successfully!";
        } else {
            // If there's an error, send error response
            $response['success'] = false;
            $response['message'] = "Error updating node: " . $stmt->error;
        }

        $stmt->close();
    } else {
        $response['success'] = false;
        $response['message'] = "Missing parameters.";
    }
} else {
    $response['success'] = false;
    $response['message'] = "Invalid request method.";
}

// Return the response as JSON
echo json_encode($response);
?>
