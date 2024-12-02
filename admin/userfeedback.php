<?php 
    include('includes/header.php');
    include('../functions/queries.php');
    include('../middleware/adminMiddleware.php');
?>
<link rel="stylesheet" href="assets/css/style.css">

<!--------------- FACULTY POSITION PAGE --------------->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3>USER FEEDBACK</h3>
                <div class="card-body">
                    <hr style="border-bottom: 1px solid #000;">
                    <table class="table text-center">
                        <thead>
                            <tr style="text-align: center; vertical-align: middle;">
                                <th class="d-table-cell d-lg-table-cell">User Number</th>
                                <th class="d-table-cell d-lg-table-cell">Rating</th>
                                <th class="d-table-cell d-lg-table-cell">Comment</th>
                                <th class="d-table-cell d-lg-table-cell">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $feedback = getData("feedbacktbl"); // FUNCTION TO FETCH DATA FROM THE DATABASE
                                if(mysqli_num_rows($feedback) > 0){ // CHECK IF THERE ARE ANY 
                                    foreach($feedback as $item){ // ITERATE THROUGH EACH POSITION
                            ?>
                                        <tr style="text-align: center; vertical-align: middle;">
                                            <td><?= $item['fid']; ?></td>
                                            <td><?= $item['rating']; ?></td>
                                            <td><?= $item['feedback_text']; ?></td>
                                            <td>
                                                <form action="codes.php" method="POST">
                                                    <input type="hidden" name="fid" value="<?= $item['fid'];?>">
                                                    <button type="submit" class="btn RedBtn" style="margin-top: 10px;" name="deleteFeedback_button">Delete</button>
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

