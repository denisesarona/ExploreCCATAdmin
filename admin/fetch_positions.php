<?php
include('../functions/queries.php');

if (isset($_GET['dept_id'])) {
    $dept_id = (int)$_GET['dept_id'];
    
    // Fetch positions for the selected department
    $positionsQuery = "SELECT * FROM positiontb WHERE dept_id = $dept_id";
    $positionsResult = mysqli_query($con, $positionsQuery);

    $positions = [];
    while ($row = mysqli_fetch_assoc($positionsResult)) {
        $positions[] = $row;
    }

    // Return positions as a JSON response
    echo json_encode($positions);
}
?>
