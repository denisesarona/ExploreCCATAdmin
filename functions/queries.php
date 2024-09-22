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
?>