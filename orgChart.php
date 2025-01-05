<?php
session_start();
include('includes/header.php');
include('config/dbconnect.php');

// Function to fetch faculty members by department
function getFacultyByDepartment($con, $dept_id) {
    $sql = "
        SELECT 
            dpf.faculty_dept_id AS id,  -- Use faculty_dept_id instead of faculty_id
            f.name, 
            f.img AS img, 
            p.position_name AS position, 
            dpf.dept_id AS department, 
            dpf.pid  -- Get the parent node ID from dept_pos_facultytb
        FROM 
            facultytb AS f
        INNER JOIN 
            dept_pos_facultytb AS dpf 
        ON 
            f.faculty_id = dpf.faculty_id
        INNER JOIN 
            positiontb AS p 
        ON 
            dpf.position_id = p.position_id
        WHERE 
            dpf.dept_id = ? 
        ORDER BY 
            dpf.pid ASC";
    
    $stmt = $con->prepare($sql);
    if ($stmt === false) {
        die("Error preparing statement: " . $con->error);
    }

    $stmt->bind_param("i", $dept_id);
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }

    $result = $stmt->get_result();
    $nodes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Adjust image path
            $row['img'] = 'uploads/' . $row['img']; 
            $nodes[] = $row;
        }
    }
    $stmt->close();
    return $nodes;
}


// Function to fetch department details by Name
function getDepartmentsByName($table, $name) {
    global $con;

    // Prepare the SQL statement
    $stmt = $con->prepare("SELECT * FROM $table WHERE name = ?");
    if ($stmt === false) {
        die("Error preparing statement: " . $con->error);
    }

    // Bind the parameters
    $stmt->bind_param("s", $name);

    // Execute the statement
    if (!$stmt->execute()) {
        die("Error executing statement: " . $stmt->error);
    }

    // Get the result
    $result = $stmt->get_result();

    // Close the statement
    $stmt->close();

    return $result;
}

// Initialize variables
$dept_name = "Department not found."; 
$nodes = [];

// Check if a department name is provided
if (isset($_GET['name'])) {
    $name = htmlspecialchars($_GET['name']); // Sanitize input

    // Fetch department details by name
    $dept = getDepartmentsByName('departmenttb', $name);

    if ($dept && mysqli_num_rows($dept) > 0) {
        $data = mysqli_fetch_array($dept);
        $dept_name = htmlspecialchars($data['name']);
        $dept_id = intval($data['dept_id']); // Get the department ID

        // Fetch faculty nodes based on department ID
        $nodes = getFacultyByDepartment($con, $dept_id);
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
OrgChart.templates.myTemplate.size = [400, 120];
OrgChart.templates.myTemplate.field_0 = 
    `<text style="font-size: 14px;" font-weight="bold" fill="#FFFFFFFF" x="100" y="60" text-anchor="right">{val}</text>`;

    OrgChart.templates.myTemplate.field_1 = 
    `<foreignObject x="100" y="65" width="290" height="50">
        <div xmlns="http://www.w3.org/1999/xhtml" style="font-size: 12px;line-height: 1; color: white; text-align: left; word-wrap: break-word; white-space: normal;">
            {val}
        </div>
    </foreignObject>`;

    
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
