<!--------------- INCLUDES ---------------> 
<?php 
    include('includes/header.php');
    session_start();
?>
<!--------------- CSS ---------------> 
<link rel="stylesheet" href="assets/css/changepassword.css">    

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
        <div class="input-box row align-items-center">
            <div class="col-12 col-md-8">
                <input type="password" class="form-control" placeholder="Enter new password" name="new_password" required id="pass" oninput="checkPasswordStrength()">
            </div>
            <div class="col-12 col-md-4">
                <div class="progress mt-1">
                    <div id="barCheck" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                </div>
                <p id="strength-message" class="mt-1"></p>
            </div>
        </div>

        <div class="input-box row align-items-center">
            <div class="col-12 col-md-8">
                <input type="password" class="form-control" placeholder="Confirm password" name="confirm_password" required id="cpass" oninput="checkPasswordStrength()">
            </div>
            <div class="col-12 col-md-4">
                <p id="match-message" class="mt-2"></p>
            </div>
        </div>

        <button type="submit" name="changePassword_button" class="btn">SUBMIT</button> 
    </form>
</div> 
<!--------------- PASSWORD CHECKER --------------->
<script>
    function checkPasswordStrength() {
            var barCheck = document.getElementById('barCheck');
            var strengthMessage = document.getElementById('strength-message');
            var password = document.getElementById('pass').value;
            var confirm_password = document.getElementById('cpass').value;
            var strength = 0;
            var matchMessage = document.getElementById('match-message'); // Element to display the message

            // Password strength calculation
            if (password.length >= 8) {
                strength++;
            }
            if (password.match(/[a-z]/)) {
                strength++;
            }
            if (password.match(/[A-Z]/)) {
                strength++;
            }
            if (password.match(/[0-9]/)) {
                strength++;
            }
            if (password.match(/[$@#&!]/)) {
                strength++;
            }

            switch (strength) {
                case 0:
                case 1:
                    barCheck.style.width = '30%';
                    barCheck.style.backgroundColor = '#ff4d4d';
                    strengthMessage.textContent = 'Weak';
                    strengthMessage.style.color = '#ff4d4d';
                    break;
                case 2:
                    barCheck.style.width = '50%';
                    barCheck.style.backgroundColor = '#ffa500';
                    strengthMessage.textContent = 'Fair';
                    strengthMessage.style.color = '#ffa500';
                    break;
                case 3:
                    barCheck.style.width = '70%';
                    barCheck.style.backgroundColor = '#ffff00';
                    strengthMessage.textContent = 'Good';
                    strengthMessage.style.color = '#ffff00';
                    break;
                case 4:
                    barCheck.style.width = '100%';
                    barCheck.style.backgroundColor = '#9acd32';
                    strengthMessage.textContent = 'Strong';
                    strengthMessage.style.color = '#9acd32';
                    break;
            }

            // Password matching validation
            if (password === confirm_password && password !== '') {
                document.getElementById('cpass').style.borderColor = '#9acd32';
                matchMessage.textContent = 'Passwords match.';
                matchMessage.style.color = '#9acd32';
            } else if (password === '' && confirm_password === '') {
                document.getElementById('cpass').style.borderColor = '';
                matchMessage.textContent = '';
            } else {
                document.getElementById('cpass').style.borderColor = '#FA5B5BFF';
                matchMessage.textContent = 'Passwords do not match.';
                matchMessage.style.color = '#FA5B5BFF';
            }
        }
</script>
<!--------------- FOOTER --------------->
<?php include('includes/footer.php');?>
