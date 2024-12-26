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
                                        <div class="form-group">
                                            <label for="">Department/Office (For Organizational Chart)</label>
                                            <select class="form-control" name="department_name" id="department" onchange="updateDeptId()">
                                                <?php
                                                $current_department = $data['department_name'];
                                                ?>
                                                <option value='<?=$current_department?>' selected><?=$current_department?></option>
                                                <?php
                                                    while ($rows = $departmentresultSet->fetch_assoc()) {
                                                        $department_name = $rows['name'];
                                                        $dept_id = $rows['dept_id'];                                             // Set the option value to dept_id but display department name
                                                        echo "<option value='$department_name' data-dept-id='$dept_id'>$department_name</option>";
                                                    }
                                                ?>
                                            </select>
                                            <input type="hidden" name="dept_id" id="dept_id">
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
                                                    placeholder="Enter Office Name">
                                                <button 
                                                    type="button" 
                                                    class="btn btn-success" 
                                                    onclick="addOffice()">Add Office</button>
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
function updateDeptId() {
    var departmentSelect = document.getElementById('department');
    var deptIdInput = document.getElementById('dept_id');
    var selectedOption = departmentSelect.options[departmentSelect.selectedIndex];
    deptIdInput.value = selectedOption.getAttribute('data-dept-id'); // Get the dept ID from data attribute
}
</script>
<script>
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
