<?php
include('includes/header.php');
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');

// Get the faculty ID from the URL
$faculty_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch the current data of the faculty
$facultyData = [];
if ($faculty_id > 0) {
    $facultyQuery = "SELECT * FROM facultytb WHERE faculty_id = $faculty_id";
    $facultyResult = mysqli_query($con, $facultyQuery);
    if ($facultyRow = mysqli_fetch_assoc($facultyResult)) {
        $facultyData = $facultyRow;
    }
}

// Fetch all departments
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
// Handle faculty member form submission for updating
if (isset($_POST['editFaculty_button'])) {
    // Sanitize inputs to prevent SQL Injection
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $image = $_FILES['img']['name'];
    $path = "../uploads";
    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $filename = time() . '.' . $image_ext;

    // If image is uploaded, update it, otherwise keep the existing image
    if ($image) {
        $imageUpdateQuery = ", img = '$filename'";
        if (!move_uploaded_file($_FILES['img']['tmp_name'], $path . '/' . $filename)) {
            $_SESSION['error'] = "Image upload failed!";
            header("Location: facultyDetails.php?id=$faculty_id");
            exit();
        }
    } else {
        $imageUpdateQuery = ""; // No new image uploaded, keep the old one
    }

    // Check for duplicate departments in the submitted form
    $departments = $_POST['departments']; // Array of selected department IDs
    if (count($departments) !== count(array_unique($departments))) {
        $_SESSION['error'] = "Duplicate departments are not allowed!";
        header("Location: facultyDetails.php?id=$faculty_id");
        exit();
    }

    // Update faculty data in the database
    $updateFacultyQuery = "UPDATE facultytb SET name = '$name' $imageUpdateQuery WHERE faculty_id = $faculty_id";

    if (mysqli_query($con, $updateFacultyQuery)) {
        // Handle updating department and position relationships
        $positions = $_POST['positions'];    // Array of selected position IDs

        // Remove existing department-position pairs
        $deleteDepartmentsQuery = "DELETE FROM dept_pos_facultytb WHERE faculty_id = $faculty_id";
        mysqli_query($con, $deleteDepartmentsQuery);

        // Insert new department-position pairs
        foreach ($departments as $index => $dept_id) {
            $position_id = $positions[$index]; // Position ID selected for this department
            $addDepartment_query = "INSERT INTO dept_pos_facultytb (faculty_id, dept_id, position_id) 
                                    VALUES ('$faculty_id', '$dept_id', '$position_id')";
            if (!mysqli_query($con, $addDepartment_query)) {
                $_SESSION['error'] = "Failed to link faculty to department: " . mysqli_error($con);
                header("Location: facultyDetails.php?id=$faculty_id");
                exit();
            }
        }

        $_SESSION['success'] = "✔ Faculty member updated successfully!";
        header("Location: facultyMember.php?id=$faculty_id");
        exit();
    } else {
        $_SESSION['error'] = "Updating Faculty member failed: " . mysqli_error($con);
        header("Location: facultyMember.php?id=$faculty_id");
        exit();
    }
}

?>

<link rel="stylesheet" href="assets/css/style.css">

<!-- Error Alert -->
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show right-alert" id="error-alert" role="alert">
        <strong>Error!</strong> <?= $_SESSION['error']; ?>
    </div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<!-- Alert for Duplicate Department -->
<div id="duplicate-dept-alert" class="alert alert-danger alert-dismissible fade show right-alert" style="display: none;" role="alert">
    <strong>Warning!</strong> This department has already been selected!
</div>

