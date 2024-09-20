<?php
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
?>

<div id="tree"></div>

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

    enableDragDrop: true,

    // Listen for node drop event
    onDrop: function(sender, dragNode, dropNode) {
        // Prepare the data to send to the server
        let data = {
            id: dragNode.id,
            pid: dropNode.id // The new parent ID
        };

        // Send the update to the server
        fetch('updateNode.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data) // Send data as JSON
        })
        .then(response => response.json())
        .then(result => {
            if (result.success) {
                console.log("Node updated successfully");
            } else {
                console.error("Failed to update node:", result.message);
            }
        })
        .catch(error => console.error('Error:', error));
    }
});
</script>
<!--------------- FOOTER --------------->
<?php include('includes/footer.php');?>
