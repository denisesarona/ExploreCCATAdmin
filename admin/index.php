<?php 
    include('includes/header.php');
    include('../config/dbconnect.php');
    include('../functions/queries.php');
    include('../middleware/adminMiddleware.php');

    $totalAdmin = countItem($con, 'users'); 
    $totalFaculty = countItem($con, 'facultytb'); 
    $totalPosition = countItem($con, 'positiontb'); 
    $totalDepartment = countItem($con, 'departmenttb'); 

    // Fetch random feedback comments from the feedback table (1 to 4 random comments)
    $feedbacks = [];
    $result = mysqli_query($con, "SELECT feedback_text FROM feedbacktbl ORDER BY RAND() LIMIT 4");

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $feedbacks[] = $row['feedback_text'];  // Store feedback text in the array
        }
    }
    // Fetch the ratings data from the feedback table
    $ratingsData = [];
    $result = mysqli_query($con, "SELECT rating, COUNT(*) as count FROM feedbacktbl GROUP BY rating");

    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $ratingsData[$row['rating']] = $row['count'];
        }
    }

    // Prepare the ratings data for the JavaScript (default 0 if not found)
    $chartData = [
        isset($ratingsData[1]) ? $ratingsData[1] : 0,  // Rating 1 - Bad
        isset($ratingsData[2]) ? $ratingsData[2] : 0,  // Rating 2 - Poor
        isset($ratingsData[3]) ? $ratingsData[3] : 0,  // Rating 3 - Average
        isset($ratingsData[4]) ? $ratingsData[4] : 0,  // Rating 4 - Good
        isset($ratingsData[5]) ? $ratingsData[5] : 0   // Rating 5 - Great
    ];

    // Encode the chart data array as a JSON object for JavaScript
    $chartDataJson = json_encode($chartData);
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
                                        <p class="text-sm mb-0 text-capitalize">Total Faculty Members</p>
                                        <h4 class="mb-0"><?php echo number_format($totalFaculty); ?></h4>
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
                        <h3>USER FEEDBACK</h3>
                        <div>
                            <canvas id="feedbackChart"></canvas>
                        </div>
                        <div class="random-feedback mt-5">
                            <h4>User Random Comments</h4>
                            <div class="row">
                                <?php if (count($feedbacks) > 0): ?>
                                    <?php foreach ($feedbacks as $feedback): ?>
                                        <div class="col-md-6 col-sm-12 mb-3"> <!-- 2 columns on medium screens and 1 on small -->
                                            <div class="card">
                                                <div class="card-body">
                                                    <p class="card-text"><?php echo htmlspecialchars($feedback); ?></p>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No feedback available.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('feedbackChart');

    // Use the PHP-generated chart data
    const chartData = <?php echo $chartDataJson; ?>;

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['1 - Bad', '2 - Poor', '3 - Average', '4 - Good', '5 - Great'],
            hoverOffset: 4,
            datasets: [{
                label: '# of Ratings',
                data: chartData,  // Use the dynamically generated data
                borderWidth: 1
            }]
        },
        options: {
            responsive: true
        }
    });
</script>
 

<!--------------- ALERTIFY JS ---------------> 
<?php include('includes/footer.php');?>
