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
        $_SESSION['registration_data'] = $_POST;

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
            $_SESSION['user_id'] = $id;

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
} else if (isset($_POST['emailVerify_button'])) {
    $email = $_POST['email'];
    $email_checkbox = isset($_POST['email_checkbox']) ? 1 : 0; // Check if checkbox was set

    if (empty($email)) {
        $_SESSION['error'] = "Please fill in all fields!";
        header("Location: addAdmin.php");
        exit();
    }

    // INITIALIZE PHPMailer
    $mail = new PHPMailer(true);
    try {
        // SMTP configuration...
        
        // SET EMAIL SENDER AND RECIPIENT
        $mail->setFrom('aquaflow024@gmail.com', 'AquaFlow');
        $mail->addAddress($email); // ADD RECIPIENT EMAIL
        $mail->isHTML(true); // SET EMAIL FORMAT TO HTML

        // GENERATE A VERIFICATION CODE
        $verification_code = substr(number_format(time() * rand(), 0, '', ''), 0, 6);

        // SET EMAIL SUBJECT AND BODY CONTENT
        $mail->Subject = 'Email verification';
        $mail->Body = '<p>Your verification code is: <b style="font-size: 30px;">' . $verification_code . '</b></p>';
        $mail->send(); // SEND THE EMAIL

        // INSERT VERIFICATION CODE INTO DATABASE
        $sql = "INSERT INTO verification_codes (email, verification_code) VALUES (?, ?)";
        $stmt = $con->prepare($sql);
        $stmt->bind_param("ss", $email, $verification_code);
        $stmt->execute();

        // Set session variable to indicate email was sent
        $_SESSION['email_sent'] = true;

        if ($stmt) {
            $_SESSION['success'] = "Verification code sent to email!";
            header("Location: addAdmin.php");
            exit();
        } else {
            $_SESSION['error'] = "Error sending email!";
            header('Location: addAdmin.php');
            exit();
        }
    } catch (Exception $e) {
        $_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}!";
        header('Location: addAdmin.php');
        exit();
    } finally {
        $con->close(); // CLOSE THE DATABASE CONNECTION
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
    $name = $_POST['name'];
    $position = $_POST['position'];
    $department = $_POST['department'];

    $image = $_FILES['img']['name']; // GET THE ORIGINAL NAME OF THE UPLOADED FILE 

    $path = "../uploads"; // DEFINE THE DIRECTORY WHERE UPLOADED IMAGES WILL BE STORED 

    $image_ext = pathinfo($image, PATHINFO_EXTENSION); // GET THE FILE EXTENSION OF THE UPLOADED IMAGE 
    $filename = time() . '.' . $image_ext; // GENERATE A UNIQUE FILENAME FOR THE UPLOADED IMAGE

    $addFaculty_query = "INSERT INTO facultytb
        (name, position, department, img)
        VALUES ('$name', '$position', '$department', '$filename')"; 
    
    $addFaculty_query_run = mysqli_query($con, $addFaculty_query); // EXECUTE THE SQL QUERY TO INSERT FACULTY INFORMATION INTO THE DATABASE 

    if ($addFaculty_query_run) {
        move_uploaded_file($_FILES['img']['tmp_name'], $path . '/' . $filename); // MOVE THE UPLOADED IMAGE FILE 
        $_SESSION['success'] = "✔ Faculty member added successfully!";
        header("Location: facultyMember.php");
        exit();
    } else {
        $_SESSION['error'] = "Adding Faculty member failed!";
        header("Location: facultyMember.php");
        exit();
    }
} else if(isset($_POST['editFaculty_button'])){
    $faculty_id = $_POST['faculty_id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $department = $_POST['department'];
    $image = $_POST['image'];

    $new_image = $_FILES['image']['name']; // GET THE ORIGINAL NAME OF THE UPLOADED FILE 
    $old_image = $_POST['old_image'];

    if($new_image != ""){
        $image_ext = pathinfo($new_image, PATHINFO_EXTENSION); // GET THE FILE EXTENSION OF THE UPLOADED IMAGE 
        $update_filename = time().'.'.$image_ext; // GENERATE A UNIQUE FILENAME FOR THE UPLOADED IMAGE BY APPEDING THE CURRENT TIMESTAMP AND THE ORIGINAL FILE EXT
    } else{
        $update_filename = $old_image;
    }                                               

    $path = "../uploads";

    $update_query = "UPDATE facultytb SET name='$name', position='$position', department='$department', img='$update_filename' WHERE faculty_id='$faculty_id'";

    $update_query_run = mysqli_query($con, $update_query);

    if ($update_query_run) {
        if ($_FILES['image']['name'] != "") {
            move_uploaded_file($_FILES['image']['tmp_name'], $path . '/' . $update_filename);
            if (file_exists("../uploads/" . $old_image)) {
                unlink("../uploads/" . $old_image);
            }
        }
    
        $_SESSION['success'] = "✔ Faculty Member Details updated successfully!";
        header("Location: facultyMember.php");
        exit();
    } else {
        $_SESSION['error'] = "Updating Faculty Member Details failed! Error: " . mysqli_error($con);
        header("Location: facultyMember.php");
        exit();
    }    
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
} else if(isset($_POST['addDepartment_button'])){
    $department = $_POST['dept_name'];

    $dept_query = "INSERT INTO departmenttb(name) VALUES ('$department')";

    $dept_query_run = mysqli_query($con, $dept_query);

    if($dept_query_run){
        $_SESSION['success'] = "✔ Department added successfully!";
        header("Location: department.php");
        exit();
    } else {
        $_SESSION['error'] = "Adding Department failed!";
        header("Location: department.php");
        exit();
    }
} else if(isset($_POST['deleteDepartment_button'])){
    $dept_id = $_POST['dept_id']; 

    $dept_query = "SELECT * FROM departmenttb WHERE dept_id='$dept_id'";
    $dept_query_run = mysqli_query($con, $dept_query);
    $dept_data = mysqli_fetch_array($dept_query_run);

    // DELETE DEPARTMENT
    $delete_query = "DELETE FROM departmenttb WHERE dept_id='$dept_id'";
    $delete_query_run = mysqli_query($con, $delete_query);

    if($delete_query_run){
        $_SESSION['success'] = "✔ Department deleted successfully!";
        header("Location: department.php");
        exit();
    } else {
        $_SESSION['error'] = "Deleting department failed!";
        header("Location: department.php");
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