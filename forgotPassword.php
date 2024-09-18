<!--------------- INCLUDES ---------------> 
<?php 
    include('includes/header.php');
?>
<!--------------- CSS ---------------> 
<link rel="stylesheet" href="assets/css/login.css">    

<div class="back-btn-container">
    <a href="index.php" class="back-btn">
        <i class='bx bx-arrow-back'></i>
    </a>
</div>

<div class="wrapper">
    <form action="functions/authcode.php" method="POST">
        <h1  style="font-size: 28px">Forgot your password?</h1>
        <div class="word text-center">
            <p>Enter your Email Address</p>
        </div>
        <div class="input-box">
            <input type="text" class="form-control" placeholder="Enter email Address" name="email" required>
        </div>
        <button type="submit" name="forgotPass" class="btn">CONTINUE</button> 
    </form>
</div> 
