<?php 
include('includes/header.php');
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');


    /*--------------- GET ALL DATA FROM TABLE BY ID ---------------*/
    function getAmenityByID($table, $id) {
        global $con;
        $stmt = $con->prepare("SELECT * FROM $table WHERE amenities_id = ?"); 
        $stmt->bind_param("i", $id); 
        $stmt->execute();
        return $stmt->get_result();
    }

?>
<link rel="stylesheet" href="assets/css/style.css">
<!--------------- EDIT BUILDING INFORMATION DETAILS PAGE --------------->

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            if (isset($_GET['id'])) {
                $id = $_GET['id']; // Capture the ID from the URL
                $amenity = getAmenityByID('amenities', $id); // Fetch record from database

                if (mysqli_num_rows($amenity) > 0) {
                    $data = mysqli_fetch_array($amenity);
            ?>  
                    <div class="card mt-5">
                        <h3>EDIT AMENITIES DETAILS</h3>
                        <div class="card-body">
                            <!--------------- FORM --------------->
                            <form action="codes.php" method="POST" enctype="multipart/form-data">
                                <div class="row" style="font-family: 'Poppins', sans-serif;">
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <label for="">Amenity Name</label>
                                            <input type="text" value="<?=$data['amenities_name']; ?>" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <input type="hidden" name="amenities_id" value="<?=$data['amenities_id']; ?>"> <!-- Use the correct column name -->
                                            <label for="">Amenity Description</label>
                                            <textarea rows="5" class="form-control" placeholder="Enter Amenity Description" name="amenities_description"><?= htmlspecialchars($data['amenities_description']); ?></textarea>
                                        </div>
                                    </div>
                                    <!--------------- SAVE BUTTON --------------->
                                    <div class="col-md-6">
                                        <button type="submit" class="btn BlueBtn mt-2" name="editAmenityinfo_button">Update</button>
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
