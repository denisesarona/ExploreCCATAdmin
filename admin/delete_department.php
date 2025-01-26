<?php
include('../middleware/adminMiddleware.php'); // Session handling and admin access control
include('../functions/queries.php'); // Database connection and query functions

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deptId = isset($_POST['dept_id']) ? (int)$_POST['dept_id'] : 0;
    $facultyId = isset($_POST['faculty_id']) ? (int)$_POST['faculty_id'] : 0;

    if ($deptId > 0 && $facultyId > 0) {
        $query = "DELETE FROM dept_pos_facultytb WHERE faculty_id = ? AND dept_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('ii', $facultyId, $deptId);

        if ($stmt->execute()) {
            $_SESSION['success'] = 'Department removed successfully.';
            echo json_encode(['status' => 'success']); // Optional if used with AJAX
        } else {
            $_SESSION['error'] = 'Failed to remove department.';
            echo json_encode(['status' => 'error']); // Optional if used with AJAX
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = 'Invalid parameters.';
        echo json_encode(['status' => 'error']); // Optional if used with AJAX
    }
} else {
    $_SESSION['error'] = 'Invalid request method.';
    echo json_encode(['status' => 'error']); // Optional if used with AJAX
}
?>
