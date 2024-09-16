<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "capstonedb";

// CREATE CONNECTION TO DATABASE
$con = mysqli_connect($servername, $username, $password, $dbname);

// CHECK DATABASE CONNECTION
if(!$con){
    die("Connection Failed ". mysqli_connect_error());
}


?>