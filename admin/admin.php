<?php 
    include('includes/header.php');
    include('../functions/queries.php');
    include('../middleware/adminMiddleware.php');
?>
<link rel="stylesheet" href="assets/css/style.css">

<!--------------- ADMINS PAGE --------------->
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <div class="card-header d-flex justify-content-center align-items-center">
                    <h4 style="font-family: 'Poppins', sans-serif; font-size: 32px; color:#064918">ADMINS</h4>
                </div>
                <div class="card-body">
                    <!--------------- ADMIN TABLE --------------->
                    <table class="table text-center">
                        <thead>
                            <tr style="text-align: center; vertical-align: middle;">
                                <th class="d-none d-lg-table-cell">ID</th>
                                <th class="d-table-cell d-lg-table-cell">Name</th>
                                <th class="d-table-cell d-lg-table-cell">Role</th>
                                <th class="d-none d-lg-table-cell">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $users = getData("users"); // FUNCTION TO FETCH ADMIN DATA FROM THE DATABASE
                                if(mysqli_num_rows($users) > 0){ // CHECK IF THERE ARE ANY ADMIN
                                    foreach($users as $item){ // ITERATE THROUGH EACH ADMIN
                                        $user_id = $item['user_id'];
                                        // FETCH CURRENT ROLE FROM DATABASE
                                        $query = "SELECT role FROM users WHERE user_id = $user_id";
                                        $result = mysqli_query($con, $query);

                                        if ($result && mysqli_num_rows($result) > 0) {
                                            $row = mysqli_fetch_assoc($result);
                                            $current_role = $row['role'];
                                        } else {
                                            $current_role = null; 
                                        }

                                        $roleOptions = [
                                            1 => 'Admin'
                                        ];
                            ?>
                                        <tr style="text-align: center; vertical-align: middle;">
                                            <td name="user_id" class="d-none d-lg-table-cell"><?= $item['user_id']; ?></td>
                                            <td><?= $item['name']; ?></td>
                                            <td><?= $item['role']; ?></td>
                                            <td class="d-none d-lg-table-cell">
                                                <form action="codes.php" method="POST">
                                                    <input type="hidden" name="user_id" value="<?= $item['user_id'];?>">
                                                    <button type="submit" class="btn RedBtn" style="margin-top: 10px;" name="deleteUser_button">Delete</button>
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

