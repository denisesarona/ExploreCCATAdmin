<?php
    include('../config/dbconnect.php');

    /*--------------- GET ALL DATA FROM TABLE ---------------*/
    function getData($table){
        global $con;
        $query = "SELECT * FROM $table";
        return $query_run = mysqli_query($con,$query);
    }

    /*--------------- GET ALL DATA FROM TABLE BY ID ---------------*/
    function getFacultyByID($table, $id) {
        global $con;
        $stmt = $con->prepare("SELECT * FROM $table WHERE faculty_id = ?"); // Use 'faculty_id'
        $stmt->bind_param("i", $id); // Assuming faculty_id is an integer
        $stmt->execute();
        return $stmt->get_result();
    }

    function getDataFromQuery($query) {
        global $con; // Your database connection variable

        // Execute the query
        $result = mysqli_query($con, $query);
        
        // Check if query was successful
        if (!$result) {
            die("Query Failed: " . mysqli_error($con));
        }
        
        return $result; // Return mysqli_result object
    }

    // Example function for fetching data from a table
    function getDatas($table) {
        global $con;
        $query = "SELECT * FROM $table";
        return getDataFromQuery($query);
    }
    /*--------------- COUNT ALL DATA IN A TABLE ---------------*/
    function countItem($con, $table) {
        $query = "SELECT COUNT(*) AS total_item FROM $table";
        $result = $con->query($query);
    
        $totalItem = 0;
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $totalItem = $row['total_item'];
        }
        return $totalItem;
    }

    function getFacultyNodes($con) {
        $sql = "SELECT faculty_id AS id, name, position AS position, img AS img, department, pid FROM facultytb"; 
        $result = $con->query($sql);
    
        $nodes = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Adjust the image path if necessary
                $row['img'] = 'uploads/' . $row['img']; // Ensure this is correct
                $nodes[] = $row;
            }
        }
    
        return $nodes;
    }
    
    function getDepartments($con) {
        $sql = "SELECT DISTINCT department AS name FROM facultytb";
        $result = $con->query($sql);
    
        $departments = [];
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $departments[] = $row;
            }
        }
        return $departments;
    }

    function getDepartmentsByID($table, $id) {
        global $con; // Use the existing database connection
    
        // Prepare the SQL statement
        $stmt = $con->prepare("SELECT * FROM $table WHERE dept_id = ?");
        
        // Check if preparation was successful
        if ($stmt === false) {
            die("Error preparing statement: " . $con->error);
        }
    
        // Bind the parameters
        $stmt->bind_param("i", $id); // Assuming dept_id is an integer
    
        // Execute the statement
        if (!$stmt->execute()) {
            die("Error executing statement: " . $stmt->error);
        }
    
        // Get the result
        $result = $stmt->get_result();
    
        // Close the statement
        $stmt->close();
    
        return $result; // Return the result set
    }
    
    if(isset($_POST['addDepartment_button'])){
        $department = $_POST['dept_name'];
    
        $dept_query = "INSERT INTO departmenttb(name) VALUES ('$department')";
    
        $dept_query_run = mysqli_query($con, $dept_query);
    
        if($dept_query_run){
            $_SESSION['success'] = "✔ Department added successfully!";
            header("Location: department.php");
            exit();
        } else {
            $_SESSION['error'] = "Adding Department failed!";
            header("Location: department.php");
            exit();
        }

        
    }
    /*--------------- GET ALL DATA FROM TABLE BY ID ---------------*/
    function getBldgByID($table, $id) {
        global $con;
        $stmt = $con->prepare("SELECT * FROM $table WHERE building_id = ?"); 
        $stmt->bind_param("i", $id); 
        $stmt->execute();
        return $stmt->get_result();
    }
        
    /*--------------- GET ALL DATA FROM TABLE BY ID ---------------*/
    function getPolByID($table, $id) {
        global $con;
        $stmt = $con->prepare("SELECT * FROM $table WHERE pol_id = ?"); 
        $stmt->bind_param("i", $id); 
        $stmt->execute();
        return $stmt->get_result();
    }

?>