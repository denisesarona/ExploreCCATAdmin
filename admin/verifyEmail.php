<?php 
include('includes/header.php');
include('../functions/queries.php');
?>
<link rel="stylesheet" href="assets/css/style.css">

<!--------------- VERIFICATION PAGE --------------->
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card mt-5">
                <div class="card-body py-5">
                    <form action="codes.php" method="POST">
                        <h1 style="font-size: 28px" class="text-center">Verify Email</h1>
                        <div class="word text-center">
                            <p>Enter your code to verify</p>
                        </div>
                        <div class="input-box mb-3">
                            <input type="text" class="form-control" placeholder="Enter Code" name="code" required>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" name="emailVerify_button" class="btn BlueBtn w-100">SUBMIT</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<!--------------- FOOTER ---------------> 
<?php include('includes/footer.php'); ?>
