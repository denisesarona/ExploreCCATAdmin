<?php
include('includes/header.php');
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');

// Fetch all departments
$departmentresultSet = getData("departmenttb");

$positions = [];
$deptPositionCount = 0;


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
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $image = $_FILES['img']['name'];
    $path = "../uploads";

    // Handle Image Upload
    if (!empty($image)) {
        $image_ext = pathinfo($image, PATHINFO_EXTENSION);
        $filename = time() . '.' . $image_ext;
        move_uploaded_file($_FILES['img']['tmp_name'], "$path/$filename");
    } else {
        $filename = 'default.jpg'; // Use a default image if none is uploaded
    }

    // Insert new faculty
    $insertFacultyQuery = "INSERT INTO facultytb (name, img) VALUES ('$name', '$filename')";
    mysqli_query($con, $insertFacultyQuery) or die(mysqli_error($con));

    // Get the inserted faculty ID
    $faculty_id = mysqli_insert_id($con);
    // Insert department and position links
    if (!empty($_POST['departments']) && !empty($_POST['positions'])) {
        $insertSuccess = false; // Track if any insertion was successful
        foreach ($_POST['departments'] as $index => $dept_id) {
            $position_id = $_POST['positions'][$index] ?? '';
    
            // Only insert if both department and position are selected
            if (!empty($dept_id) && !empty($position_id)) {
                $insertDeptPosQuery = "INSERT INTO dept_pos_facultytb (faculty_id, dept_id, position_id) 
                                    VALUES ('$faculty_id', '$dept_id', '$position_id')";
                mysqli_query($con, $insertDeptPosQuery) or die(mysqli_error($con));
                $insertSuccess = true; // Mark as successful
            } else {
                $_SESSION['error'] = 'Please select a position for the department!';
            }
        }
    
        // Redirect only if at least one insertion was successful
        if ($insertSuccess) {
            $_SESSION['success'] = "✔ Faculty member added successfully!";
            header("Location: facultyMember.php");
            exit();
        }
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

                        <!-- Add Department Button -->
                        <div class="col-md-6 mb-3">
                            <button type="button" class="btn BlueBtn" id="addDeptBtn">Add Department</button>
                        </div>
                        <!-- Department and Position -->
                        <div id="position-department-container">
                            <?php
                            // Render department-position sets based on the number of departments selected dynamically
                            if (isset($_POST['departments'])) {
                                foreach ($_POST['departments'] as $index => $selectedDeptId) {
                                    $selectedPositionId = $_POST['positions'][$index] ?? '';
                                    ?>
                                    <div class="row mb-3 position-department-set">
                                        <div class="col-md-5"> 
                                            <div class="form-group">
                                                <label for="department" class="form-label">Department</label>
                                                <select class="form-control department-dropdown" name="departments[]">
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
                                        <div class="col-md-5">
                                            <div class="form-group">
                                                <label for="position" class="form-label">Position</label>
                                                <select class="form-control" name="positions[]">
                                                    <option value="">Select Position</option>
                                                    <?php
                                                    // Populate positions dropdown based on the selected department
                                                    if ($selectedDeptId) {
                                                        foreach ($positions[$index] as $position) {
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
                                        <div class="col-md-2 d-flex align-items-end">
                                            <button type="button" class="btn RedBtn removeDeptBtn">Remove</button>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                            ?>
                        </div>
                        <!-- Upload Image -->
                        <div class="col-md-12 mb-3"> 
                            <div class="form-group">
                                <label for="image" class="form-label">Upload Image</label>
                                <input type="file" class="form-control" name="img" id="img">
                            </div>
                        </div>
                        <input type="hidden" name="faculty_id" value="<?= isset($faculty_id) ? $faculty_id : '' ?>">
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
function validateForm(event) {
    let isValid = true; // Assume the form is valid initially
    const deptPosSets = document.querySelectorAll('.position-department-set');

    deptPosSets.forEach(function (set) {
        const departmentDropdown = set.querySelector('.department-dropdown');
        const positionDropdown = set.querySelector('.position-dropdown');

        // Check if department and position are selected
        if (departmentDropdown.value && !positionDropdown.value) {
            isValid = false;
        }
    });

    // If validation fails, stop form submission
    if (!isValid) {
        event.preventDefault(); // Prevent form submission
    }

    return isValid; // Ensure that validation result is returned
}

// Attach the validateForm logic to the form's submit event
document.querySelector('form').addEventListener('submit', function (event) {
    validateForm(event); // Pass the event to the validation function
});
document.getElementById('addDeptBtn').addEventListener('click', function() {
    const container = document.getElementById('position-department-container');
    const departmentPositionSet = document.createElement('div');
    departmentPositionSet.classList.add('row', 'mb-3', 'position-department-set');

    // Department & Position Dropdown with Remove Button
    departmentPositionSet.innerHTML = `
        <div class="col-md-5"> 
            <div class="form-group">
                <label for="department" class="form-label">Department</label>
                <select class="form-control department-dropdown" name="departments[]">
                    <option value="">Select Department</option>
                    <?php
                        // Populate department dropdown
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
        
        <div class="col-md-5">
            <div class="form-group">
                <label for="position" class="form-label">Position</label>
                <select class="form-control" name="positions[]">
                    <option value="">Select Position</option>
                </select>
            </div>
        </div>

        <!-- Remove Button -->
        <div class="col-md-2">
            <button type="button" class="btn btn-danger removeDeptBtn">Remove</button>
        </div>
    `;

    // Add new set to the top instead of the bottom
    container.prepend(departmentPositionSet);

    // Add event listener for remove button
    departmentPositionSet.querySelector('.removeDeptBtn').addEventListener('click', function() {
        departmentPositionSet.remove();
    });

    // Add event listener to department dropdown for position fetching
    const departmentDropdown = departmentPositionSet.querySelector('.department-dropdown');
    departmentDropdown.addEventListener('change', function() {
        const deptId = departmentDropdown.value;
        const positionDropdown = departmentPositionSet.querySelector('select[name="positions[]"]');

        if (deptId) {
            fetch(`fetch_addposition.php?dept_id=${deptId}`)
                .then(response => response.json())
                .then(data => {
                    positionDropdown.innerHTML = `<option value="">Select Position</option>`;
                    data.positions.forEach(position => {
                        const option = document.createElement('option');
                        option.value = position.position_id;
                        option.textContent = position.position_name;
                        positionDropdown.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching positions:', error));
        } else {
            positionDropdown.innerHTML = `<option value="">Select Position</option>`;
        }
    });
});

</script>

 

<!--------------- ALERTIFY JS ---------------> 
<?php include('includes/footer.php');?>
