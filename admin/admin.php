<?php 
    session_start();
    include('includes/header.php');
?>
<link rel="stylesheet" href="assets/css/admin.css"> 

<section class="dashboard">
    <div class="dash-content">
        <div class="activity">
            <div class="title">
                <i class='bx bxs-user'></i>
                <span class="text">ADMIN</span>
            </div>

            <div class="activity-data">
                <div class="data names">
                    <span id="id" name="id" class="data-title">ID</span>
                </div>
                <div class="data email">
                    <span id="name" name="name" class="data-title">Name</span>
                </div>
                <div class="data joined">
                    <span id="status" name="status" class="data-title">Status</span>
                </div>
                <div class="data type">
                    <span id="delete" name="delete" class="data-title">Delete</span>
                </div>
            </div>
        </div>
    </div>
</section>
