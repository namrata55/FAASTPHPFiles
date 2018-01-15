<?php

header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('connection.php');
require('SMTPconfig.php');
require('SMTPclass.php');

//$username = "8549826883";
//$reactivation_request = "no";

$username = $_POST['username'];
$reactivation_request = $_POST['reactivation_request'];

if ($reactivation_request != '') {

    if ($reactivation_request == 'yes') {

        $sql5 = "UPDATE rm_users SET groupid = '4' WHERE username = '" . $username . "' ";
        $check2 = mysqli_query($conn, $sql5);
        echo 'success';
    } else {

        $support_subject = "Cancellation support ticket";
        $support_comment = "Customer cleared pending amount and opted for deactivation of service, team, kindly recover the device";
        $digits = 4;
        $support_id = rand(pow(10, $digits - 1), pow(10, $digits) - 1);

        date_default_timezone_set("Asia/Kolkata");
        $request_date1 = date('Y-m-d h:i:s A');

        $sql1 = "SELECT * FROM rm_users ru INNER JOIN rm_services rs ON ru.srvid = rs.srvid where username = '" . $username . "' ";
        $check1 = mysqli_query($conn, $sql1);
        while ($row_1 = mysqli_fetch_assoc($check1)) {
            $firstname = $row_1["firstname"];
            $lastname = $row_1["lastname"];
            $fullname = $firstname . " " . $lastname;
            $email = $row_1["email"];
            $customer_mobile = $row_1["mobile"];
            $srvname = $row_1["srvname"];
            $city = $row_1["city"];
            $address = $row_1["address"];
            $owner = $row_1["owner"];
        }
        $sql6 = "SELECT mobile, company FROM rm_managers where managername = '" . $owner . "'";
        $check6 = mysqli_query($conn, $sql6);
        while ($row_6 = mysqli_fetch_assoc($check6)) {
            $partner_mobile1 = $row_6["mobile"];
            $company_name = $row_6["company"];
        }

        $conn->close();

        require_once('connection_rm_faast.php');
        $sql2 = "INSERT INTO support (support_id,username,full_name,email,mobile,plan_name,city,address,subject,status_id,assigned_to,createdby) VALUES ('" . $support_id . "','" . $username . "','" . $fullname . "','" . $email . "','" . $customer_mobile . "','" . $srvname . "','" . $city . "','" . $address . "','" . $support_subject . "','1','" . $owner . "','" . $username . "')";
        $check11 = mysqli_query($conn_rm_faast, $sql2);
        if ($check11) {

            $sql4 = "SELECT id FROM support ORDER BY id DESC LIMIT 1";
            $check4 = mysqli_query($conn_rm_faast, $sql4);
            while ($row4 = mysqli_fetch_assoc($check4)) {
                $id = $row4["id"];
            }

            $db_support_comment = $support_comment . "-user created ticket";
            $sql5 = 'INSERT INTO support_comments (support_user_id,comment,commented_by,type) VALUES ("' . $id . '","' . $support_comment . '","' . $username . '","1")';
            mysqli_query($conn_rm_faast, $sql5);

            $sql7 = "SELECT * FROM partners where username = '" . $owner . "' ";
            $check7 = mysqli_query($conn_rm_faast, $sql7);
            while ($row7 = mysqli_fetch_assoc($check7)) {
                $partner_mobile2 = $row7["pers_phone_no"];
            }

            $buffer1 = '';
            $buffer2 = '';
            $buffer3 = '';
            $msg = '';
            $msg1 = '';
            $msg2 = '';
            $msg3 = '';
            if (!empty($customer_mobile)) {
                if ($owner == 'admin') {
                    $msg = "Dear " . $firstname . ", a Ticket ID:" . $support_id . ", issue:" . $support_subject . ", has been generated for your FAAST Broadband issue, the same will be resolved within maximum 24 business Hours.";

                    $msg1 = str_replace(' ', '%20', $msg);

                    $curl_handle = curl_init();

//            curl_setopt($curl_handle,CURLOPT_URL,'http://193.105.74.159/api/v3/sendsms/plain?user=smartt&password=8800755655&sender=FAAAST&SMSText='.$msg1.'&type=longsms&GSM=91'.$customer_mobile.'&output=json');
                    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                    $buffer1 = curl_exec($curl_handle);
                    curl_close($curl_handle);
                } else {
                    $msg = "Dear " . $firstname . ", a Ticket ID:" . $support_id . ", issue:" . $support_subject . ", has been generated for your FAAST Broadband issue, the same will be resolved by " . $company_name . ":" . $partner_mobile1 . " within maximum of 24 business Hours.";

                    $msg1 = str_replace(' ', '%20', $msg);

                    $curl_handle = curl_init();

//            curl_setopt($curl_handle,CURLOPT_URL,'http://193.105.74.159/api/v3/sendsms/plain?user=smartt&password=8800755655&sender=FAAAST&SMSText='.$msg1.'&type=longsms&GSM=91'.$customer_mobile.'&output=json');
                    curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
                    curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                    $buffer1 = curl_exec($curl_handle);
                    curl_close($curl_handle);
                }
            }


            if (!empty($partner_mobile1)) {
                $msg2 = "Dear Team, a new Support Ticket ID:" . $support_id . ", issue:" . $support_subject . ", has been assigned to you, customer name:" . $firstname . ", username:" . $username . ".";

                $msg_2 = str_replace(' ', '%20', $msg2);

                $curl_handle = curl_init();

//                curl_setopt($curl_handle, CURLOPT_URL, 'http://193.105.74.159/api/v3/sendsms/plain?user=smartt&password=8800755655&sender=FAAAST&SMSText=' . $msg_2 . '&type=longsms&GSM=91' . $partner_mobile1 . '&output=json');
                curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                $buffer2 = curl_exec($curl_handle);
                curl_close($curl_handle);
            }

            if (!empty($partner_mobile2)) {
                $msg3 = "Dear Team, a new Support Ticket ID:" . $support_id . ", issue:" . $support_subject . ", has been assigned to you, customer name:" . $firstname . ", username:" . $username . ".";

                $msg_3 = str_replace(' ', '%20', $msg3);

                $curl_handle = curl_init();

//        curl_setopt($curl_handle,CURLOPT_URL,'http://193.105.74.159/api/v3/sendsms/plain?user=smartt&password=8800755655&sender=FAAAST&SMSText='.$msg_3.'&type=longsms&GSM=91'.$partner_mobile2.'&output=json');
                curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                $buffer3 = curl_exec($curl_handle);
                curl_close($curl_handle);
            }

//    $from = "internet@faast.in";
//    $to = "internet@faast.in";
//    $bcc = "archive@faast.in";

            $from = "namrata.s@credenceis.com";
            $to = "namrata.s@credenceis.com";
            $bcc = "sambarekarnamrata@gmail.com";
            $subject = "" . $support_subject;
            $MESSAGE_BODY =
                "Dear admin," . "\n" .
                    "\t" . "Please find the below details for support ticket raised." . "\n" .
                    "Username           : " . $username . "\n" .
                    "Full Name          : " . $fullname . "\n" .
                    "Mobile             : " . $customer_mobile . "\n\n" .
                    "Support Id         : " . $support_id . "\n" .
                    "Support Subject    : " . $support_subject . "\n" .
                    "Support Comment    : " . $support_comment . "\n" .
                    "Assigned To        : " . $owner . "\n" .
                    "Requested Date     : " . $request_date1 . "\n" .
                    "Requested From: App";

            $SMTPMail_1 = new SMTPClient ($SmtpServer, $SmtpPort, $SmtpUser, $SmtpPass, $from, $to, $bcc, $subject, $MESSAGE_BODY);

            $SMTPMail_1->SendMail();

            $sql8 = "INSERT INTO ucp_activity (username,fullname,mobile,supportsubject,supportcomment,message,sms_message,message_2,sms_message_2,type,requestedby,requestedthrough) VALUES ('" . $username . "','" . $fullname . "','" . $customer_mobile . "','" . $support_subject . "','" . $support_comment . "','" . $buffer1 . "','" . $msg . "','" . $buffer2 . "','" . $msg2 . "','6','" . $username . "','through app') ";
            mysqli_query($conn_rm_faast, $sql8);

            echo 'success';
        }
    }
} else {
    echo 'failure';
}
?>