<?php
include('includes/header.php');
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');

// Fetch all departments
$departmentresultSet = getData("departmenttb");

$positions = [];
$deptPositionCount = 0;

if (isset($_POST['number_of_depts'])) {
    $deptPositionCount = (int)$_POST['number_of_depts'];
}

if (isset($_POST['departments']) && $deptPositionCount > 0) {
    for ($i = 0; $i < $deptPositionCount; $i++) {
        $selectedDeptId = $_POST['departments'][$i] ?? '';
        $positions[$i] = [];
        if ($selectedDeptId) {
            $positionsQuery = "SELECT * FROM positiontb WHERE dept_id = $selectedDeptId";
            $result = mysqli_query($con, $positionsQuery);
            while ($row = mysqli_fetch_assoc($result)) {
                $positions[$i][] = $row;
            }
        }
    }
}

if (isset($_POST['addFaculty_button'])) {
    // Sanitize inputs to prevent SQL Injection
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $image = $_FILES['img']['name'];
    $path = "../uploads";
    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $filename = time() . '.' . $image_ext;

    // Check if the faculty member already exists
    $checkQuery = "SELECT * FROM facultytb WHERE name = '$name'";
    $checkResult = mysqli_query($con, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $_SESSION['error'] = "A faculty member with this name already exists!";
        header("Location: facultyMember.php");
        exit();
    }

    if (!move_uploaded_file($_FILES['img']['tmp_name'], $path . '/' . $filename)) {
        $_SESSION['error'] = "Please upload an image!";
        header("Location: addFacultyMember.php");
        exit(); // Exit to prevent data from being inserted
    }
    
    // Insert faculty data into the facultytb table after image is uploaded successfully
    $addFaculty_query = "INSERT INTO facultytb (name, img) VALUES ('$name', '$filename')";
    if (mysqli_query($con, $addFaculty_query)) {
        $faculty_id = mysqli_insert_id($con); // Get the last inserted faculty ID
    
        // Handle multiple departments and positions
        $departments = $_POST['departments'];  // Array of selected department IDs
        $positions = $_POST['positions'];     // Array of selected position IDs
    
        foreach ($departments as $index => $dept_id) {
            $position_id = $positions[$index];  // Position ID selected for this department
            $pid = isset($_POST['pid']) ? $_POST['pid'] : 0; // Use the pid from the form submission (default to 0 if not provided)
        
            // Insert faculty to department position link (allow duplicates)
            $addDepartment_query = "INSERT INTO dept_pos_facultytb (faculty_id, dept_id, position_id, pid) 
                                    VALUES ('$faculty_id', '$dept_id', '$position_id', '$pid')";
        
            if (!mysqli_query($con, $addDepartment_query)) {
                $_SESSION['error'] = "Failed to link faculty to department: " . mysqli_error($con);
                header("Location: facultyMember.php");
                exit();
            }
        }
    
        $_SESSION['success'] = "✔ Faculty member added successfully!";
        header("Location: addFacultyMember.php");
        exit();
    } else {
        $_SESSION['error'] = "Adding Faculty member failed: " . mysqli_error($con);
        header("Location: facultyMember.php");
        exit();
    }
}

