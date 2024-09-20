<?php
session_start(); // Start the session
include('includes/header.php'); // Include header or any other necessary files
include('config/dbconnect.php'); // Include the database connection

// Function to fetch all faculty members
function getFacultyNodes($con) {
    $sql = "SELECT faculty_id AS id, name, position AS title, img AS image, department, pid, pid_primary, pid_secondary, pid_tertiary FROM facultytb";
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

// Handle form submission to update PID
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pid'])) {
    $faculty_id = intval($_POST['faculty_id']);
    $new_pid = intval($_POST['new_pid']);
    $parent_type = $_POST['parent_type'];

    // Update the PID in the appropriate column based on parent type
    $column = '';
    switch ($parent_type) {
        case 'pid_primary':
            $column = 'pid_primary';
            break;
        case 'pid_secondary':
            $column = 'pid_secondary';
            break;
        case 'pid_tertiary':
            $column = 'pid_tertiary';
            break;
        default:
            $_SESSION['error'] = "Invalid parent type selected.";
            break;
    }

    if ($column) {
        $update_sql = "UPDATE facultytb SET $column = ?, pid = ? WHERE faculty_id = ?";
        $stmt = $con->prepare($update_sql);
        $stmt->bind_param("iii", $new_pid, $new_pid, $faculty_id); // Update both the specific parent ID and the general PID

        if ($stmt->execute()) {
            $_SESSION['success'] = "Parent ID updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating Parent ID.";
        }
        $stmt->close();
    }
}
?>
<link rel="stylesheet" href="assets/css/orgChart.css">
<div class="container">
    <div class="wrapper mt-5">
        <h3 class="text-center">Update Parent ID</h3>
        <form method="POST" action="">
            <div class="row">
                <div class="form-group col-md-6">
                    <label for="faculty_id">Select Faculty:</label>
                    <select name="faculty_id" class="form-control orgInput" id="faculty_id" required>
                        <?php foreach ($nodes as $node): ?>
                            <option value="<?php echo $node['id']; ?>"><?php echo $node['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="parent_type">Select Parent Type:</label>
                    <select name="parent_type" class="form-control orgInput" id="parent_type" required>
                        <option value="pid_primary">Primary Parent ID</option>
                        <option value="pid_secondary">Secondary Parent ID</option>
                        <option value="pid_tertiary">Tertiary Parent ID</option>
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label for="new_pid">New Parent ID:</label>
                    <input type="number" class="form-control orgInput" name="new_pid" id="new_pid" required>
                </div>
            </div>

            <button type="submit" class="btn btn-success btn-block" name="update_pid">Update PID</button>
        </form>
        <div id="tree" class="mt-4"></div>
    </div>
</div>

<script>
let nodes = <?php echo json_encode($nodes); ?>; // Convert PHP array to JSON

let chart = new OrgChart("#tree", {
    nodeBinding: {
        field_0: "name",
        field_1: "title",  
        img: "image",      
        field_2: "department" 
    },
    nodes: nodes,  // Use the data retrieved from the database
});
</script>

<?php include('includes/footer.php'); ?>
