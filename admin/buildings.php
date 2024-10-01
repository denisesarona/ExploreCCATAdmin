<?php 
    include('includes/header.php');
    include('../functions/queries.php');
?>
<link rel="stylesheet" href="assets/css/style.css">

<!--------------- BUILDING INFO PAGE --------------->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3>BUILDING INFORMATION</h3>
                <div class="card-body">
                    <table class="table text-center">
                        <thead>
                            <tr style="text-align: center; vertical-align: middle;">
                                <th class="d-table-cell d-lg-table-cell">Name</th>
                                <th class="d-table-cell d-lg-table-cell">View</th>
                                <th class="d-none d-lg-table-cell">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $bldg = getData("buildingtbl"); // FUNCTION TO FETCH DATA FROM THE DATABASE
                                if(mysqli_num_rows($bldg) > 0){ // CHECK IF THERE ARE ANY 
                                    foreach($bldg as $item){ // ITERATE THROUGH EACH DEPARTMENT
                            ?>
                                        <tr style="text-align: center; vertical-align: middle;">
                                            <td><?= $item['building_name']; ?></td>
                                            <td>
                                                <a href="buildinginfo.php?id=<?= $item['building_id']; ?>" style="margin-top: 10px;" class="btn BlueBtn">View Details</a>
                                            </td>
                                            <td>
                                                <a href="buildinginfoEdit.php?id=<?= $item['building_id']; ?>" style="margin-top: 10px;" class="btn BlueBtn">Edit Details</a>
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

