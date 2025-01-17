<?php
// Include header, functions for queries, and middleware for admin access control
include('includes/header.php');
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');

// Get the faculty ID from the URL parameter 'id'
$faculty_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the current data of the faculty from the database
$facultyData = [];
if ($faculty_id > 0) {
    $facultyQuery = "SELECT * FROM facultytb WHERE faculty_id = $faculty_id";
    $facultyResult = mysqli_query($con, $facultyQuery);
    if ($facultyRow = mysqli_fetch_assoc($facultyResult)) {
        $facultyData = $facultyRow;
    }
}

// Fetch all departments for the dropdown options
$departmentresultSet = getData("departmenttb");

$positions = [];
// Fetch current department-position pairs for the selected faculty
if ($faculty_id > 0) {
    $positionsQuery = "SELECT * FROM dept_pos_facultytb WHERE faculty_id = $faculty_id";
    $positionsResult = mysqli_query($con, $positionsQuery);
    while ($row = mysqli_fetch_assoc($positionsResult)) {
        $positions[] = $row; // Store department-position pairs
    }
}

// If the form is submitted (editFaculty_button clicked)
if (isset($_POST['editFaculty_button'])) {
    // Sanitize the faculty name input to prevent SQL injection
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $image = $_FILES['img']['name']; // Get uploaded image name
    $path = "../uploads";
    $image_ext = pathinfo($image, PATHINFO_EXTENSION); // Get the image extension
    $filename = time() . '.' . $image_ext; // Generate a new filename based on the current timestamp

    // If image is uploaded, update it, otherwise keep the existing image
    if ($image) {
        $imageUpdateQuery = ", img = '$filename'";
        if (!move_uploaded_file($_FILES['img']['tmp_name'], $path . '/' . $filename)) {
            $_SESSION['error'] = "Image upload failed!";
            header("Location: facultyDetails.php?id=$faculty_id");
            exit(); // Exit if the upload fails
        }
    } else {
        $imageUpdateQuery = ""; // No new image uploaded, keep the old one
    }

    // Initialize $departments safely, ensuring it's an array
    $departments = isset($_POST['departments']) ? $_POST['departments'] : [];

    // Update the faculty data in the database
    $updateFacultyQuery = "UPDATE facultytb SET name = '$name' $imageUpdateQuery WHERE faculty_id = $faculty_id";

    if (mysqli_query($con, $updateFacultyQuery)) {
        // Initialize the $positions array safely
        $positions = isset($_POST['positions']) ? $_POST['positions'] : [];

        // Remove the departments that were marked for removal
        if (isset($_POST['removed_depts']) && !empty($_POST['removed_depts'])) {
            $removed_depts = explode(',', $_POST['removed_depts']);
            foreach ($removed_depts as $removed_dept_id) {
                $removed_dept_id = intval($removed_dept_id); // Ensure it's an integer
                if ($removed_dept_id > 0) {
                    $removeDeptQuery = $con->prepare("DELETE FROM dept_pos_facultytb WHERE faculty_id = ? AND dept_id = ?");
                    $removeDeptQuery->bind_param("ii", $faculty_id, $removed_dept_id);
                    if (!$removeDeptQuery->execute()) {
                        $_SESSION['error'] = "Failed to remove department: " . $removeDeptQuery->error;
                        header("Location: facultyDetails.php?id=$faculty_id");
                        exit();
                    }
                }
            }
        }
                


        // Add new department-position pairs to the database
        foreach ($departments as $index => $dept_id) {
            $position_id = isset($positions[$index]) ? $positions[$index] : null;

            // Validate that both department and position are selected
            if (!empty($dept_id) && !empty($position_id)) {
                // Insert new department-position pair into the dept_pos_facultytb table
                $addDepartmentQuery = "INSERT INTO dept_pos_facultytb (faculty_id, dept_id, position_id) 
                                        VALUES ('$faculty_id', '$dept_id', '$position_id')";
                if (!mysqli_query($con, $addDepartmentQuery)) {
                    $_SESSION['error'] = "Failed to link faculty to department: " . mysqli_error($con);
                    header("Location: facultyDetails.php?id=$faculty_id");
                    exit(); // Exit if insertion fails
                }
            }
        }


        $_SESSION['success'] = "✔ Faculty member updated successfully!";
        header("Location: facultyMember.php?id=$faculty_id");
        exit(); // Exit after successful update
    } else {
        $_SESSION['error'] = "Updating Faculty member failed: " . mysqli_error($con);
        header("Location: facultyMember.php?id=$faculty_id");
        exit(); // Exit if faculty update fails
    }
}

