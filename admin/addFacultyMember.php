<?php 
        include('includes/header.php');
        include('../functions/queries.php');
    ?>
    <link rel="stylesheet" href="assets/css/style.css">

    <!--------------- ADD FACULTY MEMBER PAGE --------------->
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card mt-5">
                    <div class="card-header d-flex justify-content-center align-items-center">
                        <h4 style="font-family: 'Poppins', sans-serif; font-size: 35px; color:#064918">ADD FACULTY MEMBERS</h4>
                    </div>
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
                                <div class="col-md-6 mb-3"> 
                                    <div class="form-group">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" class="form-control" placeholder="Enter Password" name="password" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3"> 
                                    <div class="form-group">
                                        <label for="confirm_password" class="form-label">Confirm Password</label>
                                        <input type="confirm_password" class="form-control" placeholder="Confirm your Password" name="confirm_password" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn BlueBtn mt-2" name="addAdmin_button">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>  
            </div>
        </div>
    </div><!--------------- FOOTER --------------->
    <?php include('includes/footer.php'); ?>
