<?php
require_once('connection.php');
//    $username = "ka.santosh.k1";
$username = $_POST['username'];
$used_data_gb1 = 0;
$faast_prime_status = "0";
$faast_prime_request = "0";
$flag_for_faast_prime = "0";
$month_start = date('Y-m-') . '01' . ' 00:00:00';
if (!$conn) {
    die('Could not connect:' . mysql_error());
}
$response = array();
$response["fixture"] = array();

$result_1 = "Select * from rm_users ru INNER JOIN rm_services rs ON ru.srvid = rs.srvid where username = '" . $username . "' ";
$check_1 = mysqli_query($conn, $result_1);

while ($row_1 = mysqli_fetch_assoc($check_1)) {
    $firstname = $row_1["firstname"];
    $lastname = $row_1["lastname"];
    $email = $row_1["email"];
    $mobile = $row_1["mobile"];
    $srvname = $row_1["srvname"];
    $city = $row_1["descr"];
    $group_id = $row_1["groupid"];
    $arr = explode(' ', trim($srvname));
//    if ($arr[0] == 'HOME' && $city == "Belgaum") {
    $flag_for_faast_prime = "1";
//    }
    $status = $row_1["enableuser"];
    $downlimit_rmusers = $row_1["downlimit"];
    $uplimit_rmusers = $row_1["uplimit"];
    $comblimit_rmusers = $row_1["comblimit"];
    $status = $row_1["enableuser"];
    $comb_limit = $row_1["trafficunitcomb"];
    $combquota = $row_1["combquota"];
    $total_data = $comb_limit / 1024;
    $total_data_gb = number_format(($total_data), 2, '.', '');
    if($group_id == '15' || $group_id == '19'){
        $acc_type = "inactive";
    }
    else{
        $acc_type = "active";
    }
}

$a = $srvname;
if (preg_match('/DAILY/',$a)){

    date_default_timezone_set('Asia/Kolkata');
    $currentDateStart = date('Y-m-d 00:00:00');
    $currentDateEnd = date('Y-m-d 23:59:59');

    $result = "select SUM(acctinputoctets) as acctinputoctets, SUM(acctoutputoctets) as acctoutputoctets, SUM(acctinputoctets + acctoutputoctets) as total_usage from radacct where username = '" . $username . "' and acctstarttime >= '".$currentDateStart."' and (acctstoptime <= '".$currentDateEnd."' or acctstoptime IS NULL)";
    $check = mysqli_query($conn, $result);

    while ($row_1 = mysqli_fetch_assoc($check)) {
        $total_usage_from_rad = $row_1['total_usage'];
    }

    $total_allocated_data_for_month = $combquota;
    $remaining_data = $total_allocated_data_for_month - $total_usage_from_rad;
    $used_data_of_month = $total_allocated_data_for_month - $remaining_data;

    $used_data1 = (($used_data_of_month / 1024) / 1024) / 1024;
    $used_data_in_gb = number_format(($used_data1), 2, '.', '');

    $remaining_data_of_month1 = (($remaining_data / 1024) / 1024) / 1024;
    $remaining_data_in_gb = number_format(($remaining_data_of_month1), 2, '.', '');

    $total_data1 = (($total_allocated_data_for_month / 1024) / 1024) / 1024;
    $total_data_in_gb = number_format(($total_data1), 2, '.', '');

    if ($remaining_data_in_gb < 0.0) {
        $remaining_data_of_month = 0.0;
    } else {
        $remaining_data_of_month = $remaining_data_in_gb;
    }
}
else{

    $result = "select SUM(acctinputoctets) as acctinputoctets, SUM(acctoutputoctets) as acctoutputoctets, SUM(acctinputoctets + acctoutputoctets) as total_usage from radacct where username = '" . $username . "'";
    $check = mysqli_query($conn, $result);

    while ($row_1 = mysqli_fetch_assoc($check)) {
        $total_usage_from_rad = $row_1['total_usage'];
    }

    $total_allocated_data_for_month = $comblimit_rmusers - ($downlimit_rmusers + $uplimit_rmusers);
    $remaining_data = $comblimit_rmusers - $total_usage_from_rad;
    $used_data_of_month = $total_allocated_data_for_month - $remaining_data;

    $used_data1 = (($used_data_of_month / 1024) / 1024) / 1024;
    $used_data_in_gb = number_format(($used_data1), 2, '.', '');

    $remaining_data_of_month1 = (($remaining_data / 1024) / 1024) / 1024;
    $remaining_data_in_gb = number_format(($remaining_data_of_month1), 2, '.', '');

    $total_data1 = (($total_allocated_data_for_month / 1024) / 1024) / 1024;
    $total_data_in_gb = number_format(($total_data1), 2, '.', '');

    if ($remaining_data_in_gb < 0.0) {
        $remaining_data_of_month = 0.0;
    } else {
        $remaining_data_of_month = $remaining_data_in_gb;
    }
}

$result2 = "SELECT SUM(amount * (price + tax)) as due_amount, min(paymentopt) as due_date FROM `rm_invoices` where invnum != ' ' and paid = '0000-00-00' and price > '0.00' and username = '" . $username . "' ";
$check2 = mysqli_query($conn, $result2);

if ((mysqli_affected_rows($conn)) > 0) {

    while ($row1 = mysqli_fetch_assoc($check2)) {

        if(!$row1["due_date"] == null){
            $current_date= date("Y-m-d");
            $due_date=$row1["due_date"];
            if($current_date > $due_date)
            {
                $due_date_value ="Immediate";
            }
            else
            {
                $due_date_value=$row1["due_date"];
            }
            $due_amount_final = $row1["due_amount"];
            $due_date_final =  $row1["due_date"];
        }
        else{
            $due_amount_final = "0.0";
            $due_date_final = "PAID";
        }
    }
}
else {
    $due_amount_final = "0.0";
    $due_date_final = "PAID";
}

$conn->close();
require_once('connection_rm_faast.php');

$sql22 = "select * from faast_prime_member where username = '" . $username . "' and status = '1'";
$check22 = mysqli_query($conn_rm_faast, $sql22);
$rowcount = mysqli_num_rows($check22);
if ($rowcount > 0) {
    $faast_prime_status = "1";
}

$sql33 = "select * from faast_prime_member where username = '" . $username . "'";
$check33 = mysqli_query($conn_rm_faast, $sql33);
$rowcount33 = mysqli_num_rows($check33);
if ($rowcount33 > 0){
    $faast_prime_request = "1";
}

$tmp["success"] = "1";
$tmp["firstname"] = $firstname;
$tmp["lastname"] = $lastname;
$tmp["mobile"] = $mobile;
$tmp["flag_for_faast_prime"] = $flag_for_faast_prime;
$tmp["faast_prime_member"] = $faast_prime_status;
$tmp["faast_prime_request"] = $faast_prime_request;
$tmp["email"] = $email;
$tmp["srvname"] = $srvname;
$tmp["account_status"] = $status;
$tmp["account_type"] = $acc_type;
$tmp["used_data"] = $used_data_in_gb;
$tmp["remaining_data"] = $remaining_data_of_month;
$tmp["total_data"] = $total_data_in_gb;

$tmp["due_date"] = $due_date_final;
$tmp["due_amount"] = $due_amount_final;

array_push($response["fixture"], $tmp);
echo json_encode($response);

//    echo '<pre>'; print_r($response);

?>