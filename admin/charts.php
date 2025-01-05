<?php
include('includes/header.php'); // Include header or any other necessary files
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');

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
            dpf.pid DESC";
    
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
            $row['img'] = '../uploads/' . $row['img']; 
            $nodes[] = $row;
        }
    }
    $stmt->close();
    return $nodes;
}


// Fetch department details
$dept_name = "Department not found.";
$dept_id = 0;
$nodes = [];
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize input
    $dept = getDepartmentsByID('departmenttb', $id);
    if ($dept && mysqli_num_rows($dept) > 0) {
        $data = mysqli_fetch_array($dept);
        $dept_name = htmlspecialchars($data['name']);
        $dept_id = intval($data['dept_id']);

        // Fetch faculty members for this department
        $nodes = getFacultyByDepartment($con, $dept_id); // Pass $con here
    }
}
?>

<link rel="stylesheet" href="assets/css/orgChart.css">
<div class="container">
    <!-- Custom Modal Alert -->
<div id="customAlert" class="custom-alert">
    <div class="alert-content">
        <div class="alert-header">
            <h4>Success</h4>
        </div>
        <div class="alert-body">
            <p id="alertMessage">Node updated successfully!</p>
        </div>
        <div class="alert-footer">
            <button id="alertOkBtn">OK</button>
        </div>
    </div>
</div>

    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3 class="text-center"><?php echo $dept_name; ?></h3>
            </div>
            <div class="card-body">
                <form id="updateForm" action="update_nodes.php" method="POST">
                    <div class="row mb-3"> 
                        <!-- In the form where you input the nodeId and pid -->
                        <div class="col-md-6 mt-4"> 
                            <div class="form-group">
                                <label for="nodeId">Node ID:</label>
                                <input type="text" class="form-control" id="nodeId" name="nodeId" required>
                            </div>
                        </div>
                        <div class="col-md-6 mt-4"> 
                            <div class="form-group">
                                <label for="pid">Parent ID (Node it is connected to):</label>
                                <input type="text" class="form-control" id="pid" name="pid" required>
                            </div>
                            <input type="hidden" name="dept_id" value="<?php echo $dept_id; ?>">
                        </div>

                        <div class="col-md-2 d-flex align-items-end mt-3"> 
                            <div class="form-group w-100">
                                <button type="submit" class="btn btn-success btn-block" id="saveChanges">Save Changes</button>
                            </div>
                        </div>
                    </div>
                </form>
                <div id="tree" class="mt-4" style="overflow-x: auto;"> <!-- Allow horizontal scrolling -->
                </div>
            </div>
        </div>   
    </div>
</div>

<script>
document.getElementById("updateForm").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent the default form submission

    // Create a FormData object to easily collect the form data
    let formData = new FormData(this);

    // Use Fetch API to submit the form via AJAX
    fetch("update_nodes.php", {
        method: "POST",
        body: formData
    })
    .then(response => response.json()) // Assuming the PHP script returns a JSON response
    .then(data => {
        // If the response is successful
        if (data.success) {
            // Show custom modal with success message
            showCustomAlert("Node updated successfully!");
        } else {
            // Show custom modal with error message
            showCustomAlert("Error updating node: " + data.message);
        }
    })
    .catch(error => {
        showCustomAlert("An error occurred: " + error);
    });
});

// Function to show the custom alert
function showCustomAlert(message) {
    // Set the message in the alert
    document.getElementById("alertMessage").innerText = message;

    // Show the modal
    document.getElementById("customAlert").style.display = "flex";

    // When the "OK" button is clicked, refresh the page
    document.getElementById("alertOkBtn").addEventListener("click", function() {
        document.getElementById("customAlert").style.display = "none"; // Hide the modal
        location.reload(); // Refresh the page
    });
}



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

// Initialize the OrgChart
let chart = new OrgChart(document.getElementById("tree"), {
    template: "olivia",
    layout: OrgChart.tree,    
    enableDragDrop: false,
    enableSearch: false,
    mouseScrool: OrgChart.none,
    align: OrgChart.ORIENTATION,
    scaleInitial: OrgChart.match.boundary,
    nodeMouseClick: OrgChart.action.edit,
    toolbar: {
        layout: false,
        zoom: true,
        fit: true,
        expandAll: false
    },
    nodeBinding: {
        field_0: "name",
        field_1: "position",
        field_2: "id",  
        img_0: "img",      
    },
    editForm: false,
    nodes: <?php echo json_encode($nodes); ?> // Load nodes statically from PHP
});
</script>

<?php include('includes/footer.php'); ?>
