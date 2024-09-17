<?php
session_start();
include('../config/dbconnect.php');
include('../functions/queries.php');

if(isset($_POST['deleteUser_button'])){
    $user_id = $_POST['user_id']; // Adjusted to 'customer_id' as per the form input name

    // Fetch user data (optional, for logging or additional operations)
    $user_query = "SELECT * FROM users WHERE user_id='$user_id'";
    $user_query_run = mysqli_query($con, $user_query);
    $user_data = mysqli_fetch_array($user_query_run);

    // Delete the user
    $delete_query = "DELETE FROM users WHERE user_id='$user_id'";
    $delete_query_run = mysqli_query($con, $delete_query);

    if($delete_query_run){
        $_SESSION['success'] = "✔ Admin deleted successfully!";
        header("Location: admin.php");
        exit();
    } else {
        $_SESSION['error'] = "Deleting admin failed!";
        header("Location: admin.php");
        exit();
    }
}