?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3>ADD FACULTY MEMBERS</h3>
                <div class="card-body">
                <form id="facultyForm" action="addFacultyMember.php" method="POST" enctype="multipart/form-data">
                        <div class="row" style="font-family: 'Poppins', sans-serif;">
                            <!-- Faculty Name -->
                            <div class="col-md-12 mb-3"> 
                                <div class="form-group">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Faculty Member's Name" name="name" id="name" value="<?= isset($_POST['name']) ? $_POST['name'] : '' ?>" required>
                                </div>
                            </div>

                            <!-- Number of Departments -->
                            <div class="col-md-6 mb-3"> 
                                <div class="form-group">
                                    <label for="number_of_depts" class="form-label">Number of Departments Assigned</label>
                                    <input type="number" class="form-control" placeholder="Enter number of departments" name="number_of_depts" id="number_of_depts" value="<?= $deptPositionCount ?>" min="1" required>
                                </div>
                            </div>

                            <!-- Button to Set the Number of Departments -->
                            <div class="col-md-6 mt-4 mb-3">
                                <button type="submit" class="btn BlueBtn" name="setDeptNumber"><?= $deptPositionCount > 0 ? "Reselect Number of Departments" : "Set Number of Departments" ?></button>
                            </div>

                            <!-- Department and Position -->
                            <div id="position-department-container">
                                <?php
                                // Render department-position sets based on the number of departments selected
                                for ($i = 0; $i < $deptPositionCount; $i++) {
                                    $selectedDeptId = $_POST['departments'][$i] ?? '';
                                    $selectedPositionId = $_POST['positions'][$i] ?? '';
                                ?>
                                    <div class="row mb-3 position-department-set">
                                        <div class="col-md-6"> 
                                            <div class="form-group">
                                                <label for="department" class="form-label">Department</label>
                                                <select class="form-control department-dropdown" name="departments[]" onchange="this.form.submit();">
                                                    <option value="">Select Department</option>
                                                    <?php
                                                        // Populate department dropdown
                                                        $departmentresultSet->data_seek(0);
                                                        while ($rows = $departmentresultSet->fetch_assoc()) {
                                                            $department_name = $rows['name'];
                                                            $department_id = $rows['dept_id'];
                                                            $selected = ($department_id == $selectedDeptId) ? 'selected' : ''; // Mark selected department
                                                            echo "<option value='$department_id' $selected>$department_name</option>";
                                                        }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Position Dropdown (Will be shown based on department selection) -->
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="position" class="form-label">Position</label>
                                                <select class="form-control" name="positions[]">
                                                    <option value="">Select Position</option>
                                                    <?php
                                                    // Populate positions dropdown based on the selected department
                                                    if ($selectedDeptId) {
                                                        foreach ($positions[$i] as $position) {
                                                            $position_id = $position['position_id'];
                                                            $position_name = $position['position_name'];
                                                            $selectedPosition = ($position_id == $selectedPositionId) ? 'selected' : '';
                                                            echo "<option value='$position_id' $selectedPosition>$position_name</option>";
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>

                            <!-- Upload Image -->
                            <div class="col-md-12 mb-3"> 
                                <div class="form-group">
                                    <label for="image" class="form-label">Upload Image</label>
                                    <input type="file" class="form-control" name="img" id="img">
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

<!-- Custom Alert Box -->
<div id="alertBox" class="alert-box" style="display: none;">
    <span id="alertMessage"></span>
</div>

<!--------------- FOOTER --------------->

<?php include('includes/footer.php'); ?>

<script>
function validateDepartments() {
    const departments = document.getElementsByName('departments[]');
    const selectedDepts = [];
    let duplicateDetected = false;

    for (let i = 0; i < departments.length; i++) {
        const deptId = departments[i].value;
        if (deptId !== "" && selectedDepts.includes(deptId)) {
            duplicateDetected = true;
            break;
        }
        selectedDepts.push(deptId);
    }

    if (duplicateDetected) {
        // Show the modal for duplicate department confirmation
        const modal = document.getElementById('confirm-duplicate-modal');
        modal.style.display = 'flex'; // Show modal

        // Return false to halt form submission until modal decision
        return new Promise((resolve) => {
            document.getElementById('confirm-yes').onclick = function () {
                modal.style.display = 'none';
                resolve(true); // User chose "Yes"
            };
            document.getElementById('confirm-no').onclick = function () {
                modal.style.display = 'none';
                resolve(false); // User chose "No"
            };
        }).then((continueInsertion) => {
            // If user confirms, allow the form to submit
            if (continueInsertion) {
                return true; // Proceed with form submission
            } else {
                return false; // Do not submit the form
            }
        });
    }

    // If no duplicates, proceed with form submission
    return true;
}

</script>

 

<!--------------- ALERTIFY JS ---------------> 
<?php include('includes/footer.php');?>