<div class="container">
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

                            <!-- Number of Departments -->
                            <div class="col-md-6 mb-3">
                                <div class="form-group">
                                    <label for="number_of_depts" class="form-label">Number of Departments Assigned</label>
                                    <input type="number" class="form-control" placeholder="Enter number of departments" name="number_of_depts" id="number_of_depts" value="<?= count($positions) ?>" min="1" required>
                                </div>
                            </div>

                            <!-- Button to Set the Number of Departments -->
                            <div class="col-md-6 mt-4 mb-3">
                                <button type="button" class="btn BlueBtn" id="addDeptBtn">Set Number of Departments</button>
                            </div>

                            <!-- Department and Position -->
                            <div id="position-department-container">
                                <?php
                                // Render department-position sets based on the existing data
                                foreach ($positions as $i => $positionData) {
                                    $selectedDeptId = $positionData['dept_id'];
                                    $selectedPositionId = $positionData['position_id'];
                                ?>
                                    <div class="row mb-3 position-department-set">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="department" class="form-label">Department</label>
                                                <select class="form-control department-dropdown" name="departments[]">
                                                    <option value="">Select Department</option>
                                                    <?php
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

                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="position" class="form-label">Position</label>
                                                <select class="form-control" name="positions[]">
                                                    <option value="">Select Position</option>
                                                    <?php
                                                    // Populate positions dropdown
                                                    if ($selectedDeptId) {
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
                                    </div>
                                <?php } ?>
                            </div>
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

                            <!-- Save Button -->
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
    var numDepts = document.getElementById("number_of_depts").value;
    var container = document.getElementById("position-department-container");
    var currentDeptCount = container.getElementsByClassName("position-department-set").length;

    if (numDepts > currentDeptCount) {
        // Add new department-position fields
        for (var i = currentDeptCount; i < numDepts; i++) {
            var deptPosDiv = document.createElement("div");
            deptPosDiv.classList.add("row", "mb-3", "position-department-set");

            // Department Dropdown
            var deptDiv = document.createElement("div");
            deptDiv.classList.add("col-md-6");
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
            posDiv.classList.add("col-md-6");
            posDiv.innerHTML = `
                <div class="form-group">
                    <label for="position" class="form-label">Position</label>
                    <select class="form-control" name="positions[]">
                        <option value="">Select Position</option>
                    </select>
                </div>
            `;
            deptPosDiv.appendChild(posDiv);

            container.appendChild(deptPosDiv);
        }
        populateDepartments();
    } else if (numDepts < currentDeptCount) {
        // Remove extra fields if number is reduced
        for (var i = currentDeptCount - 1; i >= numDepts; i--) {
            container.removeChild(container.lastElementChild);
        }
    }
});

function populateDepartments() {
    var departmentDropdowns = document.getElementsByClassName("department-dropdown");

    for (var i = 0; i < departmentDropdowns.length; i++) {
        var dropdown = departmentDropdowns[i];
        var currentValue = dropdown.value;

        dropdown.innerHTML = '<option value="">Select Department</option>';
        <?php
        $departmentresultSet->data_seek(0);
        while ($row = $departmentresultSet->fetch_assoc()) {
            $department_id = $row['dept_id'];
            $department_name = $row['name'];
        ?>
            dropdown.innerHTML += `<option value="<?= $department_id ?>"><?= $department_name ?></option>`;
        <?php } ?>

        dropdown.value = currentValue;
    }
}

document.addEventListener("change", function (event) {
    if (event.target.classList.contains("department-dropdown")) {
        var deptId = event.target.value;
        var positionDropdown = event.target.closest(".position-department-set").querySelector('.form-control[name="positions[]"]');
        fetchPositions(deptId, positionDropdown);

        // Prevent selecting duplicate department
        var departmentDropdowns = document.getElementsByClassName("department-dropdown");
        var departmentIds = [];
        var duplicateFound = false;

        for (var i = 0; i < departmentDropdowns.length; i++) {
            var selectedDept = departmentDropdowns[i].value;
            if (selectedDept && departmentIds.includes(selectedDept)) {
                duplicateFound = true;
                break;
            }
            departmentIds.push(selectedDept);
        }

        if (duplicateFound) {
            // Show the duplicate department alert
            document.getElementById('duplicate-dept-alert').style.display = 'block';
            event.target.value = ""; // Clear the duplicate selection
        } else {
            // Hide the alert if no duplicate is found
            document.getElementById('duplicate-dept-alert').style.display = 'none';
        }
    }
});

function fetchPositions(deptId, positionDropdown) {
    if (deptId) {
        var xhr = new XMLHttpRequest();
        xhr.open("GET", "get_positions.php?dept_id=" + deptId, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                var positions = JSON.parse(xhr.responseText);
                positionDropdown.innerHTML = '<option value="">Select Position</option>';
                positions.forEach(function(position) {
                    var option = document.createElement("option");
                    option.value = position.position_id;
                    option.textContent = position.position_name;
                    positionDropdown.appendChild(option);
                });
            }
        };
        xhr.send();
    } else {
        positionDropdown.innerHTML = '<option value="">Select Position</option>';
    }
}

// Show alert for 3 seconds and hide it
window.onload = function() {
    var alert = document.getElementById("error-alert");
    if (alert) {
        setTimeout(function() {
            alert.style.display = "none";
        }, 3000); // Hide after 3 seconds
    }
};
</script>

<?php include('includes/footer.php'); ?>
