<?php
require_once('connection.php');
$username = $_POST['username'];
//$username = "8549826883";

$sql = "select username from rm_users where username='".$username."' and warningsent='0' ";
$check = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($check);

if(isset($row)) {
    echo "fiber";
}
else{
    echo "wireless";
}