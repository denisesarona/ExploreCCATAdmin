<?php 
include('includes/header.php');
include('../functions/queries.php');
?>

<!--------------- VIEW AND EDIT FACULTY MEMBERS DETAILS PAGE --------------->

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            if (isset($_GET['id'])) {
                $id = $_GET['id']; // Capture the ID from the URL
                $facultymember = getFacultyByID('facultytb', $id); // Fetch record from database

                if (mysqli_num_rows($facultymember) > 0) {
                    $data = mysqli_fetch_array($facultymember);
            ?>  
                    <div class="card mt-5">
                        <div class="card-header d-flex justify-content-center align-items-center">
                            <h4 style="font-family: 'Poppins', sans-serif; font-size: 32px; color:#064918">FACULTY MEMBERS DETAILS</h4>
                        </div>
                        <div class="card-body">
                            <!--------------- FORM --------------->

                            <form action="codes.php" method="POST" enctype="multipart/form-data">
                                <div class="row" style="font-family: 'Poppins', sans-serif;">
                                    <div class="col-md-6 mb-3"> 
                                        <div class="form-group">
                                            <input type="hidden" name="faculty_id" value="<?=$data['faculty_id']; ?>"> <!-- Use the correct column name -->
                                            <label for="">Name</label>
                                            <input type="text" value="<?=$data['name']; ?>" class="form-control" placeholder="Enter Name" name="name" id="name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3"> 
                                        <div class="form-group">
                                            <label for="">Position</label>
                                            <select class="form-control" name="position" id="position">
                                                <option value="">Select Position</option>
                                                <option value="Chairperson">Chairperson</option>
                                                <option value="Professor">Professor</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <label for="">Department</label>
                                            <select class="form-control" name="position" id="position">
                                                <option value="">Select Department</option>
                                                <option value="DCS">DCS</option>
                                                <option value="DMS">DMS</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <label for="">Upload Image</label>
                                            <input type="file" class="form-control" name="img" id="img">
                                            <label for="" style="margin-right: 10px;">Current Image</label>
                                            <input type="hidden" name="old_image" value="<?=$data['img']; ?>">
                                            <img src="../uploads/<?=$data['img']; ?>" height="50px" width="50px" alt="">
                                        </div>
                                    </div>
                                    <!--------------- SAVE BUTTON --------------->
                                    <div class="col-md-6">
                                        <button type="submit" class="btn BlueBtn mt-2" name="editFaculty_button" id="addFacultySave">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
            <?php
                } else {
                    echo "Faculty member not found.";
                }
            } else {
                echo "ID missing from URL.";
            }
            ?>
        </div>
    </div>
</div>

<!--------------- FOOTER --------------->

<?php include('includes/footer.php');?>
