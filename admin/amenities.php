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
                                <a class="main-link" href="buildings.php">Building Information</a>
                            </div>
                            <div class="links col-md-6 mt-2">
                                <a class="main-link active" href="amenities.php">Amenities Information</a>
                            </div>

                            </div>
                        </div>
                    </div>
                    <table class="table text-center">
                        <thead>
                            <tr>
                                <th class="d-table-cell d-lg-table-cell">Amenities Name</th>
                                <th class="d-table-cell d-lg-table-cell">View Details</th>
                                <th class="d-table-cell d-lg-table-cell">Edit Details</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $amenitys = getData("amenities"); // Fetch buildings from database
                            if(mysqli_num_rows($amenitys) > 0){
                                foreach($amenitys as $amenity){
                        ?>
                                <tr>
                                    <td><?= $amenity['amenities_name']; ?></td>
                                    <td>
                                        <a href="amenitiesinfo.php?id=<?= $amenity['amenities_id']; ?>" class="btn BlueBtn">View</a>
                                    </td>
                                    <td>
                                        <a href="amenitiesinfoEdit.php?id=<?= $amenity['amenities_id']; ?>" class="btn BlueBtn">Edit</a>
                                    </td>
                                </tr>
                        <?php
                                }
                            } else {
                        ?>
                                <tr>
                                    <td colspan="4">No amenities records found</td>
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

