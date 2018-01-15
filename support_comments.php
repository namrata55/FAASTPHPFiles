<?php
require_once('connection_rm_faast.php');
$username = $_POST['username'];
//$username = "8549826883";
$support_id = $_POST['support_id'];
//$support_id = "2034";

$result = "Select * from support_comments where support_user_id = '".$support_id."' and type = '1' ";
$check=mysqli_query($conn_rm_faast,$result);

if((mysqli_affected_rows($conn_rm_faast)) > 0 ){
    while($row1=mysqli_fetch_assoc($check)){
        $temp["commented_by"] = $row1["commented_by"];
        $temp["comment"] = $row1["comment"];
        $creation_date= $row1["creation"];
        $temp["ticket_creation"] = date('d/m/Y h:i:s',strtotime($creation_date));
        $json_output[] = $temp;
    }
    print(json_encode($json_output));
//    echo '<pre>';print_r($json_output);
}
else{
    echo "";
}
?>