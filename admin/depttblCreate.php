<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capstonedb";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST['addDepartment_button'])) {
    $department = $_POST['dept_name'];

    // Prepare to insert the new department
    $dept_query = "INSERT INTO departmenttb(name) VALUES (?)";
    $stmt = $conn->prepare($dept_query);
    
    if ($stmt) {
        $stmt->bind_param("s", $department);

        if ($stmt->execute()) {
            $_SESSION['success'] = "✔ Department added successfully!";

            // Create the department-specific table
            $table_name = 'dept_' . preg_replace('/\s+/', '_', strtolower($department));
            $sql_create_dept_table = "CREATE TABLE `$table_name` (
                faculty_id INT(6) AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(200),
                position VARCHAR(200),
                dept_id INT(6),
                department VARCHAR(200),
                img VARCHAR(200),
                pid INT DEFAULT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
            )";

            // Debugging output
            echo "SQL for creating department table: $sql_create_dept_table<br>"; // Display the SQL query

            if ($conn->query($sql_create_dept_table) === TRUE) {
                $_SESSION['success'] .= " Table '$table_name' created successfully.";
            } else {
                $_SESSION['error'] = "Error creating table '$table_name': " . $conn->error;
                echo "Error creating table: " . $conn->error . "<br>"; // Debugging output
            }

            // Redirect to the department page
            header("Location: department.php");
            exit();
        } else {
            $_SESSION['error'] = "Adding Department failed: " . $stmt->error;
            header("Location: department.php");
            exit();
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error . "<br>";
    }
} else if (isset($_POST['deleteDepartment_button'])) {
    $dept_id = $_POST['dept_id']; 

    // Fetch department details to get the table name
    $dept_query = "SELECT * FROM departmenttb WHERE dept_id='$dept_id'";
    $dept_query_run = mysqli_query($conn, $dept_query);
    $dept_data = mysqli_fetch_array($dept_query_run);
    
    if ($dept_data) {
        // Get the department name and construct the table name
        $department_name = $dept_data['name'];
        $table_name = 'dept_' . preg_replace('/\s+/', '_', strtolower($department_name));
        
        // Delete the department from the departmenttb
        $delete_query = "DELETE FROM departmenttb WHERE dept_id='$dept_id'";
        $delete_query_run = mysqli_query($conn, $delete_query);

        // If department deletion was successful, delete the associated table
        if ($delete_query_run) {
            // Drop the department-specific table
            $drop_table_query = "DROP TABLE IF EXISTS `$table_name`";
            if (mysqli_query($conn, $drop_table_query)) {
                $_SESSION['success'] = "✔ Department and associated table deleted successfully!";
            } else {
                $_SESSION['error'] = "Department deleted, but failed to drop the table: " . mysqli_error($conn);
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

$conn->close();
?>
