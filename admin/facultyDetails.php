<?php 
    include('includes/header.php');
    include('../functions/queries.php');
    include('../middleware/adminMiddleware.php');
    $positionresultSet = getData("positiontb");
    $departmentresultSet = getData("departmenttb");


    // Fetch the position details by position ID
    function getPositionByID($position_id) {
        global $con;
        $query = "SELECT name FROM positiontb WHERE position_id = $position_id";
        $result = mysqli_query($con, $query);
        return mysqli_fetch_assoc($result);
    }

    // Fetch the department details by department ID
    function getDepartmentByID($dept_id) {
        global $con;
        $query = "SELECT name FROM departmenttb WHERE dept_id = $dept_id";
        $result = mysqli_query($con, $query);
        return mysqli_fetch_assoc($result);
    }

    // Fetch all position and department assignments for a specific faculty member
    function getFacultyDeptAssignments($faculty_id) {
        global $con;
        $query = "SELECT * FROM dept_pos_facultytb WHERE faculty_id = $faculty_id";
        return mysqli_query($con, $query);
    }
?>
<link rel="stylesheet" href="assets/css/style.css">
<!--------------- VIEW AND EDIT FACULTY MEMBERS DETAILS PAGE --------------->

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            if (isset($_GET['id'])) {
                $id = $_GET['id']; // Capture the ID from the URL
                $facultymember = getFacultyByID('facultytb', $id);
                $facultymember_dept_pos = getFacultyByID('dept_pos_facultytb', $id); // Fetch record from database

                if (mysqli_num_rows($facultymember) > 0) {
                    $data = mysqli_fetch_array($facultymember);
                    $faculty_data = mysqli_fetch_array($facultymember_dept_pos);
                        // Fetch the faculty member's position and department assignments
                    $faculty_id = $data['faculty_id'];  // Faculty ID from the previous query
                    $assignments = getFacultyDeptAssignments($faculty_id);
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

                                    <div class="form-group">
                                        <label for="">Current Positions and Departments</label>
                                        <ul class="list-group">
                                            <?php
                                                // Display positions and departments
                                                while ($assignment = mysqli_fetch_assoc($assignments)) {
                                                    // Fetch department and position names
                                                    $position = getPositionByID($assignment['position_id']);
                                                    $department = getDepartmentByID($assignment['dept_id']);
                                                    
                                                    // Display each position and department in a list
                                                    echo "<li class='list-group-item'>";
                                                    echo "<strong>Position:</strong> " . $position['name'] . " | ";
                                                    echo "<strong>Department:</strong> " . $department['name'];
                                                    echo "</li>";
                                                }
                                            ?>
                                        </ul>
                                    </div>

                                    <div class="col-md-6 mb-3"> 
                                        <div class="form-group">
                                            <label for="">Position</label>
                                            <select class="form-control" name="positions[]" multiple>
                                                <?php
                                                    // Fetch current positions assigned to the faculty member
                                                    $current_positions = explode(",", $faculty_data['position']); 
                                                    while ($rows = $positionresultSet->fetch_assoc()) {
                                                        $position_name = $rows['name'];
                                                        $position_id = $rows['position_id'];
                                                        // Check if the position is selected and apply the custom class
                                                        $selected = in_array($position_id, $current_positions) ? 'selected-option' : ''; 
                                                        echo "<option value='$position_id' class='$selected'>$position_name</option>";
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mb-3"> 
                                        <div class="form-group">
                                            <label for="department">Departments</label>
                                            <select class="form-control" name="departments[]" id="department" multiple>
                                                <?php
                                                    // Fetch current departments
                                                    $current_departments = explode(",", $faculty_data['department']); // Assuming departments are stored as comma-separated values
                                                    while ($rows = $departmentresultSet->fetch_assoc()) {
                                                        $department_name = $rows['name'];
                                                        $department_id = $rows['dept_id'];
                                                        $selected = in_array($department_id, $current_departments) ? 'selected selected-option' : ''; // Add the 'selected-option' class
                                                        echo "<option value='$department_id' $selected>$department_name</option>";
                                                    }
                                                ?>
                                            </select>
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

                                    <div class="col-md-6">
                                        <button type="submit" class="btn BlueBtn mt-2" name="editFaculty_button" id="addFacultySave">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <?php
                } else {
                    echo "Faculty Member not found";
                }
            } else {
                echo "ID missing from URL.";
            }
            ?>
        </div>
    </div>
</div>

<script>
    function updateDeptIds() {
        var departmentSelect = document.getElementById('department');
        var deptIds = [];

        // Loop through selected options and get their values
        for (var i = 0; i < departmentSelect.selectedOptions.length; i++) {
            deptIds.push(departmentSelect.selectedOptions[i].value);
        }

        // Store the department IDs in a hidden input field
        document.getElementById('dept_ids').value = deptIds.join(","); // Convert array to comma-separated string
    }

    // Trigger the function before submitting the form
    document.querySelector('form').addEventListener('submit', function() {
        updateDeptIds();
    });
</script>

<!--------------- FOOTER --------------->

<?php include('includes/footer.php');?>
