<?php
require_once('connection_rm_faast.php');
$srvname = $_POST['srvname'];
//$srvname = "SMB";

$sql1 = "Select nextsrvid, nextsrvname, price from faast_prime_plan_list where srvname = '".$srvname."' ";
$check1=mysqli_query($conn_rm_faast,$sql1);
$row1 = mysqli_fetch_assoc($check1);

if(isset($row1)) {
    echo "success";
}
else{
    echo "failure";
}
?>