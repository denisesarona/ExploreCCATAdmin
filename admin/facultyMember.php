<?php 
    session_start();
    include('includes/header.php');
?>

<link rel="stylesheet" href="assets/css/facultyMember.css"> 

<section class="dashboard">
    <div class="dash-content">
        <div class="overview">
            <div class="activity">
                <div class="title">
                    <i class='bx bx-group'></i>
                    <span class="text">FACULTY MEMBERS</span>
                </div>
    
                <div class="activity-data">
                    <div class="data id">
                        <span id="id" name="id" class="data-title">ID</span>
                    </div>
                    <div class="data name">
                        <span id="name" name="name" class="data-title">Name</span>
                    </div>
                    <div class="data details">
                        <span id="details" name="details" class="data-title">View Details</span>
                        <button class="btn" type="button" >VIEW DETAILS</button>
                    </div>
                    <div class="data delete">
                        <span id="delete" name="delete" class="data-title">Delete</span>
                        <button class="btn" type="button" >DELETE</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
