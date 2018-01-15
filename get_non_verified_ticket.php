<?php

require_once('connection_rm_faast.php');
$username = $_POST['username'];
//$username = "8549826883";

$result = "Select * from support where username ='" . $username . "' and status_id !='4' ";
$check=mysqli_query($conn_rm_faast,$result);
if((mysqli_affected_rows($conn_rm_faast)) > 0 ){
    echo 'success';
}
else{
    echo "failure";
}
?>