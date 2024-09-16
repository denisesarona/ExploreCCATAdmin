<!--------------- INCLUDES --------------->
<?php 
    session_start();
    include('includes/header.php');
?>
<!--------------- CSS --------------->
<link rel="stylesheet" href="assets/css/forgetPassword.css">    


<a href="login.php" class="back-btn">
        <i class='bx bx-arrow-back'></i>
    </a>

    <div class="wrapper">
        <form action="">
            <h1>Forgot your password</h1>
            <div class="word">
                <p>Enter your Email Address</p>
            </div>
            <div class="input-box">
                <input type="text" class="form-control" placeholder="Enter email Address" name="email" required>
            </div>
        
            <button type="submit" name="forgotPass" class="btn">CONTINUE</button> 
        </form>
</div>