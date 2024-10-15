<?php
session_start(); // Start the session
include('includes/header.php'); // Include header or any other necessary files
include('config/dbconnect.php'); // Include the database connection

function getDepartmentsByID($table, $id) {
    global $con; // Use the existing database connection

    // Prepare the SQL statement
    $stmt = $con->prepare("SELECT * FROM $table WHERE dept_id = ?");
    
    // Check if preparation was successful
    if ($stmt === false) {
        die("Error preparing statement: " . $con->error);
    }

    // Bind the parameters
    $stmt->bind_param("i", $id); // Assuming dept_id is an integer

    // Execute the statement
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }

    // Get the result
    $result = $stmt->get_result();

    // Close the statement
    $stmt->close();

    return $result; // Return the result set
}

function getFacultyByDepartment($con, $dept_id) {
    // Prepare the SQL statement
    $sql = "SELECT faculty_id AS id, name, position AS position, img AS img, department, pid 
            FROM facultytb 
            WHERE dept_id = ? ORDER BY pid ASC"; // Assuming 'department' is the ID

    // Prepare the statement
    $stmt = $con->prepare($sql);
    
    // Check if preparation was successful
    if ($stmt === false) {
        die("Error preparing statement: " . $con->error);
    }
    
    // Bind the department ID
    $stmt->bind_param("i", $dept_id);
    
    // Execute the statement
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }
    
    // Get the result
    $result = $stmt->get_result();
    
    // Initialize an array for nodes
    $nodes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Adjust the image path if necessary
            $row['img'] = '../uploads/' . $row['img'];
            $nodes[] = $row;
        }
    }
    
    // Close the statement
    $stmt->close();
    
    return $nodes; // Return the array of nodes
}

// Initialize variables
$dept_name = "Department not found."; // Default value
$nodes = []; // Initialize nodes as an empty array

// Check if a department ID is provided
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Capture the ID from the URL

    // Fetch department details
    $dept = getDepartmentsByID('departmenttb', $id);
    
    if ($dept && mysqli_num_rows($dept) > 0) {
        $data = mysqli_fetch_array($dept);
        $dept_name = htmlspecialchars($data['name']);
        $dept_id = intval($data['dept_id']); // Assuming 'id' is the column for department ID

        // Fetch faculty nodes based on department ID
        $nodes = getFacultyByDepartment($con, $dept_id); // Pass the correct department ID
    }
}

?>

<link rel="stylesheet" href="assets/css/orgChart.css">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <br>
                <h3 class="text-center"><?php echo $dept_name; ?> Organizational Chart</h3>
                <br>
            </div>
            <div class="card-body">
                <div id="tree" class="mt-4" style="overflow-x: auto;"> <!-- Allow horizontal scrolling -->
                </div>
            </div>
        </div>   
    </div>
</div>

<script>
let nodes = <?php echo json_encode($nodes); ?>; // Convert PHP array to JSON

OrgChart.LINK_ROUNDED_CORNERS = 10;
// Define the template first
OrgChart.templates.myTemplate = OrgChart.templates.olivia;

// Then set the fields
OrgChart.templates.myTemplate.size = [350, 120];
OrgChart.templates.myTemplate.field_0 = 
    `<text style="font-size: 14px;" font-weight="bold" fill="#FFFFFFFF" x="100" y="60" text-anchor="right">{val}</text>`;

OrgChart.templates.myTemplate.field_1 = 
    `<text style="font-size: 12px;" fill="#FFFFFFFF" x="100" y="80" text-anchor="right">{val}</text>`;
    
OrgChart.templates.myTemplate.field_2 = 
    `<text style="font-size: 12px;" fill="#FFFFFFFF" x="280" y="20" text-anchor="right">Node ID {val}</text>`;

// OrgChart configuration
var chart = new OrgChart(document.getElementById("tree"), {
    template: "olivia",
    layout: OrgChart.tree,    
    enableDragDrop: false, // Disable drag-and-drop
    enableSearch: false,
    mouseScrool: OrgChart.none,
    align: OrgChart.ORIENTATION,
    scaleInitial: OrgChart.match.boundary,
    nodeMouseClick: OrgChart.action.edit,
    toolbar: {
        layout: false,
        zoom: true,
        fit: false,
        expandAll: false
    },
    nodeBinding: {
        field_0: "name",
        field_1: "position",
        img_0: "img",      
    },
    editForm: false,
    nodes: nodes  // Use the data retrieved from the database
});
</script>

<?php include('includes/footer.php'); ?>
