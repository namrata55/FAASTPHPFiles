<?php

require_once('connection.php');
$username = $_POST['username'];
$password = md5($_POST['password']);
$gcmid = $_POST['gcmid'];
//$username="ka.mmdc";
////$password = "taranath123";
//$password = md5("mmdc123");
//$gcmid="66666666666666";

$sql = "select * from rm_users where username='".$username."' and password='".$password."'";

$check=mysqli_query($conn,$sql);
$row=mysqli_fetch_assoc($check);
if(isset($row)){
            $sql2 = "UPDATE rm_users SET contractid = '". $gcmid . "' WHERE username='".$username."' ";
            $check2 = mysqli_query($conn,$sql2);
            echo "success";
}else{
    echo 'failure';
}
?>