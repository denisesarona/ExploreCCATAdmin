<?php 
include('includes/header.php');
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');
$positionresultSet = getData("positiontb");
$departmentresultSet = getData("departmenttb");
?>
<link rel="stylesheet" href="assets/css/style.css">

<!--------------- ADD FACULTY MEMBER PAGE --------------->

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3>ADD FACULTY MEMBERS</h3>
                <div class="card-body">
                    <form action="codes.php" method="POST" enctype="multipart/form-data">
                        <div class="row" style="font-family: 'Poppins', sans-serif;">
                            <!-- Faculty Name -->
                            <div class="col-md-12 mb-3"> 
                                <div class="form-group">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Faculty Member's Name" name="name" id="name" required>
                                </div>
                            </div>

                            <!-- Initial Position and Department -->
                            <div id="position-department-container">
                                <div class="row mb-3 position-department-set">
                                    <div class="col-md-6"> 
                                        <div class="form-group">
                                            <label for="position" class="form-label">Position</label>
                                            <select class="form-control" name="positions[]" required>
                                                <option value="">Select Position</option>
                                                <?php
                                                    $positionresultSet->data_seek(0); // Reset position result set
                                                    while ($rows = $positionresultSet->fetch_assoc()) {
                                                        $position_name = $rows['name'];
                                                        $position_id = $rows['position_id'];
                                                        echo "<option value='$position_id'>$position_name</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6"> 
                                        <div class="form-group">
                                            <label for="department" class="form-label">Department</label>
                                            <select class="form-control" name="departments[]">
                                                <option value="">Select Department</option>
                                                <?php
                                                    $departmentresultSet->data_seek(0); // Reset department result set
                                                    while ($rows = $departmentresultSet->fetch_assoc()) {
                                                        $department_name = $rows['name'];
                                                        $department_id = $rows['dept_id'];
                                                        echo "<option value='$department_id'>$department_name</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Button to Add More Position-Department Sets -->
                            <div class="col-md-12 mb-3">
                                <button type="button" class="btn btn-success" onclick="addPositionDepartmentSet()">+ Add Position and Department</button>
                            </div>

                            <!-- Upload Image -->
                            <div class="col-md-12 mb-3"> 
                                <div class="form-group">
                                    <label for="image" class="form-label">Upload Image</label>
                                    <input type="file" class="form-control" name="img" id="img" required>
                                </div>
                            </div>

                            <!-- Save Button -->
                            <div class="col-md-6">
                                <button type="submit" class="btn BlueBtn mt-2" name="addFaculty_button">Save</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>  
        </div>
    </div>
</div>

<!--------------- JavaScript to Add Dynamic Sets --------------->
<script>
function addPositionDepartmentSet() {
    const container = document.getElementById('position-department-container');

    const positionDepartmentSet = `
    <div class="row mb-3 position-department-set">
        <div class="col-md-6"> 
            <div class="form-group">
                <label for="position" class="form-label">Position</label>
                <select class="form-control" name="positions[]" required>
                    <option value="">Select Position</option>
                    <?php
                        $positionresultSet->data_seek(0);
                        while ($rows = $positionresultSet->fetch_assoc()) {
                            $position_name = $rows['name'];
                            $position_id = $rows['position_id'];
                            echo "<option value='$position_id'>$position_name</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-md-6"> 
            <div class="form-group">
                <label for="department" class="form-label">Department</label>
                <select class="form-control" name="departments[]" required>
                    <option value="">Select Department</option>
                    <?php
                        $departmentresultSet->data_seek(0);
                        while ($rows = $departmentresultSet->fetch_assoc()) {
                            $department_name = $rows['name'];
                            $department_id = $rows['dept_id'];
                            echo "<option value='$department_id'>$department_name</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
    </div>`;

    container.insertAdjacentHTML('beforeend', positionDepartmentSet);
}
</script>

<!--------------- FOOTER --------------->
<?php include('includes/footer.php'); ?>