?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="container">
    <div id="confirm-duplicate-modal" class="modal" style="display: none;">
        <div class="modal-content">
            <p>This department has already been selected. Do you want to continue and allow duplication?</p>
            <button id="confirm-yes">Yes</button>
            <button id="confirm-no">No</button>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3>Edit Faculty Member</h3>
                <div class="card-body">
                    <form action="facultyDetails.php?id=<?= $faculty_id ?>" method="POST" enctype="multipart/form-data">
                        <div class="row" style="font-family: 'Poppins', sans-serif;">
                            <!-- Faculty Name -->
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label for="name" class="form-label">Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Faculty Member's Name" name="name" id="name" value="<?= $facultyData['name'] ?? '' ?>" required>
                                </div>
                            </div>
            
                            <div class="col-md-6 mt-4 mb-3">
                                <button type="button" class="btn BlueBtn" id="addDeptBtn">Add Department</button>
                            </div>

<!-- Department and Position -->
<div id="position-department-container">
    <?php
    // Render existing department-position sets based on the current data
    foreach ($positions as $i => $positionData) {
        $selectedDeptId = $positionData['dept_id'];
        $selectedPositionId = $positionData['position_id'];
    ?>
        <div class="row mb-3 position-department-set">
            <div class="col-md-5">
                <div class="form-group">
                    <label for="department" class="form-label">Department</label>
                    <select class="form-control department-dropdown" name="departments[]" disabled>
                        <option value="">Select Department</option>
                        <?php
                        $departmentresultSet->data_seek(0); // Reset department result set pointer
                        while ($rows = $departmentresultSet->fetch_assoc()) {
                            $department_name = $rows['name'];
                            $department_id = $rows['dept_id'];
                            $selected = ($department_id == $selectedDeptId) ? 'selected' : ''; 
                            echo "<option value='$department_id' $selected>$department_name</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>

            <div class="col-md-5">
                <div class="form-group">
                    <label for="position" class="form-label">Position</label>
                    <select class="form-control" name="positions[]" disabled>
                        <option value="">Select Position</option>
                        <?php
                        if ($selectedDeptId) {
                            // Fetch positions for the selected department
                            $positionsQuery = "SELECT * FROM positiontb WHERE dept_id = $selectedDeptId";
                            $result = mysqli_query($con, $positionsQuery);
                            while ($position = mysqli_fetch_assoc($result)) {
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

            <!-- Remove Button -->
            <div class="col-md-2 mt-4">
                <button type="button" class="btn btn-danger removeDeptBtn" onclick="removeDepartment(this)">Remove</button>
            </div>
        </div>
    <?php } ?>
</div>
<!-- Hidden input to store removed department IDs -->
<input type="hidden" id="removedDepts" name="removed_depts" value="">
                            <!-- Upload Image -->
                            <div class="col-md-12 mb-3">
                                <div class="form-group">
                                    <label for="">Upload Image</label>
                                    <input type="file" class="form-control" name="img">
                                    <label for="">Current Image</label>
                                    <input type="hidden" name="old_image" value="<?= $facultyData['img']; ?>">
                                    <img src="../uploads/<?= $facultyData['img']; ?>" height="50px" width="50px" alt="">
                                </div>
                            </div>


                            <div class="col-md-6">
                                <button type="submit" class="btn BlueBtn mt-2" name="editFaculty_button">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById("addDeptBtn").addEventListener("click", function () {
    var container = document.getElementById("position-department-container");

    // Create a new department-position set
    var deptPosDiv = document.createElement("div");
    deptPosDiv.classList.add("row", "mb-3", "position-department-set");

    // Department Dropdown
    var deptDiv = document.createElement("div");
    deptDiv.classList.add("col-md-5");
    deptDiv.innerHTML = `
        <div class="form-group">
            <label for="department" class="form-label">Department</label>
            <select class="form-control department-dropdown" name="departments[]">
                <option value="">Select Department</option>
            </select>
        </div>
    `;
    deptPosDiv.appendChild(deptDiv);

    // Position Dropdown
    var posDiv = document.createElement("div");
    posDiv.classList.add("col-md-5");
    posDiv.innerHTML = `
        <div class="form-group">
            <label for="position" class="form-label">Position</label>
            <select class="form-control position-dropdown" name="positions[]">
                <option value="">Select Position</option>
            </select>
        </div>
    `;
    deptPosDiv.appendChild(posDiv);

    // Remove Button
    var removeDiv = document.createElement("div");
    removeDiv.classList.add("col-md-2", "mt-4");
    removeDiv.innerHTML = `
        <button type="button" class="btn btn-danger removeDeptBtn" onclick="removeDepartment(this)">Remove</button>
    `;
    deptPosDiv.appendChild(removeDiv);

    // Prepend the new row to the container (adds to the top)
    container.prepend(deptPosDiv);

    // Populate the department dropdown for the new set
    populateDepartments();
});


function populateDepartments() {
    var departmentDropdowns = document.getElementsByClassName("department-dropdown");

    for (var i = 0; i < departmentDropdowns.length; i++) {
        var dropdown = departmentDropdowns[i];
        var currentValue = dropdown.value; // Store current selected value to retain it later

        // Populate department options
        dropdown.innerHTML = '<option value="">Select Department</option>';
        <?php
        $departmentresultSet->data_seek(0);
        while ($row = $departmentresultSet->fetch_assoc()) {
            $department_id = $row['dept_id'];
            $department_name = $row['name'];
        ?>
            dropdown.innerHTML += `<option value="<?= $department_id ?>"><?= $department_name ?></option>`;
        <?php } ?>

        dropdown.value = currentValue; // Re-select the previously selected value, if any

        // Fetch positions if a department value is selected
        if (dropdown.value) {
            fetchPositions(dropdown.value, dropdown.closest('.position-department-set').querySelector('.position-dropdown'));
        }
    }
}


// Event listener for department change
document.addEventListener("change", function (event) {
    if (event.target.classList.contains("department-dropdown")) {
        var deptId = event.target.value;
        var positionDropdown = event.target.closest(".position-department-set").querySelector('.position-dropdown');
        fetchPositions(deptId, positionDropdown);
    }
});

function fetchPositions(deptId, positionDropdown) {
    if (deptId) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "fetch_positions.php?dept_id=" + deptId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var positions = JSON.parse(xhr.responseText);
                var currentValue = positionDropdown.value; // Save the currently selected value

                positionDropdown.innerHTML = '<option value="">Select Position</option>';
                positions.forEach(function(position) {
                    var option = document.createElement("option");
                    option.value = position.position_id;
                    option.textContent = position.position_name;
                    positionDropdown.appendChild(option);
                });

                positionDropdown.value = currentValue; // Restore the selection
            }
        };
        xhr.send();
    } else {
        positionDropdown.innerHTML = '<option value="">Select Position</option>';
    }
}

function removeDepartment(button) {
    var deptPosSet = button.closest('.position-department-set');
    var departmentDropdown = deptPosSet.querySelector('.department-dropdown');
    var removedDeptsInput = document.getElementById('removedDepts');

    if (departmentDropdown) {
        var deptId = departmentDropdown.value; // Get the department ID
        if (deptId) {
            // Get existing removed departments
            var removedDepts = removedDeptsInput.value ? removedDeptsInput.value.split(',') : [];
            
            // Add the department ID if it's not already in the list
            if (!removedDepts.includes(deptId)) {
                removedDepts.push(deptId);
                removedDeptsInput.value = removedDepts.join(','); // Update the hidden input
            }
        }
    }

    // Remove the department-position set from the UI
    deptPosSet.remove();
}



document.querySelector('form').addEventListener('submit', function () {
    var removedDeptsInput = document.getElementById('removedDepts');
    console.log('Removed Departments:', removedDeptsInput.value);
});

</script>


<?php include('includes/footer.php'); ?>
