<?php
$db_servername = "localhost";
$db_username = "root";
$db_password = "ccpl@1234";
$db_name = "rm_faast";

// Create connection
$conn_rm_faast = new mysqli($db_servername, $db_username, $db_password, $db_name);

// Check connection
if ($conn_rm_faast->connect_error) {
    die("Connection failed: " . $conn_rm_faast->connect_error);
}
?>
