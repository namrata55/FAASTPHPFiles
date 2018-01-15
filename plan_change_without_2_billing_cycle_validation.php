
<?php
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('connection.php');

//$username = $_POST['username'];
//$new_plan_id = $_POST['newsrvid'];
//$new_plan_name = $_POST['newsrvname'];
//$old_plan_name = $_POST['oldsrvname'];

date_default_timezone_set("Asia/Kolkata");
$new_plan_scheduled_date = date('Y-m-d', mktime(0, 0, 0, date('m')+1, 1, date('Y')));
$current_date = date('Y-m-d');

$old_plan_name ="HOME ";
$username = "8549826883";
$new_plan_name = "HOME FAASTEST";
$new_plan_id = "7";

        $sql1 = "INSERT INTO rm_changesrv (newsrvid,newsrvname,scheduledate,requestdate,username,requested) VALUES ('". $new_plan_id . "','". $new_plan_name . "','".$new_plan_scheduled_date."','". $current_date ."','" . $username . "','" . $username . "')";
        $check1 = mysqli_query($conn, $sql1);

        $sql3 = "SELECT firstname, lastname, mobile FROM rm_users where username = '".$username."' ";
        $check3 = mysqli_query($conn,$sql3);

        while($row1=mysqli_fetch_assoc($check3))
        {
            if ((mysqli_affected_rows($conn)) > 0) {
                $first_name = $row1["firstname"];
                $fullname = $first_name . " " . $row1["lastname"];
                $mb = trim($row1['mobile']);
                if ($mb == null || empty($mb)) {
                    $mobile = $username;
                } else {
                    $mobile = $mb;
                }

                $msg = "Dear " . $first_name . ", your request for plan change has been registered. OLD Plan: " . $old_plan_name . ", NEW Plan: " . $new_plan_name . ". The same will be effective from next billing cycle. FAAST Broadband.";

                $msg1 = str_replace(' ', '%20', $msg);

                $curl_handle = curl_init();

//                curl_setopt($curl_handle,CURLOPT_URL,'http://193.105.74.159/api/v3/sendsms/plain?user=smartt&password=8800755655&sender=FAAAST&SMSText='.$msg1.'&type=longsms&GSM=91'.$mobile.'&output=json');
                curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
                $buffer = curl_exec($curl_handle);
                curl_close($curl_handle);

                echo 'success';

                $conn->close();
                require_once('connection_rm_faast.php');
                $sql22 = "INSERT INTO ucp_activity (username,fullname,mobile,oldplan,newplan,requested,scheduled,message,sms_message,type,requestedby,requestedthrough) VALUES ('" . $username . "','" . $fullname . "','" . $mobile . "','" . $old_plan_name . "','" . $new_plan_name . "','" . $current_date . "','" . $new_plan_scheduled_date . "','" . $buffer . "','" . $msg . "','3','" . $username . "','through app') ";
                mysqli_query($conn_rm_faast, $sql22);

            }
            else{
                echo "failure";
            }
        }

?>