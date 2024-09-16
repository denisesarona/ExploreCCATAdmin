<!--------------- INCLUDES --------------->
<?php 
    session_start();
    include('includes/header.php');
?>
<!--------------- CSS --------------->
<link rel="stylesheet" href="assets/css/login.css">    

<div class="wrapper">
    <form action="">
        <h1>ADMIN LOGIN</h1>
            <div class="input-box">
                <input type="text" placeholder="Username" 
                required>
                <i class='bx bxs-user'></i>
            </div>
            <div class="input-box">
                <input type="password" 
                placeholder="Password" required>
                <i class='bx bxs-lock-alt' ></i>
            </div>

            <div class="remember-forgot">
                <label><input type="checkbox">Remember me</label>
                <a href="changePassword.php">Forgot password?</a>
            </div>

            <button type="submit" class="btn">LOGIN</button>
                
            <div class="register-link">
                <p>Don't have an account? <a href="">Register</a></p>
            </div>  
        </form>
</div>