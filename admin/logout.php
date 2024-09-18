<?php
    session_start(); // START SESSION

    if(isset($_SESSION['auth'])){ // IF LOGGED IN
        unset($_SESSION['auth']); // UNSET ADMIN
        $_SESSION['success'] = "Logged out successfully!"; 
    }
    header('Location: ../index.php'); // REDIRECT TO LOGIN
?>