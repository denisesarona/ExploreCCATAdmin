<!--------------- INCLUDES --------------->
<?php 
    session_start();
    include('includes/header.php');
?>

<link rel="stylesheet" href="assets/css/addAdmin.css">

<section class="dashboard">
        <div class="register-container">
            <div class="title">
                <i class='bx bx-plus-circle'></i>
                <span class="text">ADMIN</span>
            </div>
            <form class="AdminReg">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstn" class="form-label">Full Name</label>
                        <input type="text" name="adminName" class="form-control" id="adminName" placeholder="Enter admin name">
                    </div>
                    <div class="form-group">
                        <label for="em" class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" id="em" placeholder="Enter email address">
                    </div>
                </div>
        
                <div class="form-row">
                    <div class="form-group">
                        <label for="pass" class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" id="pass" placeholder="Enter password">
                        <div class="progress mt-2">
                            <div id="barCheck" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                        </div>
                        <p id="strength-message" class="mt-2"></p>
                    </div>
                    <div class="form-group">
                        <label for="type" class="form-label">Confirm Password</label>
                        <input type="password" name="confirm_password" class="form-control" id="type" placeholder="Confirm your password">
                        <p id="match-message" class="mt-2"></p>
                    </div>
                </div>
        
                <button type="submit" class="btn">SAVE</button>
            </form>
        </div>
</section>
