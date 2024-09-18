<?php 
    include('includes/header.php');
    include('../functions/queries.php');
?>
<link rel="stylesheet" href="assets/css/style.css">

<!--------------- FACULTY POSITION PAGE --------------->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-header d-flex justify-content-center align-items-center">
                    <h4 style="font-family: 'Poppins', sans-serif; font-size: 32px; color:#064918">FACULTY POSITIONS</h4>
                </div>
                <div class="card-body">
                    <!--------------- FACULTY POSITION TABLE --------------->
                    <form action="codes.php" method="POST">
                        <div class="row mb-3"> 
                            <div class="col-md-10"> 
                                <div class="form-group">
                                    <label for="position" class="form-label">Faculty Position</label>
                                    <input type="text" class="form-control" placeholder="Enter Position" name="name" required>
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
                    <table class="table text-center">
                        <thead>
                            <tr style="text-align: center; vertical-align: middle;">
                                <th class="d-table-cell d-lg-table-cell">Name</th>
                                <th class="d-table-cell d-lg-table-cell">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $position = getData("positiontb"); // FUNCTION TO FETCH DATA FROM THE DATABASE
                                if(mysqli_num_rows($position) > 0){ // CHECK IF THERE ARE ANY 
                                    foreach($position as $item){ // ITERATE THROUGH EACH POSITION
                            ?>
                                        <tr style="text-align: center; vertical-align: middle;">
                                            <td><?= $item['name']; ?></td>
                                            <td>
                                                <form action="codes.php" method="POST">
                                                    <input type="hidden" name="position_id" value="<?= $item['position_id'];?>">
                                                    <button type="submit" class="btn RedBtn" style="margin-top: 10px;" name="deletePosition_button">Delete</button>
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

