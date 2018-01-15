<?php
require_once('connection.php');
require('SMTPconfig.php');
require('SMTPclass.php');

$username = $_POST['username'];
//$username = "8549826883";
date_default_timezone_set("Asia/Kolkata");
$current_date = date('Y-m-d');

$result_1 = "Select firstname, lastname, email, mobile from rm_users where username = '".$username."' ";
$check_1=mysqli_query($conn,$result_1);

while($row_1 = mysqli_fetch_assoc($check_1))
{
    $firstname = $row_1["firstname"];
    $lastname = $row_1["lastname"];
    $email = $row_1["email"];
    $mobile = $row_1["mobile"];

    $fullname = $firstname." ".$lastname;
    $mb = trim($mobile);
    if($mb==null || empty($mb))
    {
        $mobile=$username;
    }
    else{
        $mobile=$mb;
    }

        $from = "namrata.s@credenceis.com";
        $to = "namrata.s@credenceis.com";
        $bcc = "sambarekarnamrata@gmail.com";

//    $from = "internet@faast.in";
//    $to = "internet@faast.in";
//    $bcc = "archive@faast.in";
    $subject = "FAAST Prime Membership request";
    $MESSAGE_BODY =
        "Dear admin,"."\n".
            "\t"."Please find the below details for FAAST Prime Membership request."."\n".
            "Username        : ".$username."\n".
            "Full Name       : ".$fullname."\n".
            "Mobile          : ".$mobile."\n\n".
            "Requested Date  : ".$current_date."\n".
            "Requested From  : App";

    $SMTPMail_1 = new SMTPClient ($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $bcc, $subject, $MESSAGE_BODY);

    $SMTPMail_1->SendMail();

    $msg1 = "Congratulation on signing up for FAAST Prime membership, your request has been received, once approved you will receive a notification. FAAST Broadband.";

    $msg = str_replace(' ', '%20', $msg1);

    $curl_handle=curl_init();

//    curl_setopt($curl_handle,CURLOPT_URL,'http://193.105.74.159/api/v3/sendsms/plain?user=smartt&password=8800755655&sender=FAAAST&SMSText='.$msg.'&type=longsms&GSM=91'.$mobile.'&output=json');
    curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
    curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
    $buffer = curl_exec($curl_handle);
    curl_close($curl_handle);

    $conn->close();
    require_once('connection_rm_faast.php');

    $sql23 = "INSERT INTO faast_prime_member (username) VALUES ('". $username . "') ";
    $check23 = mysqli_query($conn_rm_faast, $sql23);

    $sql22 = "INSERT INTO ucp_activity (username,fullname,mobile,requested,message,sms_message,type,requestedby,requestedthrough) VALUES ('". $username . "','". $fullname . "','".$mobile."','" . $current_date . "','" . $buffer . "','".$msg1."','17','" . $username . "','through app') ";
    $check22 = mysqli_query($conn_rm_faast, $sql22);

    echo "success";
}
?>