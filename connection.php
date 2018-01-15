<?php
$db_servername1 = "localhost";
$db_username1 = "root";
$db_password1 = "ccpl@1234";
$db_name1 = "radius";

// Create connection
$conn = new mysqli($db_servername1, $db_username1, $db_password1, $db_name1);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
