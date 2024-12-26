<?php 
include('includes/header.php');
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');

    /*--------------- GET ALL DATA FROM TABLE BY ID ---------------*/
    function getAmenityByID($table, $id) {
        global $con;
        $stmt = $con->prepare("SELECT * FROM $table WHERE amenities_id = ?"); 
        $stmt->bind_param("i", $id); 
        $stmt->execute();
        return $stmt->get_result();
    }
    
?>


<link rel="stylesheet" href="assets/css/style.css">
<!--------------- VIEW BUILDING INFORMATION DETAILS PAGE --------------->

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <?php
            if (isset($_GET['id'])) {
                $id = $_GET['id']; // Capture the ID from the URL
                $amenity = getAmenityByID('amenities', $id); // Fetch record from database

                if (mysqli_num_rows($amenity) > 0) {
                    $data = mysqli_fetch_array($amenity);
            ?>  
                    <div class="card mt-5">
                        <h3>AMENITY DETAILS</h3>
                        <div class="card-body">
                                <div class="row" style="font-family: 'Poppins', sans-serif;">
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <label for="">Amenity Name</label>
                                            <input type="text" value="<?=$data['amenities_name']; ?>" class="form-control" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-12 mb-3"> 
                                        <div class="form-group">
                                            <label for="">Amenities Description</label>
                                            <textarea rows="5" class="form-control" disabled><?= htmlspecialchars($data['amenities_description']); ?></textarea>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
            <?php
                } else {
                    echo "Amenity not found.";
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
