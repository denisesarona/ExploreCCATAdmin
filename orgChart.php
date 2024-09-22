<?php
session_start(); // Start the session
include('includes/header.php'); // Include header or any other necessary files
include('config/dbconnect.php'); // Include the database connection

// Function to fetch all faculty members
function getFacultyNodes($con) {
    $sql = "SELECT faculty_id AS id, name, position AS title, img AS image, department, pid, ppid FROM facultytb"; // Include ppid
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
            if (isset($node['id']) && isset($node['pid']) && isset($node['ppid'])) {
                $node_id = intval($node['id']);
                $parent_id = intval($node['pid']);
                $partner_parent_id = intval($node['ppid']);

                // Prepare the SQL statement
                $update_sql = "UPDATE facultytb SET pid = ?, ppid = ? WHERE faculty_id = ?";
                $stmt = $con->prepare($update_sql);
                
                // Check if preparation was successful
                if ($stmt === false) {
                    $_SESSION['error'] = "Error preparing statement: " . $con->error;
                    continue; // Skip to the next node
                }
        
                $stmt->bind_param("iii", $parent_id, $partner_parent_id, $node_id);
                
                // Execute the statement
                if (!$stmt->execute()) {
                    $_SESSION['error'] = "Error executing statement: " . $stmt->error;
                }
        
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
            <div class="form-group">
                <label for="nodeId">Node ID:</label>
                <input type="text" class="form-control" id="nodeId" name="nodeId" required>
            </div>
            <div class="form-group">
                <label for="pid">Parent ID (pid):</label>
                <input type="text" class="form-control" id="pid" name="pid" required>
            </div>
            <div class="form-group">
                <label for="ppid">Partner Parent ID (ppid):</label>
                <input type="text" class="form-control" id="ppid" name="ppid" required>
            </div>
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
    enableDragDrop: false, // Disable drag-and-drop
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

// Function to gather updated node data before form submission
function gatherUpdatedNodeData() {
    let updatedNodes = [];
    
    // Get the node ID and new pid, ppid from the form
    let nodeId = document.getElementById('nodeId').value;
    let parentId = document.getElementById('pid').value;
    let partnerParentId = document.getElementById('ppid').value;

    updatedNodes.push({
        id: nodeId,
        pid: parentId,
        ppid: partnerParentId
    });

    document.getElementById('updated_nodes').value = JSON.stringify(updatedNodes);
}

// Add an event listener to the form submission
document.querySelector('form').addEventListener('submit', function(event) {
    gatherUpdatedNodeData(); // Gather updated data
});

</script>

<?php include('includes/footer.php'); ?>
