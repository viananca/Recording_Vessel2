<?php 
session_start(); // start the session



// unset session variables
unset($_SESSION['loggedin']);
unset($_SESSION['schoolid']);
unset($_SESSION['firstname']);
unset($_SESSION['lastname']);
unset($_SESSION['user_label']);

// destroy the session
session_destroy();

// redirect user to login page
header("Location: index.php");
exit();

?>