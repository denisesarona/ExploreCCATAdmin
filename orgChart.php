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

// Handle form submission to update PID
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_pid'])) {
    $faculty_id = intval($_POST['faculty_id']);
    $new_pid = intval($_POST['new_pid']);

    // Update the PID in the database
    $update_sql = "UPDATE facultytb SET pid = ? WHERE faculty_id = ?";
    $stmt = $con->prepare($update_sql);
    $stmt->bind_param("ii", $new_pid, $faculty_id);

    if ($stmt->execute()) {
        echo "<script>alert('Parent ID updated successfully!');</script>";
    } else {
        echo "<script>alert('Error updating Parent ID.');</script>";
    }
    $stmt->close();
}
?>

<div id="tree"></div>

<!-- Form to update PID -->
<div>
    <h3>Update Parent ID</h3>
    <form method="POST" action="">
        <label for="faculty_id">Select Faculty:</label>
        <select name="faculty_id" id="faculty_id" required>
            <?php foreach ($nodes as $node): ?>
                <option value="<?php echo $node['id']; ?>"><?php echo $node['name']; ?></option>
            <?php endforeach; ?>
        </select>

        <label for="new_pid">New Parent ID:</label>
        <input type="number" name="new_pid" id="new_pid" required>

        <button type="submit" name="update_pid">Update PID</button>
    </form>
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

    enableDragDrop: true // Enable drag-and-drop
});
</script>

<!--------------- FOOTER ---------------> 
<?php include('includes/footer.php'); ?>
