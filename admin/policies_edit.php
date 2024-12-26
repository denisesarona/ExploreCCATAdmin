<?php 
include('includes/header.php');
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');
?>
<link rel="stylesheet" href="assets/css/style.css">
<!--------------- EDIT BUILDING INFORMATION DETAILS PAGE --------------->

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            if (isset($_GET['id'])) {
                $id = $_GET['id']; // Capture the ID from the URL
                $policy = getPolByID('policies', $id); // Fetch record from database

                if (mysqli_num_rows($policy) > 0) {
                    $data = mysqli_fetch_array($policy);
            ?>  
                    <div class="card mt-5">
                        <h3>EDIT POLICIES & VISION DETAILS</h3>
                        <div class="card-body">
                            <!--------------- FORM --------------->
                            <form action="codes.php" method="POST" enctype="multipart/form-data">
                                <div class="row" style="font-family: 'Poppins', sans-serif;">
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <label for="">Name</label>
                                            <input type="text" value="<?=$data['name']; ?>" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <input type="hidden" name="pol_id" value="<?=$data['pol_id']; ?>"> <!-- Use the correct column name -->
                                            <label for="">Description</label>
                                            <textarea rows="5" class="form-control" placeholder="Enter Description" name="pol_text"><?= htmlspecialchars($data['pol_text']); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                    <!--------------- SAVE BUTTON --------------->
                                    <div class="col-md-6">
                                        <button type="submit" class="btn BlueBtn mt-2" name="editPolinfo_button">Update</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
            <?php
                } else {
                    echo "Information not found.";
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
