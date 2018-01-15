<?php
require_once('connection.php');
require('SMTPconfig.php');
require('SMTPclass.php');
$username = $_POST['username'];
//$username = "8549826883";

date_default_timezone_set("Asia/Kolkata");
$current_date = date('Y-m-d');

$sql = "select srvid,firstname, lastname, comblimit, phone, mobile, address, city, zip, country, state, owner, credits from rm_users where username='".$username."' ";
$check = mysqli_query($conn,$sql);
$row = mysqli_fetch_assoc($check);

if(isset($row)) {
    $srvid = $row["srvid"] ;
    $first_name = $row["firstname"];
    $last_name = $row["lastname"];
    $comb_limit = $row["comblimit"];
    $fullname = $first_name." ".$last_name;
    $phone = $row["phone"];
    $mb = $row["mobile"];
    if($mb==null || empty($mb))
    {
        $mobile = $username;
    }
    else{
        $mobile = $mb;
//         $mobile="9036892369";
    }
    $address = $row["address"];
    $city = $row["city"];
    $zip = $row["zip"];
    $country = $row["country"];
    $state = $row["state"];
    $owner = $row["owner"];
    $balance = $row["credits"];
    $amount = '1.00';

//    $conn->close();
    require_once('connection_rm_faast.php');

    $sql1 = "Select nextsrvid, nextsrvname, price from faast_prime_plan_list where srvid = '".$srvid."' ";
    $check1=mysqli_query($conn_rm_faast,$sql1);

    while($row1 = mysqli_fetch_assoc($check1)) {
        $new_plan_id = $row1["nextsrvid"];
        $new_plan_name = $row1["nextsrvname"];
        $new_plan_price = $row1["price"];
    }
    $new_plan_scheduled_date = date('Y-m-d', mktime(0, 0, 0, date('m')+1, 1, date('Y')));

//    $conn_rm_faast->close();
    require_once('connection.php');

    $sql2 = "INSERT INTO rm_changesrv (newsrvid,newsrvname,scheduledate,requestdate,username,requested) VALUES ('". $new_plan_id . "','". $new_plan_name . "','".$new_plan_scheduled_date."','". $current_date ."','" . $username . "','" . $username . "')";
    $check2 = mysqli_query($conn, $sql2);

    if((mysqli_affected_rows($conn)) > 0) {

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
        $expirdate = date('Y-m-d', mktime(0, 0, 0, date('m'), date('t'), date('Y') + 1));
        $service = "Prime Membership Charges from".$new_plan_scheduled_date."-".$expirdate;

        $sql4 = "select vatpercent from rm_settings ";
        $check4 = mysqli_query($conn,$sql4);
        $row4 = mysqli_fetch_assoc($check4);
        if(isset($row4)) {
            $vatpercent = $row4["vatpercent"];
        }
        $tax = (($new_plan_price * $vatpercent)/100);

        $sql_inv = "INSERT INTO rm_invoices(invnum,managername,username,date,expiration,captotal,service,amount,address,city,zip,country,state,fullname,paymentopt,paymode,price,tax,vatpercent,balance,phone,mobile)
        VALUES ('".$final_invnum."','".$owner."','".$username."','".$current_date."','".$final_expiration."','1','".$service."','1.00','".$address."','".$city."','".$zip."','".$country."','".$state."','".$fullname."','".$final_paymentopt."','1','".$new_plan_price."','".$tax."','".$vatpercent."','".$balance."','".$phone."','".$mobile."')";
        $check11 = mysqli_query($conn,$sql_inv);

        $msg = "Dear ".$first_name.", Congratulations, your FAAST Prime membership request has been activated, an invoice for the same will be added to your account, your Prime membership benefits will commence from the ".$new_plan_scheduled_date.", thank you. FAAST Broadband.";

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

            $from = "internet@faast.in";
            $to = "internet@faast.in";
            $bcc = "archive@faast.in";
            $subject = "FAAST Prime activation";
            $MESSAGE_BODY =
                "Dear admin,"."\n".
                    "\t"."Please find the below details for FAAST Prime activation."."\n".
                    "Username           : ".$username."\n".
                    "Full Name          : ".$fullname."\n".
                    "Mobile             : ".$mobile."\n\n".
                    "FAAST Prime price  :".$new_plan_price."\n".
                    "Requested Date     : ".$current_date."\n".
                    "Requested From: App";

            $SMTPMail_1 = new SMTPClient ($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $bcc, $subject, $MESSAGE_BODY);

            $SMTPMail_1->SendMail();

        require_once('connection_rm_faast.php');

        $activated_at = date('Y-m-d H:ia');

        $sql22 = "INSERT INTO ucp_activity (username,fullname,mobile,sms_message,type,requestedby,requestedthrough) VALUES ('". $username . "','". $fullname . "','".$mobile."','" . $buffer . "','".$msg."','17','" . $username . "','through app') ";
        mysqli_query($conn_rm_faast, $sql22);

        $sql23 = "INSERT INTO faast_prime_member (username,status,activated_at) VALUES ('" . $username . "','1','".$activated_at."') ";
        $check23 = mysqli_query($conn_rm_faast, $sql23);

        echo 'success';
    }
    else{
        print "failure ";
    }
}
else{
    print "failure";
}
?>
