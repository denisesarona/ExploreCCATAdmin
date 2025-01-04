<?php 
include('includes/header.php');
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');
$positionresultSet = getData("positiontb");
$departmentresultSet = getData("departmenttb");
?>
<link rel="stylesheet" href="assets/css/style.css">
<!--------------- VIEW BUILDING INFORMATION DETAILS PAGE --------------->

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
                        <h3>BUILDING DETAILS</h3>
                        <div class="card-body">
                                <div class="row" style="font-family: 'Poppins', sans-serif;">
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <label for="">Building Name</label>
                                            <input type="text" value="<?=$data['building_name']; ?>" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <label for="">Building Description</label>
                                            <textarea rows="5" class="form-control" disabled><?= htmlspecialchars($data['building_description']); ?></textarea>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label for="">Department/Office (For Organizational Chart)</label>
                                        <div class="row">
                                            <?php 
                                            // Split the department names by comma
                                            $depts = explode(",", $data['department_name']);

                                            // Check if the first entry is blank and skip it
                                            foreach ($depts as $dept): 
                                                $deptName = htmlspecialchars(trim($dept)); // Clean and trim the department name

                                                // Skip blank entries
                                                if (empty($deptName)) continue;
                                            ?>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-control" style="background-color: #e9ecef; border: 1px solid #ced4da; color: #495057; opacity: 1; padding: 10px;">
                                                    <?= $deptName; ?>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        <label for="">Offices</label>
                                        <div class="row">
                                            <?php 
                                            // Split the office names by comma
                                            $offices = explode(",", $data['key_features']);

                                            foreach ($offices as $office): 
                                                $officeName = htmlspecialchars(trim($office)); // Clean and trim the office name
                                            ?>
                                            <div class="col-md-12 mb-2">
                                                <div class="form-control" style="background-color: #e9ecef; border: 1px solid #ced4da; color: #495057; opacity: 1; padding: 10px;">
                                                    <?= $officeName; ?>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
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
<!--------------- FOOTER --------------->
<?php include('includes/footer.php');?>
