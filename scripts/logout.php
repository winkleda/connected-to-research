<?php
ini_set('display_errors',1); error_reporting(E_ALL);
/* Destroys session and takes user to login screen */
session_start();
unset($_SESSION["email"]);
session_destroy();
header('location: ../login.html');
exit();
?>