<?php
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('connection.php');
require('SMTPconfig.php');
require('SMTPclass.php');
//
$username = $_POST['username'];
$topups_data = $_POST['topupsdata'];
$topups_price = $_POST['topupsprice'];
$topups_data_in_bytes = $_POST['topupsdatainbytes'];
$topups_vat = $_POST['topupsvat'];
$topups_tax = $_POST['topupstax'];

date_default_timezone_set("Asia/Kolkata");
$current_date = date('Y-m-d');

$timezone = new DateTimeZone("Asia/Kolkata" );
$request_date = new DateTime();
$request_date->setTimezone($timezone );
$request_date1 = $request_date->format('Y-m-d H:ia');

//$username ="7411146462";
//$topups_data_in_bytes = "5368709120.00";
//$topups_data = "5GB";
//$topups_price = "100";
//$topups_vat = 15;
//$topups_tax = 15;

$topups_price_float = $topups_price.".00";
$topups_data_service = $topups_data." Top Up";

$sql = "SELECT firstname, lastname, comblimit, phone, mobile, address, city, zip, country, state, owner, credits FROM rm_users where username = '".$username."' ";
$check = mysqli_query($conn,$sql);
while($tmp=mysqli_fetch_assoc($check)){
    $first_name = $tmp["firstname"];
    $last_name = $tmp["lastname"];
    $comb_limit = $tmp["comblimit"];
    $fullname = $first_name." ".$last_name;
    $phone = $tmp["phone"];
    $mb = $tmp["mobile"];
    if($mb==null || empty($mb))
    {
        $mobile=$username;
    }
    else{
        $mobile=$mb;
//         $mobile="9036892369";
    }
    $address = $tmp["address"];
    $city = $tmp["city"];
    $zip = $tmp["zip"];
    $country = $tmp["country"];
    $state = $tmp["state"];
    $owner = $tmp["owner"];
    $balance = $tmp["credits"];
}

$result = "select SUM(acctinputoctets) as acctinputoctets, SUM(acctoutputoctets) as acctoutputoctets, SUM(acctinputoctets + acctoutputoctets) as total_usage from radacct where username = '".$username."'";
$check = mysqli_query($conn,$result);

while($row_1 = mysqli_fetch_assoc($check))
{
    $total_usage = $row_1['total_usage'];
}
    $sql3 = "SELECT invnum FROM rm_invoices where invnum!='' ORDER BY id DESC LIMIT 1 ";
    $check3 = mysqli_query($conn,$sql3);
    while($tmp3=mysqli_fetch_assoc($check3)){
        $last_inv_num = $tmp3["invnum"];
    }
    $invnum_1 = explode('-',$last_inv_num);
    $val2 = $invnum_1[1];
    $numlength = strlen((string)$val2);
    $var = str_pad(++$val2,$numlength,'0',STR_PAD_LEFT);

    $final_invnum = date('Y').'-'.$var;
    $final_expiration = date('Y-m-01', strtotime('+1 month'));
    $final_paymentopt = date('Y-m-07', strtotime('+1 month'));
    $amount = '1.00';

    $comb_limit1 = $topups_data_in_bytes + $total_usage;
    $comb_limit2 = $topups_data_in_bytes + $comb_limit;

        $comb_limit_final = max($comb_limit1,$comb_limit2);

    $sql_inv = "INSERT INTO rm_invoices(invnum,managername,username,date,expiration,captotal,service,amount,address,city,zip,country,state,fullname,paymentopt,paymode,price,tax,vatpercent,balance,phone,mobile)
        VALUES ('".$final_invnum."','".$owner."','".$username."','".$current_date."','".$final_expiration."','1','".$topups_data_service."','1.00','".$address."','".$city."','".$zip."','".$country."','".$state."','".$fullname."','".$final_paymentopt."','1','".$topups_price."','".$topups_tax."','".$topups_vat."','".$balance."','".$phone."','".$mobile."')";
    $check11 = mysqli_query($conn,$sql_inv);

    if($check11){
        $sql_rm_users="UPDATE rm_users SET comblimit='" .$comb_limit_final. "' WHERE username='" . $username . "'";
        mysqli_query($conn, $sql_rm_users);
        if ((mysqli_affected_rows($conn)) > 0) {
            $msg = "Dear ".$first_name.", your top up request for ".$topups_data." has been activated, kindly restart your router for the new speed to take effect. FAAST Broadband.";

            $msg1 = str_replace(' ', '%20', $msg);

            $curl_handle=curl_init();

            curl_setopt($curl_handle,CURLOPT_URL,'http://193.105.74.159/api/v3/sendsms/plain?user=smartt&password=8800755655&sender=FAAAST&SMSText='.$msg1.'&type=longsms&GSM=91'.$mobile.'&output=json');
            curl_setopt($curl_handle,CURLOPT_CONNECTTIMEOUT,2);
            curl_setopt($curl_handle,CURLOPT_RETURNTRANSFER,1);
            $buffer = curl_exec($curl_handle);
            curl_close($curl_handle);

//            $from = "namrata.s@credenceis.com";
//            $to = "namrata.s@credenceis.com";
//            $bcc = "sambarekarnamrata@gmail.com";

//            $from = "internet@faast.in";
//            $to = "internet@faast.in";
//            $bcc = "archive@faast.in";
//            $subject = "Topup activation request";
//            $MESSAGE_BODY =
//                "Dear admin,"."\n".
//                    "\t"."Please find the below details for topups activation request."."\n".
//                    "Username           : ".$username."\n".
//                    "Full Name          : ".$fullname."\n".
//                    "Mobile             : ".$mobile."\n\n".
//                    "Topup plan         : ".$topups_data."\n".
//                    "Topup price         :".$topups_price_float."\n".
//                    "Requested Date     : ".$request_date1."\n".
//                    "Requested From: App";
//
//            $SMTPMail_1 = new SMTPClient ($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $bcc, $subject, $MESSAGE_BODY);
//
//            $SMTPMail_1->SendMail();

            $conn->close();

            require_once('connection_rm_faast.php');

            $sql22 = "INSERT INTO ucp_activity (username,fullname,mobile,topupdata,topupprice,requested,message,sms_message,type,requestedby,requestedthrough) VALUES ('". $username . "','". $fullname . "','".$mobile."','". $topups_data ."','" . $topups_price_float . "','" . $current_date . "','" . $buffer . "','".$msg."','2','" . $username . "','through app') ";
            mysqli_query($conn_rm_faast, $sql22);
            echo 'success';
        }
    else{
        echo 'failure';
    }
}
else{
    echo 'failure';
}
?>