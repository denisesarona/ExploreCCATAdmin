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
                <h3>FACULTY MEMBERS</h3>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h4>Filter by Department/Offices: </h4>
                            <div class="btn-group" style="display: flex; overflow-x: auto; white-space: nowrap; padding: 10px 0;">
                                <?php
                                    // Fetch departments from the departmenttb table
                                    $departments = getDatas("departmenttb");
                                    if(mysqli_num_rows($departments) > 0) {
                                        while($department = mysqli_fetch_assoc($departments)) {
                                            echo "<a href='facultyMember.php?department_id=" . $department['dept_id'] . "' class='btn BlueBtn' style='margin-right: 10px;'>" . $department['name'] . "</a>";
                                        }
                                    } else {
                                        echo "No departments available";
                                    }
                                ?>
                            </div>
                        </div>
                    </div>

                    <!-- Display the active department name -->
                    <div class="row">
                        <div class="col-md-12" style="text-align: center;">
                            <?php
                                // Get department_id from the URL
                                $department_id = isset($_GET['department_id']) ? $_GET['department_id'] : null;

                                if ($department_id) {
                                    // Fetch the name of the selected department
                                    $sql = "SELECT name FROM departmenttb WHERE dept_id = $department_id";
                                    $result = getDataFromQuery($sql);

                                    if (mysqli_num_rows($result) > 0) {
                                        $department = mysqli_fetch_assoc($result);
                                        echo "<h5><br>Department/Offices: " . $department['name'] . "</h5>";
                                    }
                                }
                            ?>
                        </div>
                    </div>

                    <!--------------- ADMIN TABLE --------------->

                    <table class="table text-center">
                        <thead>
                            <tr style="text-align: center; vertical-align: middle;">
                                <th class="d-table-cell d-lg-table-cell">Name</th>
                                <th class="d-table-cell d-lg-table-cell">View Details</th>
                                <th class="d-none d-lg-table-cell">Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                // Handle faculty deletion if delete button is pressed
                                if (isset($_POST['deleteFaculty_button'])) {
                                    $faculty_id = $_POST['faculty_id'];  // Get faculty_id from form
                                    $dept_id = $_POST['dept_id'];  // Get dept_id from form

                                    // Check if the faculty member is assigned to any department
                                    $check_query = "SELECT * FROM dept_pos_facultytb WHERE faculty_id = ?";
                                    $check_stmt = $con->prepare($check_query);
                                    $check_stmt->bind_param("i", $faculty_id);
                                    $check_stmt->execute();
                                    $check_result = $check_stmt->get_result();

                                    // If the faculty member is not assigned to any department, allow deletion of the faculty member entirely
                                    if ($check_result->num_rows === 0) {
                                        // Before deleting the faculty, delete the associated image (if any)
                                        $image_query = "SELECT img FROM facultytb WHERE faculty_id = ?";
                                        $image_stmt = $con->prepare($image_query);
                                        $image_stmt->bind_param("i", $faculty_id);
                                        $image_stmt->execute();
                                        $image_result = $image_stmt->get_result();

                                        if ($image_result->num_rows > 0) {
                                            $image_row = $image_result->fetch_assoc();
                                            $image_filename = $image_row['img'];
                                            $image_path = "../uploads/" . $image_filename;

                                            // Check if the image exists and delete it
                                            if (file_exists($image_path)) {
                                                unlink($image_path);  // Delete the image
                                            }
                                        }

                                        // Delete the entire faculty member from facultytb
                                        $delete_faculty_query = "DELETE FROM facultytb WHERE faculty_id = ?";
                                        $delete_faculty_stmt = $con->prepare($delete_faculty_query);
                                        $delete_faculty_stmt->bind_param("i", $faculty_id);

                                        if ($delete_faculty_stmt->execute()) {
                                            $_SESSION['success'] = "✔ Faculty Member deleted completely!";
                                        } else {
                                            $_SESSION['error'] = "Failed to delete faculty member: " . $delete_faculty_stmt->error;
                                        }
                                    } else {
                                        // If faculty member is associated with a department, remove department association
                                        $delete_query = "DELETE FROM dept_pos_facultytb WHERE faculty_id = ? AND dept_id = ?";
                                        $delete_stmt = $con->prepare($delete_query);
                                        $delete_stmt->bind_param("ii", $faculty_id, $dept_id);  // "ii" for two integers

                                        if ($delete_stmt->execute()) {
                                            $_SESSION['success'] = "✔ Faculty Member deleted from department successfully!";
                                        } else {
                                            $_SESSION['error'] = "Deleting Faculty Member from department failed: " . $delete_stmt->error;
                                        }
                                    }
                                    // Redirect after deletion
                                    header("Location: facultyMember.php?department_id=$dept_id");
                                    exit();
                                }

                                // SQL query to filter faculty members based on the selected department_id or show those with no department
                                if ($department_id) {
                                    // Faculty members assigned to a department
                                    $sql = "SELECT f.faculty_id, f.name, dp.dept_id 
                                            FROM facultytb f
                                            JOIN dept_pos_facultytb dp ON f.faculty_id = dp.faculty_id
                                            WHERE dp.dept_id = $department_id";  // Filter by department_id
                                    $facultymembers = getDataFromQuery($sql); // Custom function to execute query
                                } else {
                                    // Faculty members not assigned to any department
                                    $sql = "SELECT f.faculty_id, f.name 
                                            FROM facultytb f
                                            LEFT JOIN dept_pos_facultytb dp ON f.faculty_id = dp.faculty_id
                                            WHERE dp.dept_id IS NULL";  // Filter faculty with no department
                                    $facultymembers = getDataFromQuery($sql); // Custom function to execute query
                                }
                            
                                if (mysqli_num_rows($facultymembers) > 0) {
                                    // Check if we are showing faculty with no department
                                    if (!$department_id) { 
                                        echo '<br><h5 style="text-align: center;">Faculty with No Department/Offices</h5>'; // Display "No Department" header
                                    }
                            
                                    // Loop through the faculty members and display them
                                    while ($item = mysqli_fetch_assoc($facultymembers)) {
                            ?>
                                        <tr style="text-align: center; vertical-align: middle;">
                                            <td><?= $item['name']; ?></td>
                                            <td>
                                                <a href="facultyDetails.php?id=<?= $item['faculty_id']; ?>" style="margin-top: 10px;" class="btn BlueBtn">View Details</a>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <form action="facultyMember.php" method="POST">
                                                    <input type="hidden" name="faculty_id" value="<?= $item['faculty_id'];?>">
                                                    <input type="hidden" name="dept_id" value="<?= $department_id ? $department_id : 0; ?>"> <!-- Pass the department_id -->
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
