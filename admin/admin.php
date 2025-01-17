<?php 
include('includes/header.php');
include('../functions/queries.php');
include('../middleware/adminMiddleware.php');
?>
<link rel="stylesheet" href="assets/css/style.css">

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <!--------------- ADMIN LIST SECTION --------------->
            <div class="card mt-5">
                <div class="d-flex justify-content-between align-items-center px-3">
                    <h3 class="flex-grow-1 text-center mb-0">ADMINS</h3>
                    <a href="addAdmin.php" class="btn BlueBtn mt-3 mr-3" style="font-size: 20px;">+ Add Admin</a>
                </div>
                <div class="card-body">
                    <br>
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
                                $users = getData("users");
                                if(mysqli_num_rows($users) > 0){
                                    foreach($users as $item){
                                        ?>
                                        <tr style="text-align: center; vertical-align: middle;">
                                            <td class="d-none d-lg-table-cell"><?= $item['user_id']; ?></td>
                                            <td><?= $item['name']; ?></td>
                                            <td><?= $item['role']; ?></td>
                                            <td class="d-none d-lg-table-cell">
                                                <form action="codes.php" method="POST">
                                                    <input type="hidden" name="user_id" value="<?= $item['user_id'];?>">
                                                    <button type="submit" class="btn RedBtn" name="deleteUser_button">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                } else {
                                    ?>
                                    <tr>
                                        <td colspan="4"><br>No records found</td>
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

<?php include('includes/footer.php'); ?>
