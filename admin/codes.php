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
} else if (isset($_SESSION['registration_data'])) {
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
                    header("Location: faculty_position.php");
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
    $position = mysqli_real_escape_string($con, $_POST['position']);
    $department_id = mysqli_real_escape_string($con, $_POST['dept_id']);
    $department_name = mysqli_real_escape_string($con, $_POST['department']);
    
    // Handle file upload
    $image = $_FILES['img']['name'];
    $path = "../uploads";
    $image_ext = pathinfo($image, PATHINFO_EXTENSION);
    $filename = time() . '.' . $image_ext;

    // Insert faculty data into the database
    $addFaculty_query = "INSERT INTO facultytb (name, position, dept_id, department, img) 
                         VALUES ('$name', '$position', '$department_id', '$department_name', '$filename')";
    $addFaculty_query_run = mysqli_query($con, $addFaculty_query);

    // Fetch department data
    $dept_query = "SELECT * FROM departmenttb WHERE dept_id='$department_id'";
    $dept_query_run = mysqli_query($con, $dept_query);
    $dept_data = mysqli_fetch_array($dept_query_run);

    if ($dept_data) {
        // Prepare the table name
        $dept_name = $dept_data['name'];
        $table_name = 'dept_' . preg_replace('/\s+/', '_', strtolower($dept_name));
        
        // Insert into the department-specific table
        $insert_query = "INSERT INTO $table_name (name, position, dept_id, department, img) 
                         VALUES ('$name', '$position', '$department_id', '$department_name', '$filename')";
        $insert_query_run = mysqli_query($con, $insert_query);

        // Check if both queries were successful
        if ($addFaculty_query_run && $insert_query_run) {
            // Move the uploaded file
            if (move_uploaded_file($_FILES['img']['tmp_name'], $path . '/' . $filename)) {
                $_SESSION['success'] = "✔ Faculty member added successfully!";
            } else {
                $_SESSION['error'] = "Image upload failed!";
            }
        } else {
            $_SESSION['error'] = "Adding Faculty member failed!";
        }
    } else {
        $_SESSION['error'] = "Department not found!";
    }

    // Redirect back to the faculty member page
    header("Location: facultyMember.php");
    exit();
} else if (isset($_POST['editFaculty_button'])) {
    $faculty_id = $_POST['faculty_id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $new_department_id = $_POST['dept_id'];
    $department = $_POST['department']; // Ensure this input exists in your form
    $new_image = $_FILES['image']['name'];
    $old_image = $_POST['old_image'];

    // Fetch the current faculty details to get the old department ID
    $faculty_query = "SELECT dept_id FROM facultytb WHERE faculty_id=?";
    $faculty_stmt = $con->prepare($faculty_query);
    $faculty_stmt->bind_param("i", $faculty_id);
    $faculty_stmt->execute();
    $faculty_result = $faculty_stmt->get_result();
    $faculty_data = $faculty_result->fetch_assoc();
    $old_department_id = $faculty_data['dept_id'];

    // Set the update filename
    $update_filename = $new_image ? time() . '.' . pathinfo($new_image, PATHINFO_EXTENSION) : $old_image;

    $path = "../uploads";

    // Update faculty details
    $update_query = "UPDATE facultytb SET name=?, position=?, dept_id=?, department=?, img=? WHERE faculty_id=?";
    $stmt = $con->prepare($update_query);

    if (!$stmt) {
        die("Prepare failed: (" . $con->errno . ") " . $con->error);
    }

    // Bind parameters
    $stmt->bind_param("ssissi", $name, $position, $new_department_id, $department, $update_filename, $faculty_id);

    // Execute update
    if ($stmt->execute()) {
        // If the department has changed, move the entry to the new department table
        if ($old_department_id !== $new_department_id) {
            // Fetch the old department table name
            $old_dept_query = "SELECT name FROM departmenttb WHERE dept_id=?";
            $old_dept_stmt = $con->prepare($old_dept_query);
            $old_dept_stmt->bind_param("i", $old_department_id);
            $old_dept_stmt->execute();
            $old_dept_result = $old_dept_stmt->get_result();
            $old_dept_data = $old_dept_result->fetch_assoc();
            $old_table_name = 'dept_' . preg_replace('/\s+/', '_', strtolower($old_dept_data['name']));

            // Check if the old department table exists
            if (mysqli_query($con, "SHOW TABLES LIKE '$old_table_name'")->num_rows > 0) {
                // Delete from the old department table
                $delete_old_query = "DELETE FROM $old_table_name WHERE faculty_id=?";
                $delete_old_stmt = $con->prepare($delete_old_query);
                $delete_old_stmt->bind_param("i", $faculty_id);
                $delete_old_stmt->execute();
                $delete_old_stmt->close();
            }

            // Prepare the new department table name
            $new_table_name = 'dept_' . preg_replace('/\s+/', '_', strtolower($department));

            // Check if the new department table exists
            if (mysqli_query($con, "SHOW TABLES LIKE '$new_table_name'")->num_rows > 0) {
                // Insert into the new department-specific table
                $insert_new_query = "INSERT INTO $new_table_name (name, position, dept_id, department, img) VALUES (?, ?, ?, ?, ?)";
                $insert_stmt = $con->prepare($insert_new_query);

                if (!$insert_stmt) {
                    die("Prepare failed: (" . $con->errno . ") " . $con->error);
                }

                $insert_stmt->bind_param("ssiss", $name, $position, $new_department_id, $department, $update_filename);
                $insert_stmt->execute();
                $insert_stmt->close();
            }
        }

        // Handle image upload
        if ($new_image) {
            move_uploaded_file($_FILES['image']['tmp_name'], $path . '/' . $update_filename);
            if (file_exists("../uploads/" . $old_image)) {
                unlink("../uploads/" . $old_image);
            }
        }

        $_SESSION['success'] = "✔ Faculty Member Details updated successfully!";
        header("Location: facultyMember.php");
        exit();
    } else {
        $_SESSION['error'] = "Updating Faculty Member Details failed! Error: " . $stmt->error;
        header("Location: facultyMember.php");
        exit();
    }

    $stmt->close();
} else if(isset($_POST['deleteFaculty_button'])){
    $faculty_id = $_POST['faculty_id'];

    $faculty_query = "SELECT * FROM facultytb WHERE faculty_id='$faculty_id'";
    $faculty_query_run = mysqli_query($con, $faculty_query);
    $faculty_data = mysqli_fetch_array($faculty_query_run);

    // DELETE FACULTY MEMBER
    $delete_query = "DELETE FROM facultytb WHERE faculty_id='$faculty_id'";
    $delete_query_run = mysqli_query($con, $delete_query);

    if($delete_query_run){
        $_SESSION['success'] = "✔ Faculty Member deleted successfully!";
        header("Location: facultyMember.php");
        exit();
    } else {
        $_SESSION['error'] = "Deleting faculty member failed!";
        header("Location: facultyMember.php");
        exit();
    }
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
} 