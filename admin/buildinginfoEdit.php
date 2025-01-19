<?php 
include('includes/header.php');
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');
$positionresultSet = getData("positiontb");
$departmentresultSet = getData("departmenttb");
?>
<link rel="stylesheet" href="assets/css/style.css">
<!--------------- EDIT BUILDING INFORMATION DETAILS PAGE --------------->

<div class="container">
                <!-- Hidden alert for duplicate department -->
                <div id="alertDuplicate" class="alert alert-danger alert-dismissible fade show" role="alert" style="display: none;">
                <strong>Warning!</strong> This department is already added.
            </div>
    <div class="row">
        <div class="col-md-12">

            <?php
            if (isset($_GET['id'])) {
                $id = $_GET['id']; // Capture the ID from the URL
                $bldg = getBldgByID('buildingtbl', $id); // Fetch record from database

                if (mysqli_num_rows($bldg) > 0) {
                    $data = mysqli_fetch_array($bldg);
            ?>  
                    <div class="card mt-5">
                        <h3>EDIT BUILDING DETAILS</h3>
                        <div class="card-body">
                            <!--------------- FORM --------------->
                            <form action="codes.php" method="POST" enctype="multipart/form-data">
                                <div class="row" style="font-family: 'Poppins', sans-serif;">
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <label for="">Building Name</label>
                                            <input type="text" value="<?=$data['building_name']; ?>" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <input type="hidden" name="building_id" value="<?=$data['building_id']; ?>"> <!-- Use the correct column name -->
                                            <label for="">Building Description</label>
                                            <textarea rows="5" class="form-control" placeholder="Enter Building Description" name="building_description"><?= htmlspecialchars($data['building_description']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3"> 
<!-- Departments/Offices (For Organizational Chart) -->
<div class="col-md-12 mb-3"> 
    <div class="form-group">
        <label for="">Departments/Offices (For Organizational Chart)</label>
        
        <!-- Wrapper for input and button -->
        <div class="input-group">
            <select id="departmentSelect" class="form-control" style="margin-right:30px;">
                <option value="" disabled selected>Select a Department</option>
                <?php
                while ($rows = $departmentresultSet->fetch_assoc()) {
                    $department_name = $rows['name'];
                    $dept_id = $rows['dept_id'];
                    echo "<option value='$department_name' data-dept-id='$dept_id'>$department_name</option>";
                }
                ?>
            </select>
            <button type="button" class="btn btn-success" onclick="addDepartment()" style="border-radius: 0.375rem;">Add</button>
        </div>
        
        <!-- Display added departments dynamically -->
        <ul id="departmentList" class="list-group mt-3">
            <?php
            // Pre-fill the list with existing data from the database
            $departments = explode(',', $data['department_name']); // Assuming 'department_name' contains comma-separated values
            $dept_ids = explode(',', $data['dept_id']); // Assuming 'dept_id' contains comma-separated values
            for ($i = 0; $i < count($departments); $i++) {
                $department = trim($departments[$i]);
                $dept_id = trim($dept_ids[$i]);
                if (!empty($department)) {
                    echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                            <span data-dept-id='$dept_id'>$department</span>
                            <button type='button' class='btn btn-sm btn-danger' onclick='removeDepartment(this)'>Remove</button>
                        </li>";
                }
            }
            ?>
        </ul>
        
        <!-- Hidden inputs to store the list of department names and IDs -->
        <input 
            type="hidden" 
            id="departments" 
            name="department_name" 
            value="<?= htmlspecialchars($data['department_name']); ?>">
        <input 
            type="hidden" 
            id="dept_ids" 
            name="dept_id" 
            value="<?= htmlspecialchars($data['dept_id']); ?>">
    </div>
</div>



                                    <div class="col-md-12 mb-3">
                                        <div class="form-group">
                                            <input type="hidden" name="building_id" value="<?=$data['building_id']; ?>"> <!-- Hidden building ID -->
                                            
                                            <label for="">Offices</label>
                                            
                                            <!-- Wrapper for input and button -->
                                            <div class="input-group">
                                                <input 
                                                    type="text" 
                                                    id="officeInput" 
                                                    class="form-control" 
                                                    placeholder="Enter Office Name" style="margin-right:30px;">
                                                <button 
                                                    type="button" 
                                                    class="btn btn-success" 
                                                    onclick="addOffice()" style="border-radius: 0.375rem;">Add</button>
                                            </div>

                                            <!-- Display added offices dynamically -->
                                            <ul id="officeList" class="list-group mt-3">
                                                <?php
                                                // Pre-fill the list with existing data from the database
                                                $offices = explode(',', $data['key_features']); // Assuming 'key_features' contains comma-separated values
                                                foreach ($offices as $office) {
                                                    if (!empty(trim($office))) {
                                                        echo "<li class='list-group-item d-flex justify-content-between align-items-center'>
                                                                $office 
                                                                <button type='button' class='btn btn-sm btn-danger' onclick='removeOffice(this)'>Remove</button>
                                                            </li>";
                                                    }
                                                }
                                                ?>
                                            </ul>

                                            <!-- Hidden input to store the list of offices -->
                                            <input 
                                                type="hidden" 
                                                id="offices" 
                                                name="key_features" 
                                                value="<?= htmlspecialchars($data['key_features']); ?>">
                                        </div>
                                    </div>
                                    <!--------------- SAVE BUTTON --------------->
                                    <div class="col-md-6">
                                        <button type="submit" class="btn BlueBtn mt-2" name="editBldginfo_button">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
            <?php
                } else {
                    echo "Building not found.";
                }
            } else {
                echo "ID missing from URL.";
            }
            ?>
        </div>
    </div>
</div>
<script>
function addDepartment() {
    const departmentSelect = document.getElementById("departmentSelect");
    const departmentList = document.getElementById("departmentList");
    const hiddenDepartments = document.getElementById("departments");
    const hiddenDeptIds = document.getElementById("dept_ids");
    const alertDuplicate = document.getElementById("alertDuplicate");

    const selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
    const departmentName = selectedOption.value;
    const deptId = selectedOption.getAttribute("data-dept-id");

    // Hide the alert whenever a department is selected or added
    alertDuplicate.style.display = 'none';

    if (departmentName && deptId) {
        // Prevent duplicate entries
        const existingDepartments = hiddenDepartments.value.split(",").map(d => d.trim());
        const existingDeptIds = hiddenDeptIds.value.split(",").map(id => id.trim());
        if (existingDepartments.includes(departmentName)) {
            // Show the alert if duplicate is found
            alertDuplicate.style.display = 'block';

            // Automatically hide the alert after 3 seconds
            setTimeout(function () {
                alertDuplicate.style.display = 'none';
            }, 3000);
            return;
        }

        // Create a new list item
        const listItem = document.createElement("li");
        listItem.innerHTML = ` 
            <span data-dept-id="${deptId}">${departmentName}</span>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeDepartment(this)">Remove</button>
        `;

        // Add the list item to the list
        departmentList.appendChild(listItem);

        // Add the new department name and ID to the hidden inputs
        hiddenDepartments.value = existingDepartments.concat(departmentName).join(",");
        hiddenDeptIds.value = existingDeptIds.concat(deptId).join(",");

        // Reset the select dropdown
        departmentSelect.selectedIndex = 0;
    }
}


function removeDepartment(button) {
    const listItem = button.parentElement;
    const departmentList = document.getElementById("departmentList");
    const hiddenDepartments = document.getElementById("departments");
    const hiddenDeptIds = document.getElementById("dept_ids");

    const departmentName = listItem.querySelector("span").textContent.trim();
    const deptId = listItem.querySelector("span").getAttribute("data-dept-id");

    // Remove the list item from the list
    departmentList.removeChild(listItem);

    // Update the hidden inputs
    const remainingDepartments = Array.from(departmentList.children).map(
        li => li.querySelector("span").textContent.trim()
    );
    const remainingDeptIds = Array.from(departmentList.children).map(
        li => li.querySelector("span").getAttribute("data-dept-id")
    );

    hiddenDepartments.value = remainingDepartments.join(",");
    hiddenDeptIds.value = remainingDeptIds.join(",");
}


</script>
<script>
    function updateDeptIds() {
    const selectedOptions = Array.from(document.getElementById('department').selectedOptions);
    const deptIds = selectedOptions.map(option => option.value).join(',');
    document.getElementById('dept_ids').value = deptIds;
}

function addOffice() {
    const officeInput = document.getElementById("officeInput");
    const officeList = document.getElementById("officeList");
    const hiddenInput = document.getElementById("offices");

    const officeName = officeInput.value.trim();
    if (officeName) {
        // Create a new list item
        const listItem = document.createElement("li");
        listItem.innerHTML = `
            ${officeName} 
            <button type="button" class="btn btn-sm btn-danger" onclick="removeOffice(this)">Remove</button>
        `;

        // Add the list item to the list
        officeList.appendChild(listItem);

        // Add the new office name to the hidden input (comma-separated)
        const currentValue = hiddenInput.value;
        hiddenInput.value = currentValue ? currentValue + "," + officeName : officeName;

        // Clear the input field
        officeInput.value = "";
    }
}

function removeOffice(button) {
    const listItem = button.parentElement;
    const officeList = document.getElementById("officeList");
    const hiddenInput = document.getElementById("offices");

    // Remove the list item from the list
    officeList.removeChild(listItem);

    // Update the hidden input value
    const remainingOffices = Array.from(officeList.children).map(
        li => li.firstChild.textContent.trim()
    );
    hiddenInput.value = remainingOffices.join(",");
}
</script>
<!--------------- FOOTER --------------->
<?php include('includes/footer.php');?>
