<?php
include('includes/header.php'); // Include header or any other necessary files
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');

// Fetch department details
$dept_name = "Department not found.";
$dept_id = 0;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Sanitize input
    $dept = getDepartmentsByID('departmenttb', $id);
    if ($dept && mysqli_num_rows($dept) > 0) {
        $data = mysqli_fetch_array($dept);
        $dept_name = htmlspecialchars($data['name']);
        $dept_id = intval($data['dept_id']);
    }
}
?>

<link rel="stylesheet" href="assets/css/orgChart.css">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3 class="text-center"><?php echo $dept_name; ?></h3>
            </div>
            <div class="card-body">
                <form id="updateForm" action="update_nodes.php" method="POST">
                    <div class="row mb-3"> 
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

let deptId = <?php echo json_encode($dept_id); ?>;
let chart; // Define the chart globally

// Fetch initial nodes and render the OrgChart
function fetchNodes() {
    fetch(`fetch_nodes.php?dept_id=${deptId}`)
        .then(response => response.json())
        .then(nodes => {
            console.log("Fetched nodes:", nodes);

            if (chart) {
                // Update the chart if it already exists
                chart.load(nodes);
            } else {
                // Initialize the chart for the first time
                chart = new OrgChart(document.getElementById("tree"), {
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
                        fit: false,
                        expandAll: false
                    },
                    nodeBinding: {
                        field_0: "name",
                        field_1: "position",
                        field_2: "id",  
                        img_0: "img",      
                    },
                    editForm: false,
                    nodes: nodes // Initial nodes
                });
            }
        })
        .catch(error => console.error("Error fetching nodes:", error));
}

// Handle form submission with AJAX
document.querySelector('#updateForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    const formData = new FormData(this);

    fetch(this.action, {
        method: "POST",
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log("Form submitted successfully:", data);
        fetchNodes(); // Refresh the OrgChart with updated data
    })
    .catch(error => console.error("Error during form submission:", error));
});

// Initial fetch
fetchNodes();
</script>

<?php include('includes/footer.php'); ?>
