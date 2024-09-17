<?php
    include('../config/dbconnect.php');

    function getData($table){
        global $con;
        $query = "SELECT * FROM $table";
        return $query_run = mysqli_query($con,$query);
    }
?>