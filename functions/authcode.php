<?php
    // INCLUDES
    include('../config/dbconnect.php');
    // START SESSION
    session_start();
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
    
    if (isset($_POST['loginBtn'])) {
        // Retrieve email and password from POST request
        $email = $_POST['email'];
        $password = $_POST['password'];
    
        if (empty($email) || empty($password)) {
            // If any field is empty, set error message and redirect to login page
            $_SESSION['error'] = "Please fill in all fields!";
            header("Location: ../index.php");
            exit();
        }
       
        // Prepare SQL query to check if email exists in the database
        $login_query = "SELECT * FROM users WHERE email=?";
        $stmt = mysqli_prepare($con, $login_query);
        mysqli_stmt_bind_param($stmt, "s", $email); // Bind email parameter
        mysqli_stmt_execute($stmt); // Execute the query
        $result = mysqli_stmt_get_result($stmt); // Get the result
    
        // Check if exactly one row is returned
        if(mysqli_num_rows($result) == 1) {
            $userdata = mysqli_fetch_assoc($result); // Fetch user data
            $stored_password = $userdata['password']; // Get stored password
    
            // Verify password
            if($password == $stored_password) {
                // Set session variables for authenticated user
                $_SESSION['auth'] = true;
                $_SESSION['user_id'] = $userdata['user_id']; // Ensure that user_id is set in the session
                $_SESSION['auth_user'] = [
                    'name' => $userdata['name'],
                    'email' => $userdata['email']
                ];
    
                $role = $userdata['role']; // Get user role
                $_SESSION['role'] = $role;
    
                // Redirect based on user role
                if($role == 1) {
                    $_SESSION['success'] = "Welcome to Admin Dashboard!";
                    header('Location: ../admin/index.php');
                    exit();
                } else {
                    $_SESSION['error'] = "Unauthorized Access!";
                    header('Location: ../index.php');
                    exit();
                }
            } else {
                // Set error message for incorrect password
                $_SESSION['error'] = "Incorrect Password! $password";
                header("Location: ../index.php");
                exit();
            }
        } else {
            // Set error message for invalid credentials
            $_SESSION['error'] = "Email not Registered!";
            header('Location: ../index.php');
            exit();
        }
    } else if(isset($_POST['forgotPassword_button'])){
        $email = $_POST['email'];
        $_SESSION['forgotPass_data'] = $_POST; // STORE EMAIL IN SESSION FOR LATER USE

        // PREPARE SQL QUERY TO CHECK IF EMAIL EXISTS IN THE DATABASE
        $email_check_sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $con->prepare($email_check_sql);
        $stmt->bind_param("s", $email); // BIND EMAIL PARAMETER
        $stmt->execute(); // EXECUTE THE QUERY
        $email_check_sql_run = $stmt->get_result(); // GET THE RESULT

        // CHECK IF EMAIL EXISTS IN DATABASE
        if ($email_check_sql_run->num_rows == 0) {
            // EMAIL NOT REGISTERED, REDIRECT TO REGISTRATION PAGE WITH MESSAGE
            $_SESSION['error'] = "Unauthorize Access!";
            header('Location: ../index.php');
            exit();
        }

        // INITIALIZE PHPMailer
        $mail = new PHPMailer(true);
        
        try {
            // CONFIGURE SMTP OPTIONS FOR SECURE CONNECTION
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
            
            $mail->SMTPDebug = SMTP::DEBUG_SERVER; // ENABLE DEBUG OUTPUT
            $mail->isSMTP(); // SET MAILER TO USE SMTP
            $mail->Host = 'smtp.gmail.com'; // SPECIFY SMTP SERVER
            $mail->SMTPAuth = true; // ENABLE SMTP AUTHENTICATION
            $mail->Username = 'aquaflow024@gmail.com'; // SMTP USERNAME
            $mail->Password = 'pamu swlw fxyj pavq'; // SMTP PASSWORD
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // ENABLE TLS ENCRYPTION
            $mail->Port = 587; // TCP PORT TO CONNECT TO

            // SET EMAIL SENDER AND RECIPIENT
            $mail->setFrom('aquaflow024@gmail.com', 'AquaFlow');
            $mail->addAddress($email, 'AquaFlow'); // ADD RECIPIENT EMAIL
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
            $stmt->bind_param("ss", $email, $verification_code); // BIND PARAMETERS
            $stmt->execute(); // EXECUTE THE QUERY

            $user_id = mysqli_insert_id($con);

            // Store user_id in session for future use
            $_SESSION['user_id'] = $user_id;
            // CHECK IF STATEMENT EXECUTED SUCCESSFULLY
            if ($stmt) {
                // REDIRECT TO VERIFICATION PAGE WITH SUCCESS MESSAGE
                $_SESSION['success'] = "Verification code sent to email!";
                header("Location: ../EmailVerify.php?email=" . urlencode($email) );
                exit();
            } else {
                // REDIRECT TO INDEX PAGE WITH ERROR MESSAGE
                $_SESSION['error'] = "Error sending email!";
                header('Location: ../index.php');
                exit();
            }
        } catch (Exception $e) {
            // HANDLE MAIL SENDING ERROR
            $_SESSION['error'] = "Message could not be sent. Mailer Error: {$mail->ErrorInfo}!";
            header('Location: ../index.php');
            exit();
        } finally {
            $con->close(); // CLOSE THE DATABASE CONNECTION
        }
    } else if(isset($_POST['emailVerify_button'])){
        // RETRIEVE EMAIL FROM SESSION DATA OR SET TO NULL
        $email = $_SESSION['forgotPass_data']['email'] ?? null;
        $user_id = $_SESSION['user_id'];
        // RETRIEVE VERIFICATION CODE AND USER ID FROM POST DATA
        $code = $_POST['code'];

        // CHECK IF ANY FIELD IS EMPTY
        if (empty($code)) {
            // SET ERROR MESSAGE AND REDIRECT TO forgotPassword.php WITH EMAIL PARAMETER
            $_SESSION['error'] = "Please fill in all fields!";
            header("Location: ../emailVerify.php?email=" . urlencode($email));
            exit();
        }

        // ESTABLISH DATABASE CONNECTION
        $con = mysqli_connect("localhost", "root", "", "capstonedb");
        // CHECK IF CONNECTION IS SUCCESSFUL
        if (!$con) {
            // SET ERROR MESSAGE AND REDIRECT TO INDEX.PHP
            $_SESSION['message'] = "Database connection failed: " . mysqli_connect_error();
            header("Location: ../index.php");
            exit();
        }

        // PREPARE SQL STATEMENT TO SELECT VERIFICATION CODE BASED ON EMAIL AND USER ID
        $query = "SELECT verification_code FROM verification_codes WHERE email = ? AND code_id=?";
        $stmt = $con->prepare($query);
        $stmt->bind_param("si", $email, $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        // CHECK IF VERIFICATION CODE EXISTS FOR GIVEN EMAIL AND USER ID
        if ($result->num_rows > 0) {
            // FETCH ROW FROM RESULT
            $row = $result->fetch_assoc();
            // RETRIEVE STORED VERIFICATION CODE
            $stored_verification_code = $row['verification_code'];

            // CHECK IF ENTERED CODE MATCHES STORED CODE
            if ($code === $stored_verification_code) {
                $delete_code = "DELETE FROM verification_codes WHERE email='$email' AND code_id='$user_id'";
                $delete_code_query = mysqli_query($con, $delete_code);

                if($delete_code_query){
                    // SET SUCCESS MESSAGE AND REDIRECT TO changePassword.php WITH EMAIL AND USER ID PARAMETERS
                    unset($_SESSION['user_id']);
                    $_SESSION['success'] = "Verification code correct!";
                    header("Location: ../changePassword.php?email=" . urlencode($email));
                    exit();
                }
            } else {
                // SET ERROR MESSAGE AND REDIRECT TO forgotPassword.php WITH EMAIL PARAMETER
                $_SESSION['error'] = "Incorrect verification code! Please try again!";
                header("Location: ../emailVerify.php?email=" . urlencode($email));
                exit();
            }
        } else {
            // SET ERROR MESSAGE AND REDIRECT TO INDEX.PHP
            $_SESSION['error'] = "No verification code found for the provided email!";
            header("Location: ../index.php");
            exit();
        }
    } else if(isset($_POST['changePassword_button'])){
        // RETRIEVE EMAIL FROM SESSION DATA OR SET TO NULL
        $email = $_SESSION['forgotPass_data']['email'] ?? null;
        // RETRIEVE NEW PASSWORD AND CONFIRM PASSWORD FROM POST DATA
        $password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        $passwordStrength = checkPasswordStrength($password);
        
        if ($passwordStrength === 'Weak') {
            $_SESSION['error'] = 'Password is too weak. Please choose a stronger password!';
            header("Location: ../changePassword.php?email=" . urlencode($email));
            exit();
        } 
        // CHECK IF NEW PASSWORD MATCHES CONFIRM PASSWORD AND EMAIL EXISTS
        if ($password === $confirm_password && $email) {
            // HASH THE NEW PASSWORD
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // PREPARE SQL STATEMENT TO UPDATE PASSWORD
            $update_query = "UPDATE users SET password = ? WHERE email = ?";
            $stmt = $con->prepare($update_query);
            $stmt->bind_param("ss", $hashed_password, $email);

            // EXECUTE UPDATE QUERY
            if ($stmt->execute()) {
                // SET SUCCESS MESSAGE AND REDIRECT TO INDEX.PHP
                $_SESSION['success'] = "Password updated successfully!";
                header("Location: ../index.php");
                exit();
            } else {
                // SET ERROR MESSAGE AND REDIRECT TO forgot-passVerify.php WITH EMAIL PARAMETER
                $_SESSION['error'] = "Failed to update password. Please try again!";
                header("Location: ../forgotPassword.php?email=" . urlencode($email));
                exit();
            }
        } else {
            // SET ERROR MESSAGE AND REDIRECT TO forgot-passVerify.php WITH EMAIL PARAMETER
            $_SESSION['error'] = "Passwords do not match!";
            header("Location: ../changePassword.php?email=" . urlencode($email));
            exit();
        }
    }
?>
