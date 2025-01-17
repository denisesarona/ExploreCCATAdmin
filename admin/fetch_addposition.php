<?php
include('../functions/queries.php');

// Check if department ID is passed
if (isset($_GET['dept_id'])) {
    $deptId = (int)$_GET['dept_id'];

    // Fetch positions for the selected department
    $positionsQuery = "SELECT * FROM positiontb WHERE dept_id = $deptId";
    $result = mysqli_query($con, $positionsQuery);

    if ($result) {
        $positions = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $positions[] = [
                'position_id' => $row['position_id'],
                'position_name' => $row['position_name']
            ];
        }
        echo json_encode(['positions' => $positions]);
    } else {
        echo json_encode(['positions' => []]);
    }
}
?>
