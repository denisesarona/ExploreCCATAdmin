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
?>