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
                            <div class="col-md-6 mb-3"> 
                                <div class="form-group">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Faculty Member's Name" name="name" id="name" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3"> 
                                <div class="form-group">
                                    <label for="position" class="form-label">Position</label>
                                    <select class="form-control" name="positions[]" required multiple>
                                        <option value="">Select Position</option>
                                        <?php
                                            while ($rows = $positionresultSet->fetch_assoc()) {
                                                $position_name = $rows['name'];
                                                $position_id = $rows['position_id'];
                                                echo "<option value='$position_id'>$position_name</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3"> 
                                <div class="form-group">
                                    <label for="department" class="form-label">Department</label>
                                    <select class="form-control" name="departments[]" required multiple>
                                        <option value="">Select Department</option>
                                        <?php
                                            while ($rows = $departmentresultSet->fetch_assoc()) {
                                                $department_name = $rows['name'];
                                                $department_id = $rows['dept_id'];
                                                echo "<option value='$department_id'>$department_name</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12 mb-3"> 
                                <div class="form-group">
                                    <label for="image" class="form-label">Upload Image</label>
                                    <input type="file" class="form-control" name="img" id="img" required>
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
</div>

<script>
function updateDeptId() {
    var departmentSelect = document.getElementById('department');
    var deptIdInput = document.getElementById('dept_id');
    var selectedOptions = departmentSelect.selectedOptions;
    var deptIds = [];
    
    for (var i = 0; i < selectedOptions.length; i++) {
        deptIds.push(selectedOptions[i].value); // Get the selected department IDs
    }

    deptIdInput.value = deptIds.join(","); // Join the department IDs as a comma-separated string
}

</script>

<!--------------- FOOTER --------------->

<?php include('includes/footer.php'); ?>
