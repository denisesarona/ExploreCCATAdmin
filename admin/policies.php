<?php 
    include('includes/header.php');
    include('../functions/queries.php');
    include('../middleware/adminMiddleware.php');
    // Function to ensure the fields (Mission, Vision, Quality Policy) exist in the policies table
    function ensureDefaultFieldsExist($name, $defaultValue) {
        global $con;

        // Check if the field already exists in the 'policies' table
        $sql = "SELECT * FROM policies WHERE name = ?";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("s", $name);
        $stmt->execute();
        $result = $stmt->get_result();

        // If the field doesn't exist, insert it with the default value
        if ($result->num_rows == 0) {
            $insertSql = "INSERT INTO policies (name, pol_text, created_at) VALUES (?, ?, NOW())";
            $insertStmt = $con->prepare($insertSql);
            $insertStmt->bind_param("ss", $name, $defaultValue);
            $insertStmt->execute();
        }

        $stmt->close();
    }

    // Ensure that Mission, Vision, and Quality Policy are present in the policies table
    ensureDefaultFieldsExist('Mission', 'This is the default Mission statement.');
    ensureDefaultFieldsExist('Vision', 'This is the default Vision statement.');
    ensureDefaultFieldsExist('Quality Policy', 'This is the default Quality Policy statement.');
    ensureDefaultFieldsExist('CvSU-CCAT Goals', 'This is the default CvSU-CCAT statement.');
?>
<link rel="stylesheet" href="assets/css/style.css">

<!--------------- BUILDING INFO PAGE --------------->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3>POLICIES & VISION</h3>
                <div class="card-body">
                    <table class="table text-center">
                        <thead>
                            <tr style="text-align: center; vertical-align: middle;">
                                <th class="d-table-cell d-lg-table-cell">Name</th>
                                <th class="d-table-cell d-lg-table-cell">Edit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $policy = getData("policies"); // FUNCTION TO FETCH DATA FROM THE DATABASE
                                if(mysqli_num_rows($policy) > 0){ // CHECK IF THERE ARE ANY 
                                    foreach($policy as $item){ // ITERATE THROUGH EACH DEPARTMENT
                            ?>
                                        <tr style="text-align: center; vertical-align: middle;">
                                            <td><?= $item['name']; ?></td>
                                            <td>
                                                <a href="policies_edit.php?id=<?= $item['pol_id']; ?>" style="margin-top: 10px;" class="btn BlueBtn">Edit Details</a>
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

