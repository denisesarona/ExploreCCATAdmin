<!--------------- INCLUDES ---------------> 
<?php 
    include('includes/header.php');
    session_start();
?>
<!--------------- CSS ---------------> 
<link rel="stylesheet" href="assets/css/login.css">    

<div class="back-btn-container">
    <a href="emailVerify.php" class="back-btn">
        <i class='bx bx-arrow-back'></i>
    </a>
</div>

<div class="wrapper">
    <form action="functions/authcode.php" method="POST">
        <h1  style="font-size: 28px">Change Password</h1>
        <div class="word text-center">
            <p>Enter new password</p>
        </div>
        <div class="input-box">
            <input type="password" class="form-control" placeholder="Enter new password" name="new_password" required>
        </div>
        <div class="input-box">
            <input type="password" class="form-control" placeholder="Confirm password" name="confirm_password" required>
        </div>
        <button type="submit" name="changePassword_button" class="btn">SUBMIT</button> 
    </form>
</div> 
<!--------------- FOOTER --------------->
<?php include('includes/footer.php');?>
