<?php
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');

$response = array(); // Initialize the response array

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the nodeId (faculty_dept_id) and pid from the form
    $nodeId = intval($_POST['nodeId']);
    $pid = intval($_POST['pid']);
    $deptId = intval($_POST['dept_id']);

    // Update the node in the dept_pos_facultytb table
    $updateQuery = "
        UPDATE dept_pos_facultytb
        SET pid = ? 
        WHERE faculty_dept_id = ? AND dept_id = ?";

    $stmt = $con->prepare($updateQuery);
    if ($stmt === false) {
        // If statement preparation fails
        echo json_encode(["success" => false, "message" => "Error preparing statement: " . $con->error]);
        exit(); // Ensure no further code is executed after the error
    }

    $stmt->bind_param("iii", $pid, $nodeId, $deptId);
    if ($stmt->execute()) {
        // If execution is successful, return success
        echo json_encode(["success" => true]);
    } else {
        // If there's an error executing the query
        echo json_encode(["success" => false, "message" => $stmt->error]);
    }

    $stmt->close();
}
?>
