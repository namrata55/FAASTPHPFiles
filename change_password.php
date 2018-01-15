<?php

require_once('connection.php');

$old_password = $_POST['old_password'];
$new_password = $_POST['new_password'];
$new_password1 = md5($_POST['new_password']);
$old_password1= md5($_POST['old_password']);
$username = $_POST['username'];

$attribute= "Cleartext-Password";
$current_date = date('Y-m-d');

//$username ="8549826883";
//$old_password="nam1234";
//$old_password1= md5($old_password);
//$new_password="nam123";
//$new_password1 = md5($new_password);

$sql = "UPDATE rm_users SET password = '". $new_password1 . "' WHERE username ='" . $username . "' AND password = '" . $old_password1 . "' ";
mysqli_query($conn, $sql);

if ((mysqli_affected_rows($conn)) > 0) {

    $sql1="UPDATE radcheck SET value='" .$new_password. "' WHERE username='" . $username . "'  AND attribute='".$attribute."' ";
    mysqli_query($conn, $sql1);

    $sql3 = "SELECT firstname, lastname, mobile FROM rm_users where username = '".$username."' ";
    $check3 = mysqli_query($conn,$sql3);
    while($row1=mysqli_fetch_assoc($check3))
    {
        echo "success";
        $first_name = $row1["firstname"];
        $fullname = $first_name." ".$row1["lastname"];
        $mb = trim($row1['mobile']);
        if($mb==null || empty($mb))
        {
            $mobile=$username;
        }
        else{
            $mobile=$mb;
        }

        $msg1 = "Dear ".$first_name.", kindly ensure that you update your new password into your router as your password has been changed successfully. FAAST Broadband.";

        $msg = str_replace(' ', '%20', $msg1);

        $curl_handle=curl_init();

//        curl_setopt($curl_handle,CURLOPT_URL,'http://193.105.74.159/api/v3/sendsms/plain?user=smartt&password=8800755655&sender=FAAAST&SMSText='.$msg.'&type=longsms&GSM=91'.$mobile.'&output=json');
        curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
        curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
        $buffer = curl_exec($curl_handle);
        curl_close($curl_handle);

        $conn->close();
        require_once('connection_rm_faast.php');

        $sql22 = "INSERT INTO ucp_activity (username,fullname,mobile,oldpassword,newpassword,requested,message,sms_message,type,requestedby,requestedthrough) VALUES ('". $username . "','". $fullname . "','".$mobile."','". $old_password1 ."','" . $new_password1 . "','" . $current_date . "','" . $buffer . "','".$msg1."','5','" . $username . "','through app') ";
        $check22 = mysqli_query($conn_rm_faast, $sql22);
    }
} else {
    echo "Failed" . $conn->error;
}
?>
