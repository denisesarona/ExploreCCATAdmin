<?php 
    include('includes/header.php');
    include('../functions/queries.php');
    include('../middleware/adminMiddleware.php');
?>
<?php
// Handle faculty deletion if delete button is pressed
if (isset($_POST['deleteFaculty_button'])) {
    $faculty_id = $_POST['faculty_id']; // Get faculty_id from form
    $dept_id = isset($_POST['dept_id']) ? $_POST['dept_id'] : null; // Get dept_id from form if set

    if ($dept_id) {
        // Case 1: Remove faculty from the selected department
        $delete_query = "DELETE FROM dept_pos_facultytb WHERE faculty_id = ? AND dept_id = ?";
        $delete_stmt = $con->prepare($delete_query);
        $delete_stmt->bind_param("ii", $faculty_id, $dept_id); // Bind the faculty_id and dept_id

        if ($delete_stmt->execute()) {
            $_SESSION['success'] = "✔ Faculty Member removed from department successfully!";
        } else {
            $_SESSION['error'] = "Failed to remove faculty member from department: " . $delete_stmt->error;
        }
    } else {
        // Case 2: Completely delete faculty from all tables
        // Delete associated records (e.g., dept_pos_facultytb, facultytb, and any associated images)

        // Step 1: Delete image (if exists)
        $image_query = "SELECT img FROM facultytb WHERE faculty_id = ?";
        $image_stmt = $con->prepare($image_query);
        $image_stmt->bind_param("i", $faculty_id);
        $image_stmt->execute();
        $image_result = $image_stmt->get_result();

        if ($image_result->num_rows > 0) {
            $image_row = $image_result->fetch_assoc();
            $image_path = "../uploads/" . $image_row['img'];
            if (file_exists($image_path)) {
                unlink($image_path); // Delete the image
            }
        }

        // Step 2: Delete from dept_pos_facultytb
        $delete_dept_query = "DELETE FROM dept_pos_facultytb WHERE faculty_id = ?";
        $delete_dept_stmt = $con->prepare($delete_dept_query);
        $delete_dept_stmt->bind_param("i", $faculty_id);
        $delete_dept_stmt->execute();

        // Step 3: Delete from facultytb
        $delete_faculty_query = "DELETE FROM facultytb WHERE faculty_id = ?";
        $delete_faculty_stmt = $con->prepare($delete_faculty_query);
        $delete_faculty_stmt->bind_param("i", $faculty_id);

        if ($delete_faculty_stmt->execute()) {
            $_SESSION['success'] = "✔ Faculty Member deleted completely!";
        } else {
            $_SESSION['error'] = "Failed to delete faculty member: " . $delete_faculty_stmt->error;
        }
    }

    // Redirect after deletion
    $redirect_url = $dept_id ? "facultyMember.php?department_id=$dept_id" : "facultyMember.php";
    header("Location: $redirect_url");
    exit();
}
?>

<link rel="stylesheet" href="assets/css/style.css">

<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card mt-5">
                <h3>FACULTY MEMBERS</h3>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h4>Filter by Department/Offices:</h4>
                                <a href="addFacultyMember.php" class="btn BlueBtn">+ Add Faculty Member</a>
                            </div>
                            
                            <!-- Dropdown for Department Filter -->
                            <form action="facultyMember.php" method="GET" id="filterForm">
                                <div class="form-group">
                                    <select name="department_id" class="form-control" onchange="document.getElementById('filterForm').submit();">
                                        <option value="">All Faculty</option>
                                        <?php
                                        // Fetch departments from the departmenttb table
                                        $departments = getDatas("departmenttb");
                                        if (mysqli_num_rows($departments) > 0) {
                                            while ($department = mysqli_fetch_assoc($departments)) {
                                                $selected = isset($_GET['department_id']) && $_GET['department_id'] == $department['dept_id'] ? "selected" : "";
                                                echo "<option value='" . $department['dept_id'] . "' $selected>" . $department['name'] . "</option>";
                                            }
                                        } else {
                                            echo "<option value=''>No departments available</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                            </form>
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
                            // SQL query to fetch faculty members based on the selected department or show all faculty members
                            if ($department_id) {
                                // Faculty members assigned to a specific department
                                $sql = "SELECT f.faculty_id, f.name 
                                        FROM facultytb f
                                        JOIN dept_pos_facultytb dp ON f.faculty_id = dp.faculty_id
                                        WHERE dp.dept_id = $department_id";
                            } else {
                                // Show all faculty members
                                $sql = "SELECT faculty_id, name FROM facultytb";
                            }

                            $facultymembers = getDataFromQuery($sql);

                            if (mysqli_num_rows($facultymembers) > 0) {
                                while ($item = mysqli_fetch_assoc($facultymembers)) {
                            ?>
                                    <tr style="text-align: center; vertical-align: middle;">
                                        <td><?= $item['name']; ?></td>
                                        <td>
                                            <a href="facultyDetails.php?id=<?= $item['faculty_id']; ?>" style="margin-top: 10px;" class="btn BlueBtn">View Details</a>
                                        </td>
                                        <td class="d-none d-lg-table-cell">
                                            <form action="facultyMember.php" method="POST">
                                                <input type="hidden" name="faculty_id" value="<?= $item['faculty_id']; ?>">
                                                <input type="hidden" name="dept_id" value="<?= $department_id ? $department_id : ''; ?>"> <!-- Pass the dept_id if filtering -->
                                                <button type="submit" class="btn RedBtn" style="margin-top: 10px;" name="deleteFaculty_button">Remove</button>
                                            </form>
                                        </td>
                                    </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="3"><br>No records found</td>
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

