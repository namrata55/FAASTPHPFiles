<?php
require_once('connection.php');
//$username = $_POST['username'];
$username = "8549826883";

$attribute = "Cleartext-Password";
$current_date = date('Y-m-d');

$response = array();
$response["fixture"] = array();
$sql = "SELECT rc.value, us.firstname, us.lastname, us.username, us.mobile FROM radcheck rc INNER JOIN rm_users us ON rc.username = us.username WHERE rc.attribute='".$attribute."' AND rc.username='".$username."' ";
$check = mysqli_query($conn,$sql);
$row_cnt = $check->num_rows;

if($row_cnt > 0){
    while($row=mysqli_fetch_assoc($check)){

        $fullname = $row["firstname"].' '.$row["lastname"];
        $firstname = $row["firstname"];
        $password = $row["value"];
        $password1 =  md5($password);
        $mb = trim($row['mobile']);
        if($mb==null || empty($mb))
        {
             $mobile=$username;
        }
        else{
             $mobile=$mb;
        }

        $msg = "Dear ".$firstname.", your FAAST username is ".$username." and password is ".$password.". FAAST Broadband.";

        $msg1 = str_replace(' ', '%20', $msg);

        $curl_handle=curl_init();

//        curl_setopt($curl_handle,CURLOPT_URL,'http://193.105.74.159/api/v3/sendsms/plain?user=smartt&password=8800755655&sender=FAAAST&SMSText='.$msg1.'&type=longsms&GSM=91'.$mobile.'&output=json');
        curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
        $buffer = curl_exec($curl_handle);
        curl_close($curl_handle);

        echo 'success';

        $conn->close();
        require_once('connection_rm_faast.php');

        $sql22 = "INSERT INTO ucp_activity (username,fullname,mobile,forgotpassword,requested,message,sms_message,type,requestedby,requestedthrough) VALUES ('". $username . "','". $fullname . "','".$mobile."','" . $password1 . "','" . $current_date . "','" . $buffer . "','".$msg."','1','" . $username . "','through app') ";
        mysqli_query($conn_rm_faast, $sql22);
    }
}
else{
    echo 'failure';
}

?>