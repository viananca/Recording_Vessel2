<?php
$servername = "localhost";
$username = "root";
$password = "";
$db_name = "aleson_db";

// Create connection
$con = new mysqli($servername, $username, $password, $db_name);
if(!$con){
  die('Connection Failed'.mysqli_connect_error());
}
?>