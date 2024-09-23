<?php
session_start();
include('../config/dbconnect.php');

if (isset($_POST['addDepartment_button'])) {
    $department = $_POST['dept_name'];

    // Prepare to insert the new department
    $dept_query = "INSERT INTO departmenttb(name) VALUES (?)";
    $stmt = $con->prepare($dept_query);
    
    if ($stmt) {
        $stmt->bind_param("s", $department);

        if ($stmt->execute()) {
            $_SESSION['success'] = "✔ Department added successfully!";

            // Create the department-specific table
            $table_name = 'dept_' . preg_replace('/\s+/', '_', strtolower($department));
            $sql_create_dept_table = "CREATE TABLE `$table_name` (
                dept_faculty_id INT(6) AUTO_INCREMENT PRIMARY KEY,
                faculty_id INT(6) NOT NULL,
                name VARCHAR(200) NOT NULL,
                position VARCHAR(200) NOT NULL,
                dept_id INT(6) NOT NULL,
                department VARCHAR(200) NOT NULL,
                img VARCHAR(200) NOT NULL,
                pid INT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";


            if ($con->query($sql_create_dept_table) === TRUE) {
                $_SESSION['success'] = "✔ Department added successfully!"; 
                header("Location: department.php");// Corrected to use .=
            } else {
                $_SESSION['error'] = "Error creating table '$table_name': " . $con->error;
                header("Location: department.php");
            }

            exit();
        } else {
            $_SESSION['error'] = "Adding Department failed: " . $stmt->error;
            header("Location: department.php");
            exit();
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error . "<br>";
    }
} else if (isset($_POST['deleteDepartment_button'])) {
    $dept_id = $_POST['dept_id']; 

    // Fetch department details to get the table name
    $dept_query = "SELECT * FROM departmenttb WHERE dept_id='$dept_id'";
    $dept_query_run = mysqli_query($con, $dept_query);
    $dept_data = mysqli_fetch_array($dept_query_run);
    
    if ($dept_data) {
        // Get the department name and construct the table name
        $department_name = $dept_data['name'];
        $table_name = 'dept_' . preg_replace('/\s+/', '_', strtolower($department_name));
        
        // Delete the department from the departmenttb
        $delete_query = "DELETE FROM departmenttb WHERE dept_id='$dept_id'";
        $delete_query_run = mysqli_query($con, $delete_query);

        // If department deletion was successful, delete the associated table
        if ($delete_query_run) {
            // Drop the department-specific table
            $drop_table_query = "DROP TABLE IF EXISTS `$table_name`";
            if (mysqli_query($con, $drop_table_query)) {
                $_SESSION['success'] = "✔ Department deleted successfully!";
            } else {
                $_SESSION['error'] = "Department deleted, but failed to drop the table: " . mysqli_error($con);
            }
        } else {
            $_SESSION['error'] = "Deleting department failed!";
        }
    } else {
        $_SESSION['error'] = "Department not found!";
    }

    // Redirect to the department page
    header("Location: department.php");
    exit();
}

$con->close();
?>
