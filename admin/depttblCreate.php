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
}


$conn->close();
?>