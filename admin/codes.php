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
} else if(isset($_POST['editFaculty_button'])){
    $faculty_id = $_POST['faculty_id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $image = $_POST['image'];

    $new_image = $_FILES['image']['name']; // GET THE ORIGINAL NAME OF THE UPLOADED FILE 
    $old_image = $_POST['old_image'];

    if($new_image != ""){
        $image_ext = pathinfo($new_image, PATHINFO_EXTENSION); // GET THE FILE EXTENSION OF THE UPLOADED IMAGE 
        $update_filename = time().'.'.$image_ext; // GENERATE A UNIQUE FILENAME FOR THE UPLOADED IMAGE BY APPEDING THE CURRENT TIMESTAMP AND THE ORIGINAL FILE EXT
    } else{
        $update_filename = $old_image;
    }                                               

    $path = "../uploads";

    $update_query = "UPDATE product SET name='$name', size='$size', selling_price='$selling_price', quantity='$quantity',
    status='$status', image='$update_filename' WHERE id='$product_id' ";

    $update_query_run = mysqli_query($con, $update_query);

    if($update_query_run){
        if($_FILES['image']['name'] != ""){
            move_uploaded_file($_FILES['image']['tmp_name'], $path.'/'.$update_filename);
            if(file_exists("../uploads/".$old_image)){
                unlink("../uploads/".$old_image);
            }
        }
        $quantity_query = "SELECT quantity FROM product WHERE id='$product_id'";
        $quantity_query_run = mysqli_query($con, $quantity_query);

        $update_query_run = mysqli_query($con, $update_query);

        if ($update_query_run) {
            // Update the status if quantity is zero
            if ($quantity == 0) {
                $status_query = "UPDATE product SET status='0' WHERE id='$product_id'";
                $status_query_run = mysqli_query($con, $status_query);
            } else {
                // Otherwise, set status to 1
                $status_query = "UPDATE product SET status='1' WHERE id='$product_id'";
                $status_query_run = mysqli_query($con, $status_query);
            }
        
            if ($_FILES['image']['name'] != "") {
                move_uploaded_file($_FILES['image']['tmp_name'], $path.'/'.$update_filename);
                if (file_exists("../uploads/".$old_image)) {
                    unlink("../uploads/".$old_image);
                }
            }
            $_SESSION['success'] = "✔ Product updated successfully!";
            header("Location: product.php");
            exit();
        } else {
            $_SESSION['error'] = "Updating product failed!";
            header("Location: product.php");
            exit();
        }
    } else{
        $_SESSION['error'] = "Updating product failed!";
        header("Location: product.php");
        exit();
    }
}