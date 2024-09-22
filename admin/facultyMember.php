<?php 
    include('includes/header.php');
    include('../functions/queries.php');
?>
<link rel="stylesheet" href="assets/css/style.css">

<!--------------- ADMINS PAGE --------------->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3>FACULTY MEMBERS</h3>
                <div class="card-body">
                    <!--------------- ADMIN TABLE --------------->
                    <table class="table text-center">
                        <thead>
                            <tr style="text-align: center; vertical-align: middle;">
                                <th class="d-none d-lg-table-cell">ID</th>
                                <th class="d-table-cell d-lg-table-cell">Name</th>
                                <th class="d-table-cell d-lg-table-cell">View Details</th>
                                <th class="d-none d-lg-table-cell">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $facultymember = getData("facultytb"); // FUNCTION TO FETCH ADMIN DATA FROM THE DATABASE
                                if(mysqli_num_rows($facultymember) > 0){ // CHECK IF THERE ARE ANY ADMIN
                                    foreach($facultymember as $item){ // ITERATE THROUGH EACH ADMIN
                            ?>
                                        <tr style="text-align: center; vertical-align: middle;">
                                            <td name="faculty_id" class="d-none d-lg-table-cell"><?= $item['faculty_id']; ?></td>
                                            <td><?= $item['name']; ?></td>
                                            <td>
                                                <a href="facultyDetails.php?id=<?= $item['faculty_id']; ?>" style="margin-top: 10px;" class="btn BlueBtn">View Details</a>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <form action="codes.php" method="POST">
                                                    <input type="hidden" name="faculty_id" value="<?= $item['faculty_id'];?>">
                                                    <button type="submit" class="btn RedBtn" style="margin-top: 10px;" name="deleteFaculty_button">Delete</button>
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

