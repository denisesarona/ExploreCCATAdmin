<?php 
    include('includes/header.php');
    include('../functions/queries.php');
    include('../middleware/adminMiddleware.php');
?>
<link rel="stylesheet" href="assets/css/style.css">
<!--------------- BUILDING AND AMENITIES PAGE --------------->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-4">
                    <h3>Buildings & Amenities</h4>
                <div class="card-body">
                    <!-- Navigation Links for Building and Amenities -->
                    <div class="col-md-12">
                        <div class="card rounded-3 p-1 text-center" style="border:none; overflow:hidden;">
                            <div class="row align-items-center options">
                            <div class="links col-md-6 mt-2">
                                <a class="main-link active" href="buildings.php">Building Information</a>
                            </div>
                            <div class="links col-md-6 mt-2">
                                <a class="main-link" href="amenities.php">Amenities Information</a>
                            </div>

                            </div>
                        </div>
                    </div>
                    <table class="table text-center">
                        <thead>
                            <tr>
                                <th class="d-table-cell d-lg-table-cell">Building Name</th>
                                <th class="d-table-cell d-lg-table-cell">View Details</th>
                                <th class="d-table-cell d-lg-table-cell">Edit Details</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $buildings = getData("buildingtbl"); // Fetch buildings from database
                            if(mysqli_num_rows($buildings) > 0){
                                foreach($buildings as $building){
                        ?>
                                <tr>
                                    <td><?= $building['building_name']; ?></td>
                                    <td>
                                        <a href="buildinginfo.php?id=<?= $building['building_id']; ?>" class="btn BlueBtn">View</a>
                                    </td>
                                    <td>
                                        <a href="buildinginfoEdit.php?id=<?= $building['building_id']; ?>" class="btn BlueBtn">Edit</a>
                                    </td>
                                </tr>
                        <?php
                                }
                            } else {
                        ?>
                                <tr>
                                    <td colspan="4">No building records found</td>
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

