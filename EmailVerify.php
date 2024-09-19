<!--------------- INCLUDES ---------------> 
<?php 
    include('includes/header.php');
?>
<!--------------- CSS ---------------> 
<link rel="stylesheet" href="assets/css/login.css">    

<div class="back-btn-container">
    <a href="forgotPassword.php" class="back-btn">
        <i class='bx bx-arrow-back'></i>
    </a>
</div>

<div class="wrapper">
    <form action="functions/authcode.php" method="POST">
        <h1  style="font-size: 28px">Verify Email</h1>
        <div class="word text-center">
            <p>Enter your code to verify</p>
        </div>
        <div class="input-box">
            <input type="text" class="form-control" placeholder="Enter a Code" name="code" required>
        </div>
        <button type="submit" name="emailVerify_button" class="btn">SUBMIT</button> 
    </form>
</div> 
