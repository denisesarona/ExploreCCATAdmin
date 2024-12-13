<?php 
include('includes/header.php');
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');
$positionresultSet = getData("positiontb");
$departmentresultSet = getData("departmenttb");
?>
<link rel="stylesheet" href="assets/css/style.css">
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
                        <h3>FACULTY MEMBERS DETAILS</h3>
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
                                            <select class="form-control" name="position">
                                                <?php
                                                $current_position = $data['position'];
                                                ?>
                                                <option value='<?=$current_position?>' selected><?=$current_position?></option>
                                                <?php
                                                    // Fetch positions and ensure the current one is not listed again
                                                    while ($rows = $positionresultSet->fetch_assoc()) {
                                                        $position_name = $rows['name'];
                                                        if ($position_name !== $current_position) { // Only show if it's not the current position
                                                            echo "<option value='$position_name'>$position_name</option>";
                                                        }
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3"> 
                                        <div class="form-group">
                                            <label for="department">Departments</label>
                                            <!-- Add the "multiple" attribute for multi-selection -->
                                            <select class="form-control" name="departments[]" id="department" multiple onchange="updateDeptIds()">
                                                <?php
                                                $current_department = $data['department']; // Example for pre-selected department
                                                ?>
                                                <!-- Pre-select current department -->
                                                <option value='<?=$current_department?>' selected><?=$current_department?></option>
                                                <?php
                                                    while ($rows = $departmentresultSet->fetch_assoc()) {
                                                        $department_name = $rows['name'];
                                                        $dept_id = $rows['dept_id'];
                                                        // Set the option value to dept_id but display department name
                                                        echo "<option value='$dept_id'>$department_name</option>";
                                                    }
                                                ?>
                                            </select>
                                            <!-- Hidden input to store selected department IDs -->
                                            <input type="hidden" name="dept_ids" id="dept_ids">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3"> 
                                    <div class="form-group">
                                        <label for="">Upload Image</label>
                                        <input type="file" class="form-control" name="image">
                                        <label for="" style="margin-right: 10px;">Current Image</label>
                                        <input type="hidden" name="old_image" value="<?= $data['img']; ?>">
                                        <img src="../uploads/<?= $data['img']; ?>" height="50px" width="50px" alt="">
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
<script>
function updateDeptId() {
    var departmentSelect = document.getElementById('department');
    var deptIdInput = document.getElementById('dept_id');
    var selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
    deptIdInput.value = selectedOption.getAttribute('data-dept-id'); // Get the dept ID from data attribute
}
</script>
<!--------------- FOOTER --------------->

<?php include('includes/footer.php');?>
