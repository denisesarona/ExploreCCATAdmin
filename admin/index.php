<?php 
    include('includes/header.php');
    include('../config/dbconnect.php');
    include('../functions/queries.php');

    $totalAdmin = countItem($con, 'users'); 
    $totalFaculty = countItem($con, 'facultytb'); 
    $totalPosition = countItem($con, 'positiontb'); 
    $totalDepartment = countItem($con, 'departmenttb'); 
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3>ADMIN DASHBOARD</h3>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card mb-2">
                                <div class="card-header p-2 pt-2 bg-transparent">
                                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-xl mt-n4 position-absolute large-icon">
                                        <i class='bx bxs-user'></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total Admin</p>
                                        <h4 class="mb-0"><?php echo number_format($totalAdmin); ?></h4>
                                    </div>
                                </div>
                                <hr class="horizontal my-0 dark">
                                <div class="card-footer p-3">
                                    <p class="mb-0"><span class="text-muted text-sm">Total Registered Admin in the Database</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <div class="card mb-2">
                                <div class="card-header p-2 pt-2 bg-transparent">
                                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-xl mt-n4 position-absolute large-icon">
                                        <i class='bx bxs-group'></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total Faculty Members</p>
                                        <h4 class="mb-0"><?php echo number_format($totalFaculty); ?></h4>
                                    </div>
                                </div>
                                <hr class="horizontal my-0 dark">
                                <div class="card-footer p-3">
                                    <p class="mb-0"><span class="text-muted text-sm">Total Faculty Members in the Campus</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card mb-2">
                                <div class="card-header p-2 pt-2 bg-transparent">
                                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-xl mt-n4 position-absolute large-icon">
                                        <i class='bx bxs-user-detail'></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total Faculty Positions</p>
                                        <h4 class="mb-0"><?php echo number_format($totalPosition); ?></h4>
                                    </div>
                                </div>
                                <hr class="horizontal my-0 dark">
                                <div class="card-footer p-3">
                                    <p class="mb-0"><span class="text-muted text-sm">Total Faculty Positions in Departments</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card mb-2">
                                <div class="card-header p-2 pt-2 bg-transparent">
                                    <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark shadow text-center border-radius-xl mt-n4 position-absolute large-icon">
                                        <i class='bx bxs-building-house'></i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total Departments</p>
                                        <h4 class="mb-0"><?php echo number_format($totalDepartment); ?></h4>
                                    </div>
                                </div>
                                <hr class="horizontal my-0 dark">
                                <div class="card-footer p-3">
                                    <p class="mb-0"><span class="text-muted text-sm">Total Departments in Campus</span></p>
                                </div>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>

<!--------------- ALERTIFY JS ---------------> 
<?php include('includes/footer.php');?>
