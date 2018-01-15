<?php

require_once('connection_rm_faast.php');
$username = $_POST['username'];
//$username = "8549826883";

$result = "Select s.id, s.subject, s.support_id, sc.comment, ss.status_name, s.creation from support s inner join support_status ss on ss.id = s.status_id inner join support_comments sc on sc.support_user_id = s.id where username ='" . $username . "' group by s.id order by s.id desc";
$check=mysqli_query($conn_rm_faast,$result);
if((mysqli_affected_rows($conn_rm_faast)) > 0 ){
    while($row1=mysqli_fetch_assoc($check)){
        $temp["ticket_subject"] = $row1["subject"];
        $temp["id"] = $row1["id"];
        $temp["ticket_id"] = $row1["support_id"];
        $temp["ticket_status"]= $row1["status_name"];
        $temp["ticket_comment"]= $row1["comment"];
        $creation_date= $row1["creation"];
        $temp["ticket_creation"] = date('d/m/Y',strtotime($creation_date));
        $json_output[] = $temp;
    }
    print(json_encode($json_output));
//    echo '<pre>';print_r($json_output);
}
else{
    echo "";
}
?>