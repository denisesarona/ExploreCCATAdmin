<?php
session_start(); // Start the session
include('includes/header.php'); // Include header or any other necessary files
include('config/dbconnect.php'); // Include the database connection

// Function to fetch all faculty members
function getFacultyNodes($con) {
    $sql = "SELECT faculty_id AS id, name, position AS position, img AS img, department, pid FROM facultytb"; 
    $result = $con->query($sql);

    $nodes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Adjust the image path if necessary
            $row['img'] = 'uploads/' . $row['img']; // Ensure this is correct
            $nodes[] = $row;
        }
    }

    return $nodes;
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
            if (isset($node['id']) && isset($node['pid']) && isset($node['pid'])) {
                $node_id = intval($node['id']);
                $parent_id = intval($node['pid']);

                // Prepare the SQL statement
                $update_sql = "UPDATE facultytb SET pid = ? WHERE faculty_id = ?";
                $stmt = $con->prepare($update_sql);
                
                // Check if preparation was successful
                if ($stmt === false) {
                    $_SESSION['error'] = "Error preparing statement: " . $con->error;
                    continue; // Skip to the next node
                }
        
                $stmt->bind_param("ii", $parent_id, $node_id);
                
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
        <form method="POST" action="" class="row">
            <div class="form-group col-12 col-md-6 mb-3">
                <label for="nodeId">Node ID:</label>
                <input type="text" class="form-control" id="nodeId" name="nodeId" required>
            </div>
            <div class="form-group col-12 col-md-6 mb-3">
                <label for="pid">Parent ID (pid):</label>
                <input type="text" class="form-control" id="pid" name="pid" required>
            </div>
            <input type="hidden" name="updated_nodes" id="updated_nodes">
            <div class="col-12">
                <button type="submit" class="btn btn-success btn-block" name="save_changes">Save Changes</button>
            </div>
        </form>
        <div id="tree" class="mt-4"></div>
    </div>
</div>

<script>
let nodes = <?php echo json_encode($nodes); ?>; // Convert PHP array to JSON

OrgChart.LINK_ROUNDED_CORNERS = 10;

var chart = new OrgChart(document.getElementById("tree"), {
    template: "olivia",
    layout: OrgChart.tree,    
    enableDragDrop: false, // Disable drag-and-drop
    enableSearch: false,
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
        field_1: "position",  
        img_0: "img",      
    },
    nodes: nodes  // Use the data retrieved from the database
});

// Function to gather updated node data before form submission
function gatherUpdatedNodeData() {
    let updatedNodes = [];
    
    // Get the node ID and new pid, ppid from the form
    let nodeId = document.getElementById('nodeId').value;
    let parentId = document.getElementById('pid').value;

    updatedNodes.push({
        id: nodeId,
        pid: parentId
    });

    document.getElementById('updated_nodes').value = JSON.stringify(updatedNodes);
}

// Add an event listener to the form submission
document.querySelector('form').addEventListener('submit', function(event) {
    gatherUpdatedNodeData(); // Gather updated data
});

</script>

<?php include('includes/footer.php'); ?>
