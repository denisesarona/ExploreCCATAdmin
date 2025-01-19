<?php 
    include('includes/header.php');
    include('../functions/queries.php');
    include('../middleware/adminMiddleware.php');
?>
<link rel="stylesheet" href="assets/css/style.css">

<!--------------- ORGANIZATIONAL CHART PAGE --------------->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3>DEPARTMENTS/OFFICES ORGANIZATIONAL CHARTS</h3>
                <div class="card-body">
                    <table class="table text-center">
                        <thead>
                            <tr style="text-align: center; vertical-align: middle;">
                                <th class="d-table-cell d-lg-table-cell">Name</th>
                                <th class="d-table-cell d-lg-table-cell">View</th>
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
                                                <a href="charts.php?id=<?= $item['dept_id']; ?>" style="margin-top: 10px;" class="btn BlueBtn">View Details</a>
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

