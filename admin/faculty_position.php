<?php 
    include('includes/header.php');
    include('../functions/queries.php');
    include('../middleware/adminMiddleware.php');

    // Fetch all positions for a given department
    function getPositionsByDepartment($dept_id) {
        global $con;  // Database connection
        
        // SQL query to fetch all positions where the dept_id matches the given department ID
        $query = "SELECT * FROM positiontb WHERE dept_id = '$dept_id'";
        
        // Execute the query and return the result set
        return mysqli_query($con, $query);
    }

    // Fetch a single record based on a condition
    function getSingleRecord($table, $column, $value) {
        global $con;  // Assuming $con is your database connection
        $query = "SELECT * FROM $table WHERE $column = '$value' LIMIT 1";
        $result = mysqli_query($con, $query);

        // If the record is found, return it as an associative array
        if (mysqli_num_rows($result) > 0) {
            return mysqli_fetch_assoc($result);
        } else {
            return null;  // Return null if no record is found
        }
    }


    if (isset($_GET['dept_id'])) {
        $dept_id = $_GET['dept_id'];
        $department = getSingleRecord("departmenttb", "dept_id", $dept_id); // Fetch department details
    } else {
        echo "No department selected.";
        exit;
    }
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3>Manage Positions for <?= $department['name']; ?></h3>
                <div class="card-body">
                    <!--------------- ADD POSITION FORM --------------->                    
                    <form action="codes.php" method="POST">
                        <input type="hidden" name="dept_id" value="<?= $dept_id; ?>">
                        <div class="row mb-3">
                            <div class="col-md-10">
                                <div class="form-group">
                                    <label for="position" class="form-label">Position Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Position Name" name="position_name" required>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button type="submit" class="btn BlueBtn mt-2" name="addPosition_button">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <hr style="border-bottom: 1px solid #000;">
                    
                    <!--------------- POSITIONS TABLE --------------->                    
                    <table class="table text-center">
                        <thead>
                            <tr>
                                <th>Position Name</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $positions = getPositionsByDepartment($dept_id); // Function to fetch positions for this department
                                if(mysqli_num_rows($positions) > 0){
                                    foreach($positions as $position){
                            ?>
                                        <tr>
                                            <td><?= $position['position_name']; ?></td>
                                            <td>
                                                <form action="codes.php" method="POST">
                                                    <input type="hidden" name="position_id" value="<?= $position['position_id']; ?>">
                                                    <button type="submit" class="btn RedBtn" name="deletePosition_button">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                            <?php
                                    }
                                } else {
                            ?>
                                    <tr>
                                        <td colspan="2">No positions found</td>
                                    </tr>
                            <?php
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('includes/footer.php'); ?>
