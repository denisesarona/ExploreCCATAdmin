<?php
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');

if (isset($_GET['dept_id'])) {
    $dept_id = intval($_GET['dept_id']); // Sanitize input

    $query = "
        SELECT 
            f.faculty_id AS id, 
            f.name AS faculty_name, 
            f.img AS img, 
            p.position_name AS position_name, 
            f.pid AS pid
        FROM facultytb f
        LEFT JOIN dept_pos_facultytb dpf ON f.faculty_id = dpf.faculty_id
        LEFT JOIN positiontb p ON dpf.position_id = p.position_id
        WHERE dpf.dept_id = ?
    ";

    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $dept_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $nodes = [];
    while ($row = $result->fetch_assoc()) {
        $nodes[] = [
            'id' => $row['id'],
            'pid' => $row['pid'], // Include the updated parent ID
            'name' => $row['faculty_name'],
            'position' => $row['position_name'],
            'img' => !empty($row['img']) ? '../uploads/' . $row['img'] : '../uploads/default.jpg'
        ];
    }

    echo json_encode($nodes);
    exit;
}
?>
