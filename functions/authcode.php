<?php
    // INCLUDES
    include('../config/dbconnect.php');
    // START SESSION
    session_start();

    if (isset($_POST['loginBtn'])) {
        // RETRIEVE EMAIL AND PASSWORD FROM POST REQUEST
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (empty($email) || empty($password)) {
            // IF ANY FIELD IS EMPTY, SET ERROR MESSAGE AND REDIRECT TO LOGIN PAGE
            $_SESSION['error'] = "Please fill in all fields!";
            header("Location: ../index.php");
            exit();
        }
       
        // PREPARE SQL QUERY TO CHECK IF EMAIL EXISTS IN THE DATABASE
        $login_query = "SELECT * FROM users WHERE email=?";
        $stmt = mysqli_prepare($con, $login_query);
        mysqli_stmt_bind_param($stmt, "s", $email); // BIND EMAIL PARAMETER
        mysqli_stmt_execute($stmt); // EXECUTE THE QUERY
        $result = mysqli_stmt_get_result($stmt); // GET THE RESULT

        // CHECK IF EXACTLY ONE ROW IS RETURNED
        if(mysqli_num_rows($result) == 1) {
            $userdata = mysqli_fetch_assoc($result); // FETCH USER DATA
            $stored_password = $userdata['password']; // GET STORED PASSWORD

            // VERIFY PASSWORD BY DIRECT COMPARISON
            if($password === $stored_password) {
                // SET SESSION VARIABLES FOR AUTHENTICATED USER
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
                } else {
                    $_SESSION['error'] = "Unauthorized Access!";
                    header('Location: ../index.php');
                }
            } else {
                // SET ERROR MESSAGE FOR INCORRECT PASSWORD
                $_SESSION['error'] = "Incorrect Password!";
                header("Location: ../index.php");
                exit();
            }
        } else {
            // SET ERROR MESSAGE FOR INVALID CREDENTIALS
            $_SESSION['error'] = "Email not Registered!";
            header('Location: ../index.php');
            exit();
        }
    }
?>
