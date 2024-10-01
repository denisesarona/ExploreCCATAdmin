<?php 
include('includes/header.php');
include('../functions/queries.php');
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
                                    <div class="col-md-6 mb-3"> 
                                        <div class="form-group">
                                            <input type="hidden" name="building_id" value="<?=$data['building_id']; ?>"> <!-- Use the correct column name -->
                                            <label for="">Building Name</label>
                                            <input type="text" value="<?=$data['building_name']; ?>" class="form-control" placeholder="Enter Name" name="building_name" id="name">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3"> 
                                        <div class="form-group">
                                            <label for="">Department</label>
                                            <select class="form-control" name="department_name" id="department" onchange="updateDeptId()">
                                                <?php
                                                $current_department = $data['department_name'];
                                                ?>
                                                <option value='<?=$current_department?>' selected><?=$current_department?></option>
                                                <?php
                                                    while ($rows = $departmentresultSet->fetch_assoc()) {
                                                        $department_name = $rows['name'];
                                                        $dept_id = $rows['dept_id'];
                                                        // Set the option value to dept_id but display department name
                                                        echo "<option value='$department_name' data-dept-id='$dept_id'>$department_name</option>";
                                                    }
                                                ?>
                                            </select>
                                            <input type="hidden" name="dept_id" id="dept_id">
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <input type="hidden" name="building_id" value="<?=$data['building_id']; ?>"> <!-- Use the correct column name -->
                                            <label for="">Building Description</label>
                                            <textarea class="form-control" placeholder="Enter Building Description" name="building_description"><?= htmlspecialchars($data['building_description']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3"> 
                                        <div class="form-group">
                                            <input type="hidden" name="building_id" value="<?=$data['building_id']; ?>"> <!-- Use the correct column name -->
                                            <label for="">No. of Floors</label>
                                            <input type="number" value="<?=$data['no_floors']; ?>" class="form-control" placeholder="Enter No. of Floors" name="no_floors">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3"> 
                                        <div class="form-group">
                                            <input type="hidden" name="building_id" value="<?=$data['building_id']; ?>"> <!-- Use the correct column name -->
                                            <label for="">Key Features</label>
                                            <input type="text" value="<?=$data['key_features']; ?>" class="form-control" placeholder="Enter Key Features" name="key_features">
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3"> 
                                        <div class="form-group">
                                            <input type="hidden" name="building_id" value="<?=$data['building_id']; ?>"> <!-- Use the correct column name -->
                                            <label for="">Amenities</label>
                                            <input type="text" value="<?=$data['amenities_name']; ?>" class="form-control" placeholder="Enter Amenities" name="amenities_name">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3"> 
                                        <div class="form-group">
                                            <input type="hidden" name="building_id" value="<?=$data['building_id']; ?>"> <!-- Use the correct column name -->
                                            <label for="">Independent Amenity</label>
                                            <input type="checkbox" <?= $data['is_amenities'] ? "checked":""?> class="form-check-input" name="is_amenities">
                                        </div>
                                    </div>
                                    <div class="col-md-3 mb-3"> 
                                        <div class="form-group">
                                            <input type="hidden" name="building_id" value="<?=$data['building_id']; ?>"> <!-- Use the correct column name -->
                                            <label for="">Department Affiliation</label>
                                            <input type="checkbox" <?= $data['is_department'] ? "checked":""?> class="form-check-input" name="is_department">
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
<!--------------- FOOTER --------------->

<?php include('includes/footer.php');?>
