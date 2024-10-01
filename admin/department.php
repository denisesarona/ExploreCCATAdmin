<?php 
    include('includes/header.php');
    include('../functions/queries.php');
    include('../middleware/adminMiddleware.php');
?>
<link rel="stylesheet" href="assets/css/style.css">

<!--------------- DEPARTMENTS PAGE --------------->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3>DEPARTMENTS</h3>
                <div class="card-body">
                    <!--------------- DEPARTMENT TABLE --------------->
                    <form action="codes.php" method="POST">
                        <div class="row mb-3"> 
                            <div class="col-md-10"> 
                                <div class="form-group">
                                    <label for="department" class="form-label">Department Name</label>
                                    <input type="text" class="form-control" placeholder="Enter Department Name" name="dept_name" required>
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end"> 
                                <div class="form-group w-100"> 
                                    <button type="submit" class="btn BlueBtn mt-2" name="addDepartment_button">Save</button>
                                </div>
                            </div>
                        </div>
                    </form>
                    <hr style="border-bottom: 1px solid #000;">
                    <table class="table text-center">
                        <thead>
                            <tr style="text-align: center; vertical-align: middle;">
                                <th class="d-table-cell d-lg-table-cell">Name</th>
                                <th class="d-table-cell d-lg-table-cell">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $dept = getData("departmenttb"); // FUNCTION TO FETCH DATA FROM THE DATABASE
                                if(mysqli_num_rows($dept) > 0){ // CHECK IF THERE ARE ANY 
                                    foreach($dept as $item){ // ITERATE THROUGH EACH DEPARTMENT
                            ?>
                                        <tr style="text-align: center; vertical-align: middle;">
                                            <td><?= $item['name']; ?></td>
                                            <td>
                                                <form action="codes.php" method="POST">
                                                    <input type="hidden" name="dept_id" value="<?= $item['dept_id'];?>">
                                                    <button type="submit" class="btn RedBtn" style="margin-top: 10px;" name="deleteDepartment_button">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                <?php
                                        }
                                    } else {
                                ?>
                                        <tr>
                                            <td colspan="5"><br>No records found</td>
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
<!--------------- FOOTER --------------->
<?php include('includes/footer.php'); ?>

