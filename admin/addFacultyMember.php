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
                                        <input type="text" class="form-control" placeholder="Enter Faculty Members Name" name="name" id="name" required>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3"> 
                                    <div class="form-group">
                                        <label for="position" class="form-label">Position</label>
                                        <select class="form-control" name="position" id="position">
                                            <option value="">Select Position</option>
                                            <option value="Chairperson">Chairperson</option>
                                            <option value="Lecturer">Lecturer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3"> 
                                    <div class="form-group">
                                        <label for="department" class="form-label">Department</label>
                                        <select class="form-control" name="department" id="department">
                                            <option value="">Select Department</option>
                                            <option value="DCS">DCS</option>
                                            <option value="DMS">DMS</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3"> 
                                    <div class="form-group">
                                        <label for="image" class="form-label">Upload Image</label>
                                        <input type="file" class="form-control" name="img" id="img">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button type="submit" class="btn BlueBtn mt-2" name="addFaculty_button">Save</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>  
            </div>
        </div>
    </div><!--------------- FOOTER --------------->
    <?php include('includes/footer.php'); ?>
