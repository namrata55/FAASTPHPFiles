<?php

require_once('connection_rm_faast.php');
$username = $_POST['username'];
//$username = "8549826883";
$support_ticket_status = 0;

$result = "Select status_id from support where username ='" . $username . "'";
$check=mysqli_query($conn_rm_faast,$result);
while($row1=mysqli_fetch_assoc($check)){
    $status = $row1["status_id"];
    if($status!=4){
        $support_ticket_status = 1;
    }
}

if($support_ticket_status == 1){
    echo "true";
}
else{
    echo "false";
}
?>