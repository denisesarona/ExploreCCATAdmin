<?php
session_start();
include('../config/dbconnect.php');
include('../functions/queries.php');

ob_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
// REQUIRE AUTOMATIC LOADER FOR PHPMAILER AND SET ERROR REPORTING
require '../vendor/autoload.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

function checkPasswordStrength($password) {
    $strength = 0;

    // Criteria for strength
    if (strlen($password) >= 8) {
        $strength += 1;
    }
    if (preg_match('/[A-Z]/', $password)) {
        $strength += 1;
    }
    if (preg_match('/[a-z]/', $password)) {
        $strength += 1;
    }
    if (preg_match('/[0-9]/', $password)) {
        $strength += 1;
    }
    if (preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
        $strength += 1;
    }

    // Determine the strength level
    switch ($strength) {
        case 0:
        case 1:
        case 2:
            return 'Weak';
        case 3:
        case 4:
            return 'Good';
        case 5:
            return 'Strong';
        default:
            return 'Weak';
    }
}

if(isset($_POST['addAdmin_button'])){   
    $name = $_POST['name'];
    $email = $_POST['email'];
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    $passwordStrength = checkPasswordStrength($password);
    
    if ($passwordStrength === 'Weak') {
        $_SESSION['error'] = 'Password is too weak. Please choose a stronger password!';
        header("Location: addAdmin.php");
        exit();
    } 
    // VALIDATION
    if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
        // IF ANY FIELD IS EMPTY, SET ERROR MESSAGE AND REDIRECT TO REGISTER PAGE
        $_SESSION['error'] = "Please fill in all fields!";
        header("Location: addAdmin.php");
        exit();
    }
    
    // CHECK IF PASSWORD AND CONFIRM PASSWORD MATCH
    if ($password === $confirm_password) {
        $_SESSION['registration_data'] = [
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'confirm_password' => $confirm_password,
            'role' => $role // Add this line
        ];

        $mail = new PHPMailer(true);

        // CHECK IF EMAIL ALREADY EXISTS IN DATABASE
        $email_check_sql = "SELECT * FROM users WHERE email='$email'";
        $email_check_sql = $con->query($email_check_sql);

        if($email_check_sql->num_rows > 0){
            // IF EMAIL EXISTS, SET ERROR MESSAGE AND REDIRECT TO REGISTER PAGE
            $_SESSION['error'] = "Admin already exists!";
            header("Location: addAdmin.php");
            exit();
        }
        
        try {
            // SET SMTP OPTIONS FOR MAILER
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
            
            // SET MAILER DEBUG LEVEL
            $mail -> SMTPDebug = SMTP::DEBUG_SERVER;
            $mail -> isSMTP();
            $mail -> Host = 'smtp.gmail.com';
            $mail -> SMTPAuth = true;
            $mail -> Username = 'aquaflow024@gmail.com';
            $mail -> Password = 'pamu swlw fxyj pavq';
            $mail -> SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail -> Port = 587;
    
            // SET EMAIL SENDER AND RECIPIENT
            $mail -> setFrom('aquaflow024@gmail.com', 'AquaFlow');
            $mail -> addAddress($email, $name);
            $mail -> isHTML(true);
    
            // GENERATE VERIFICATION CODE
            $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);
    
            // SET EMAIL SUBJECT AND BODY
            $mail -> Subject = 'Email verification';
            $mail -> Body = '<p>Your verification code is: <b style="font-size: 30px;">' . $verification_code . '</b></p>';
            $mail -> send();
    
            // CONNECT PHP MAILER TO DATABASE
            $con = mysqli_connect("localhost: 3306", "root", "", "capstonedb");
            if (!$con) {
                throw new Exception("Database connection failed: " . mysqli_connect_error());
            }

            // INSERT VERIFICATION CODE INTO DATABASE
            $sql = "INSERT INTO verification_codes(email, verification_code) VALUES (?, ?)";
            $stmt = $con->prepare($sql);
            $stmt->bind_param("ss", $email, $verification_code);
            $stmt->execute();

            // Retrieve the newly inserted user_id
            $id = mysqli_insert_id($con);

            // Store user_id in session for later use
            $_SESSION['code_id'] = $id;

            // Redirect to verification page with email
            header("Location: verifyEmail.php?email=" . urlencode($email));
            exit();
        } catch (Exception $e) {
            // CATCH AND DISPLAY MAILER ERROR
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        $_SESSION['error'] = "Password does not match!";
        header("Location: addAdmin.php");
        exit();
    }
} else if(isset($_SESSION['registration_data'])) {
    $registration_data = $_SESSION['registration_data'];
    $code_id = $_SESSION['code_id'];
    $name = $registration_data["name"];
    $email = $registration_data["email"];
    $role = $registration_data["role"];
    $password = $registration_data["password"];
    $confirm_password = $registration_data["confirm_password"];
    
    // VERIFICATION CODE LOGIC
    if (isset($_POST['emailVerify_button'])) {
        // RETRIEVE VERIFICATION CODE AND USER_ID FROM FORM
        $code = $_POST['code'];
    
        if (empty($code)) {
            // IF CODE IS EMPTY, SET ERROR MESSAGE AND REDIRECT TO VERIFICATION PAGE
            $_SESSION['error'] = "Please fill in all fields!";
            header("Location: verifyEmail.php?email=" . urlencode($email));
            exit();
        }

        // ESTABLISH DATABASE CONNECTION
        $con = mysqli_connect("localhost:3306", "root", "", "capstonedb");
        if (!$con) {
            // HANDLE ERROR IF CONNECTION FAILS
            $_SESSION['error'] = "Database connection failed: " . mysqli_connect_error();
            header("Location: department.php");
            exit();
        }
        
        // QUERY TO RETRIEVE USER ID AND VERIFICATION CODE
        $query = "SELECT verification_code FROM verification_codes WHERE email = ? AND code_id = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("si", $email, $code_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if (mysqli_num_rows($result) > 0) {
            // FETCH THE VERIFICATION CODE FROM DATABASE
            $row = $result->fetch_assoc();
            $stored_verification_code = $row['verification_code'];

            if ($code === $stored_verification_code) {
                // IF CODES MATCH, INSERT USER DATA INTO DATABASE
                $encrypted_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users(name, email, role, password) VALUES (?, ?, ?, ?)";
                $stmt = $con->prepare($sql);
                if (!$stmt) {
                    // HANDLE ERROR IF PREPARE STATEMENT FAILS
                    $_SESSION['error'] = "Prepare statement error: " . $con->error;
                    header("Location: index.php");
                    exit();
                }

                $stmt->bind_param("ssss", $name, $email, $role, $encrypted_password);
                $stmt->execute();
                // UNSET THE USER ID AND REGISTRATION DATA SESSION VARIABLES AFTER SUCCESSFUL REGISTRATION

                $delete_code = "DELETE FROM verification_codes WHERE email='$email' AND code_id ='$code_id'";
                $delete_code_query = mysqli_query($con, $delete_code);

                if($delete_code_query){
                    // UNSET THE USER ID AND REGISTRATION DATA SESSION VARIABLES AFTER SUCCESSFUL REGISTRATION
                    unset($_SESSION['registration_data']);
                    $_SESSION['success'] = "Registered Successfully!";
                    header("Location: admin.php");
                    exit();
                }
            } else {
                $_SESSION['error'] = "Incorrect Verification Code! Please try again.";
                header("Location: admin.php?email=" . urlencode($email));
                exit();
            }
        } else {
            // IF NO VERIFICATION CODE FOUND, SET ERROR MESSAGE AND REDIRECT TO REGISTRATION PAGE
            $_SESSION['error'] = "No verification code found for the provided email: $email!";
            header("Location: facultyMember.php");
            exit();
        }
    }
} else if(isset($_POST['deleteUser_button'])){
    $user_id = $_POST['user_id']; 

    $user_query = "SELECT * FROM users WHERE user_id='$user_id'";
    $user_query_run = mysqli_query($con, $user_query);
    $user_data = mysqli_fetch_array($user_query_run);

    // DELETE ADMIN
    $delete_query = "DELETE FROM users WHERE user_id='$user_id'";
    $delete_query_run = mysqli_query($con, $delete_query);

    if($delete_query_run){
        $_SESSION['success'] = "✔ Admin deleted successfully!";
        header("Location: admin.php");
        exit();
    } else {
        $_SESSION['error'] = "Deleting admin failed!";
        header("Location: admin.php");
        exit();
    }
} else if (isset($_POST['addFaculty_button'])) {
    // Sanitize inputs to prevent SQL Injection
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $image = $_FILES['img']['name'];
    $path = "../uploads";
    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $filename = time() . '.' . $image_ext;

    // Insert faculty data into the facultytb table
    $addFaculty_query = "INSERT INTO facultytb (name, img) VALUES ('$name', '$filename')";
    
    if (mysqli_query($con, $addFaculty_query)) {
        $faculty_id = mysqli_insert_id($con); // Get the last inserted faculty ID

        // Handle image upload
        if (!move_uploaded_file($_FILES['img']['tmp_name'], $path . '/' . $filename)) {
            $_SESSION['error'] = "Image upload failed!";
        }

        // Handle multiple departments and positions
        $departments = $_POST['departments'];  // Array of selected department IDs
        $positions = $_POST['positions'];     // Array of selected position IDs

        foreach ($departments as $index => $dept_id) {
            $position_id = $positions[$index];  // Position ID selected for this department
            // Insert each department-position pair for the faculty member into the faculty_departmenttable
            $addDepartment_query = "INSERT INTO dept_pos_facultytb (faculty_id, dept_id, position_id) 
                                    VALUES ('$faculty_id', '$dept_id', '$position_id')";
            if (!mysqli_query($con, $addDepartment_query)) {
                $_SESSION['error'] = "Failed to link faculty to department: " . mysqli_error($con);
            }
        }

        $_SESSION['success'] = "✔ Faculty member added successfully!";
    } else {
        $_SESSION['error'] = "Adding Faculty member failed: " . mysqli_error($con);
    }

    // Redirect back to the faculty member page
    header("Location: facultyMember.php");
    exit();
} else if (isset($_POST['editFaculty_button'])) {
    $faculty_id = $_POST['faculty_id'];
    $positions = $_POST['positions'];
    $departments = $_POST['departments'];

    // Delete old assignments
    $deleteQuery = "DELETE FROM dept_pos_facultytb WHERE faculty_id = $faculty_id";
    mysqli_query($con, $deleteQuery);

    // Insert updated assignments
    for ($i = 0; $i < count($positions); $i++) {
        if (!empty($positions[$i]) && !empty($departments[$i])) {
            $insertQuery = "INSERT INTO dept_pos_facultytb (faculty_id, position_id, dept_id) 
                            VALUES ($faculty_id, {$positions[$i]}, {$departments[$i]})";
            mysqli_query($con, $insertQuery);
            $_SESSION['success'] = "✔ Faculty details updated successfully!";
        } else{
            $_SESSION['error'] = "Updating Faculty details failed: " . mysqli_error($con);
        }
    }
    header("Location: facultyDetails.php?id=$faculty_id&status=updated");
    exit();
} else if(isset($_POST['deleteFaculty_button'])) {
    $faculty_id = $_POST['faculty_id'];

    // Delete the faculty member from the facultytb
    $delete_query = "DELETE FROM facultytb WHERE faculty_id=?";
    $delete_stmt = $con->prepare($delete_query);
    $delete_stmt->bind_param("i", $faculty_id);

    if ($delete_stmt->execute()) {
        $delete_info_query = "DELETE FROM dept_pos_facultytb WHERE faculty_id=?";
        $delete_stmt = $con->prepare($delete_info_query);
        $delete_stmt->bind_param("i", $faculty_id);
        
        if ($delete_stmt->execute()) {
            $_SESSION['success'] = "✔ Faculty Member deleted successfully!";
        } else {
            $_SESSION['error'] = "Deleting Faculty Member information failed: " . $delete_stmt->error;
        }
    } else {
        $_SESSION['error'] = "Deleting Faculty Member failed: " . $delete_stmt->error;
    }

    // Redirect to faculty member page
    header("Location: facultyMember.php");
    exit();
} else if(isset($_POST['addPosition_button'])){
    $position = $_POST['name'];

    $position_query = "INSERT INTO positiontb(name) VALUES ('$position')";

    $position_query_run = mysqli_query($con, $position_query);

    if($position_query_run){
        $_SESSION['success'] = "✔ Position added successfully!";
        header("Location: faculty_position.php");
        exit();
    } else {
        $_SESSION['error'] = "Adding Position failed!";
        header("Location: faculty_position.php");
        exit();
    }
} else if(isset($_POST['deletePosition_button'])){
    $position_id = $_POST['position_id']; 

    $position_query = "SELECT * FROM positiontb WHERE position_id='$position_id'";
    $position_query_run = mysqli_query($con, $position_query);
    $positiont_data = mysqli_fetch_array($position_query_run);

    // DELETE DEPARTMENT
    $delete_query = "DELETE FROM positiontb WHERE position_id='$position_id'";
    $delete_query_run = mysqli_query($con, $delete_query);

    if($delete_query_run){
        $_SESSION['success'] = "✔ Position deleted successfully!";
        header("Location: faculty_position.php");
        exit();
    } else {
        $_SESSION['error'] = "Deleting position failed!";
        header("Location: faculty_position.php");
        exit();
    }
} else if(isset($_POST['addDepartment_button'])) {
    $department = $_POST['dept_name'];

    // Prepare to insert the new department
    $dept_query = "INSERT INTO departmenttb(name) VALUES (?)";
    $stmt = $con->prepare($dept_query);
    
    if ($stmt) {
        $stmt->bind_param("s", $department);

        if ($stmt->execute()) {
            $_SESSION['success'] = "✔ Department added successfully!";
            header("Location: department.php");
        } else {
            $_SESSION['error'] = "Adding Department failed: " . $stmt->error;
            header("Location: department.php");
        }

        $stmt->close();
    } else {
        echo "Error preparing statement: " . $con->error . "<br>";
    }
} else if(isset($_POST['deleteDepartment_button'])) {
    $dept_id = $_POST['dept_id']; 

    // Delete the department from the departmenttb
    $delete_query = "DELETE FROM departmenttb WHERE dept_id='$dept_id'";
    $delete_query_run = mysqli_query($con, $delete_query);

    if ($delete_query_run) {
        $_SESSION['success'] = "✔ Department deleted successfully!";
    } else {
        $_SESSION['error'] = "Deleting department failed!";
    }

    // Redirect to the department page
    header("Location: department.php");
    exit();
} else if(isset($_POST['editBldginfo_button'])){
    $building_description = $_POST['building_description'];
    $new_department_id = $_POST['dept_id'];
    $department_name = $_POST['department_name'];
    $is_amenities = isset($_POST['is_amenities']) ? '1':'0'; 
    $key_features = $_POST['key_features'];
    $building_id = $_POST['building_id']; // Assuming you get this from the form

    // Prepare the SQL update query
    $update_query = "UPDATE buildingtbl SET building_description=?, dept_id=?, department_name=?, is_amenities=?, key_features=? WHERE building_id=?";
    $stmt = $con->prepare($update_query);

    if (!$stmt) {
        die("Prepare failed: (" . $con->errno . ") " . $con->error);
    }

    // Bind parameters
    $stmt->bind_param("sisisi", $building_description, $new_department_id, $department_name, $is_amenities, $key_features, $building_id);

    // Execute update
    if ($stmt->execute()) {
        $_SESSION['success'] = "✔ Building Details updated successfully!";
        header("Location: buildings.php");
        exit();
    } else {
        $_SESSION['error'] = "Updating Building Details failed! Error: " . $stmt->error;
        header("Location: buildings.php");
        exit();
    }
} else if(isset($_POST['deleteFeedback_button'])) {
    $fid = $_POST['fid']; 

    // Delete the department from the departmenttb
    $delete_query = "DELETE FROM feedbacktbl WHERE fid='$fid'";
    $delete_query_run = mysqli_query($con, $delete_query);

    if ($delete_query_run) {
        $_SESSION['success'] = "✔ Feedback deleted successfully!";
    } else {
        $_SESSION['error'] = "Deleting feedback failed!";
    }

    // Redirect to the department page
    header("Location: userfeedback.php");
    exit();
} else if(isset($_POST['deleteFacultyInfo_button'])){
    $info_id = $_POST['info_id']; 

    // Delete the department from the departmenttb
    $delete_query = "DELETE FROM dept_pos_facultytb WHERE faculty_dept_id='$info_id'";
    $delete_query_run = mysqli_query($con, $delete_query);

    if ($delete_query_run) {
        $_SESSION['success'] = "✔ Faculty department and position deleted successfully!";
    } else {
        $_SESSION['error'] = "Deleting Faculty department and position failed!";
    }

    // Redirect to the department page
    header("Location: facultyMember.php");
    exit();
}

ob_end_flush();
?>