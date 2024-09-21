<?php
session_start(); // Start the session
include('includes/header.php'); // Include header or any other necessary files
include('config/dbconnect.php'); // Include the database connection

// Function to fetch all faculty members
function getFacultyNodes($con) {
    $sql = "SELECT faculty_id AS id, name, position AS title, img AS image, department, pid FROM facultytb";
    $result = $con->query($sql); // Execute the query

    $nodes = []; // Initialize an array to hold the nodes
    if ($result->num_rows > 0) { // Check if there are results
        while ($row = $result->fetch_assoc()) { // Fetch each row as an associative array
            $nodes[] = $row; // Append the row to the nodes array
        }
    }

    return $nodes; // Return the array of nodes
}

// Fetch faculty nodes from the database
$nodes = getFacultyNodes($con);

// Handle saving the updated node positions in the backend
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['updated_nodes'])) {
    $updated_nodes = json_decode($_POST['updated_nodes'], true);

    // Check if decoding was successful and $updated_nodes is an array
    if (json_last_error() !== JSON_ERROR_NONE || !is_array($updated_nodes)) {
        $_SESSION['error'] = "Error processing updated node data.";
    } else {
        foreach ($updated_nodes as $node) {
            if (isset($node['id']) && isset($node['pid'])) {
                $node_id = intval($node['id']);
                $parent_id = intval($node['pid']);

                // Update each node's parent ID in the database
                $update_sql = "UPDATE facultytb SET pid = ? WHERE faculty_id = ?";
                $stmt = $con->prepare($update_sql);
                $stmt->bind_param("ii", $parent_id, $node_id);
                $stmt->execute();
                $stmt->close();
            }
        }

        $_SESSION['success'] = "Node positions updated successfully!";
    }
}

?>

<link rel="stylesheet" href="assets/css/orgChart.css">
<div class="container">
    <div class="wrapper mt-5">
        <h3 class="text-center">Faculty Organizational Chart</h3>
        <form method="POST" action="">
            <!-- Hidden input to store updated node data -->
            <input type="hidden" name="updated_nodes" id="updated_nodes">
            <button type="submit" class="btn btn-success btn-block" name="save_changes">Save Changes</button>
        </form>
        <div id="tree" class="mt-4"></div>
    </div>
</div>

<script>
let nodes = <?php echo json_encode($nodes); ?>; // Convert PHP array to JSON

var chart = new OrgChart(document.getElementById("tree"), {
    layout: OrgChart.tree,    
    enableDragDrop: true,
    mouseScrool: OrgChart.none,
    align: OrgChart.ORIENTATION,
    scaleInitial: OrgChart.match.boundary,
    nodeMouseClick: OrgChart.action.edit,
    nodeMenu: {
        details: { text: "Details" },
        edit: { text: "Edit" },
        add: { text: "Add" },
        remove: { text: "Remove" }
    },
    toolbar: {
        layout: true,
        zoom: true,
        fit: true,
        expandAll: true
    },
    nodeBinding: {
        field_0: "name",
        field_1: "title",  
        img: "image",      
        field_2: "department" 
    },
    nodes: nodes  // Use the data retrieved from the database
});

// Capture the updated positions after a drag-and-drop event
chart.on('drop', function (sender, draggedNodeId, droppedNodeId) {
    var draggedNode = sender.getNode(draggedNodeId);
    var droppedNode = sender.getNode(droppedNodeId);
    
    // Update the dragged node's parent ID
    draggedNode.pid = droppedNodeId; // Set the new parent ID
    sender.updateNode(draggedNode); // Update the node in the chart

    // Prepare the updated node structure for submission
    let updatedNodes = sender.getAllNodes().map(node => {
        return { id: node.id, pid: node.pid };
    });
    document.getElementById('updated_nodes').value = JSON.stringify(updatedNodes);
});

// Show layout options when the chart is initialized
chart.on('init', function (sender) {
    sender.toolbarUI.showLayout();
});
</script>

<?php include('includes/footer.php'); ?>
