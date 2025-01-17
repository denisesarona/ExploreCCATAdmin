<?php 
include('includes/header.php');
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');
?>
<link rel="stylesheet" href="assets/css/style.css">

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <!--------------- ADD ADMIN SECTION --------------->
            <div class="card mt-5">
                <h3>ADD ADMIN</h3>
                <div class="card-body">
                    <form action="codes.php" method="POST" enctype="multipart/form-data">
                        <div class="row" style="font-family: 'Poppins', sans-serif;">
                            <div class="col-md-6 mb-3"> 
                                <div class="form-group">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Admin Name" name="name" id="name" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3"> 
                                <div class="form-group">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="text" class="form-control" placeholder="Enter Email" name="email" required>
                                </div>
                            </div>
                            <div class="col-md-6"> 
                                <div class="form-group">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" placeholder="Enter Password" name="password" required id="pass" oninput="checkPasswordStrength()">
                                </div>
                                <div class="col-12 col-md-12">
                                    <div class="progress mt-1">
                                        <div id="barCheck" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                                    </div>
                                    <p id="strength-message" class="mt-1"></p>
                                </div>
                            </div>
                            <div class="col-md-6"> 
                                <div class="form-group">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" placeholder="Confirm your Password" name="confirm_password" required id="cpass" oninput="checkPasswordStrength()">
                                    <div class="col-12 col-md-12">
                                        <p id="match-message" class="mt-1"></p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6"> 
                                <div class="form-group">
                                    <label for="name" class="form-label">Role</label>
                                    <input type="text" class="form-control" placeholder="Admin" disabled required>
                                    <input type="hidden" name="role" value="1">
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn BlueBtn" name="addAdmin_button">Save</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!--------------- PASSWORD CHECKER SCRIPT --------------->
<script>
    function checkPasswordStrength() {
        var barCheck = document.getElementById('barCheck');
        var strengthMessage = document.getElementById('strength-message');
        var password = document.getElementById('pass').value;
        var confirm_password = document.getElementById('cpass').value;
        var strength = 0;
        var matchMessage = document.getElementById('match-message');

        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[$@#&!]/)) strength++;

        switch (strength) {
            case 0:
            case 1:
                barCheck.style.width = '30%';
                barCheck.style.backgroundColor = '#ff4d4d';
                strengthMessage.textContent = 'Weak';
                break;
            case 2:
                barCheck.style.width = '50%';
                barCheck.style.backgroundColor = '#ffa500';
                strengthMessage.textContent = 'Fair';
                break;
            case 3:
                barCheck.style.width = '70%';
                barCheck.style.backgroundColor = '#ffff00';
                strengthMessage.textContent = 'Good';
                break;
            case 4:
                barCheck.style.width = '100%';
                barCheck.style.backgroundColor = '#9acd32';
                strengthMessage.textContent = 'Strong';
                break;
        }

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