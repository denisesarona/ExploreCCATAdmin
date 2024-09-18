<!--------------- INCLUDES --------------->
<?php 
    include('includes/header.php');
?>
<!--------------- CSS --------------->
<link rel="stylesheet" href="assets/css/changePassword.css">    


<!-- Back Button -->
<a href="#" class="back-btn">
        <i class='bx bx-arrow-back'></i>
    </a>

    <section class="p-5 text-sm-start mt-0">
        <div class="Register mt-4">
            <div class="heading mt-4" style="margin-bottom: 0px; font-size: 30px">Change Password</div>
            <div class="EnterV" style="text-align: center;">
                <p>Enter your code to verify.</p>
            </div>
                <div class="profile-card text-dark">
                    <form class="regform" method="...">
                        <div class="col mt-1 text-center">
                            <div class="input-box row-md-4 mb-3">
                                <input class="Newpass" type="password" id="pass" placeholder="Enter new password" name="password" >
                            </div>
                            <div class="progress input-box row-md-4 mb-3">
                                <div id="barCheck" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                            </div>
                            <div class="input-box row-md-4 mb-3">
                                <input class="Newpass" type="password" placeholder="Confirm new password" id="cpass" name="confirm_password">
                            </div>
                            <div class="input-box row-md-4 mb-3">
                            </div>
                            <div class="input-box row-md-4 mb-3">
                                <button type="submit" id="submitbtn" name="passwordUpdateBtn" class="button-text">SUBMIT</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
    </section>