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
                $facultymember_dept_pos = getFacultyByID('dept_pos_facultytb', $id);

                if (mysqli_num_rows($facultymember) > 0) {
                    $data = mysqli_fetch_array($facultymember);
                    $faculty_id = $data['faculty_id'];  // Faculty ID
                    $assignments = getFacultyDeptAssignments($faculty_id);
            ?>  
                    <div class="card mt-5">
                        <h3>FACULTY MEMBERS DETAILS</h3>
                        <div class="card-body">
                            <form action="codes.php" method="POST" enctype="multipart/form-data">
                                <div class="row" style="font-family: 'Poppins', sans-serif;">
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <input type="hidden" name="faculty_id" value="<?=$data['faculty_id']; ?>">
                                            <label for="">Name</label>
                                            <input type="text" value="<?=$data['name']; ?>" class="form-control" placeholder="Enter Name" name="name" id="name">
                                        </div>
                                    </div>

                                    <div id="position-department-container">
                                        <!-- Loop Through Existing Positions and Departments -->
                                        <?php while ($assignment = mysqli_fetch_assoc($assignments)) {
                                            $current_position_id = $assignment['position_id'];
                                            $current_department_id = $assignment['dept_id'];
                                        ?>
                                        <div class="row mb-3 position-department-set">
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="position">Position</label>
                                                    <select class="form-control" name="positions[]">
                                                        <option value="">Select Position</option>
                                                        <?php
                                                            $positionresultSet->data_seek(0);
                                                            while ($rows = $positionresultSet->fetch_assoc()) {
                                                                $selected = ($rows['position_id'] == $current_position_id) ? 'selected' : '';
                                                                echo "<option value='{$rows['position_id']}' $selected>{$rows['name']}</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="department">Department</label>
                                                    <select class="form-control" name="departments[]">
                                                        <option value="">Select Department</option>
                                                        <?php
                                                            $departmentresultSet->data_seek(0);
                                                            while ($rows = $departmentresultSet->fetch_assoc()) {
                                                                $selected = ($rows['dept_id'] == $current_department_id) ? 'selected' : '';
                                                                echo "<option value='{$rows['dept_id']}' $selected>{$rows['name']}</option>";
                                                            }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>

                                            <div class="col-md-2">
                                                <form action="codes.php" method="POST">
                                                    <input type="hidden" name="info_id" value="<?= $assignment['faculty_dept_id']; ?>">
                                                    <button type="submit" class="btn RedBtn" style="margin-top: 10px;" name="deleteFacultyInfo_button">Delete</button>
                                                </form>
                                            </div>

                                        </div>
                                        <?php } ?>
                                    </div>

                                    <!-- Button to Add New Position-Department Set -->
                                    <div class="col-md-12 mb-3">
                                        <button type="button" class="btn btn-success" onclick="addPositionDepartmentSet()">+ Add Position and Department</button>
                                    </div>

                                    <!-- Image Upload -->
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <label for="">Upload Image</label>
                                            <input type="file" class="form-control" name="image">
                                            <label for="">Current Image</label>
                                            <input type="hidden" name="old_image" value="<?= $data['img']; ?>">
                                            <img src="../uploads/<?= $data['img']; ?>" height="50px" width="50px" alt="">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <button type="submit" class="btn BlueBtn mt-2" name="editFaculty_button">Update</button>
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

<!-- JavaScript for Dynamic Rows -->
<script>
function addPositionDepartmentSet() {
    const container = document.getElementById('position-department-container');

    const newSet = `
    <div class="row mb-3 position-department-set">
        <div class="col-md-5">
            <div class="form-group">
                <label for="position">Position</label>
                <select class="form-control" name="positions[]">
                    <option value="">Select Position</option>
                    <?php
                        $positionresultSet->data_seek(0);
                        while ($rows = $positionresultSet->fetch_assoc()) {
                            echo "<option value='{$rows['position_id']}'>{$rows['name']}</option>";
                        }
                    ?>
                </select>
            </div>
        </div>
        <div class="col-md-5">
            <div class="form-group">
                <label for="department">Department</label>
                <select class="form-control" name="departments[]">
                    <option value="">Select Department</option>
                    <?php
                        $departmentresultSet->data_seek(0);
                        while ($rows = $departmentresultSet->fetch_assoc()) {
                            echo "<option value='{$rows['dept_id']}'>{$rows['name']}</option>";
                        }
                    ?>
                </select>
            </div>
        </div>

        <div class="col-md-2">
            <form action="codes.php" method="POST">
                <input type="hidden" name="info_id" value="<?= $assignment['faculty_dept_id']; ?>">
                <button type="submit" class="btn RedBtn" style="margin-top: 10px;" name="deleteFacultyInfo_button">Delete</button>
            </form>
        </div>
    </div>`;

    container.insertAdjacentHTML('beforeend', newSet);
}
</script>

<?php include('includes/footer.php');?>
