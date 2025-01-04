<?php
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $node_id = intval($_POST['nodeId']);
    $parent_id = intval($_POST['pid']);

    $query = "UPDATE facultytb SET pid = ? WHERE faculty_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('ii', $parent_id, $node_id);

    if ($stmt->execute()) {
        echo "Node updated successfully!";
    } else {
        echo "Error updating node: " . $stmt->error;
    }

    $stmt->close();
}
?>
