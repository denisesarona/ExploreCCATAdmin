<!--------------- INCLUDES --------------->
<?php 
    include('includes/header.php');
?>
<!--------------- CSS --------------->
<link rel="stylesheet" href="assets/css/EmailVerify.css">    

<!-- Back Button -->
<a href="#" class="back-btn">
        <i class='bx bx-arrow-back'></i>
    </a>

<section class="p-5 p-md-5 text-sm-start mt-4">
    <div class="Register mt-4 p-5" style="position: relative;">
        <div class="heading" style="margin-bottom: 1px; font-size: 38px">Verify Email</div>
            <form>
                <div class="EnterV" style="text-align: center;">
                    <p>Enter your code to verify.</p>
                </div>
                <div class="VerifyCode">
                    <input type="text" name="code" placeholder="Enter a code">
                </div>
                <div class="input-box row-md-4 mb-3">
                    <button type="submit" id="submitbtn" name="codeBtn" class="button-text">SUBMIT</button>
                </div>
            </form>
    </div>
</section>