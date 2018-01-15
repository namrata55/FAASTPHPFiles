<?php
require_once('connection.php');
//$username="00:0c:29:15:4a:aa";
//$username="02:00:00:00:00:00";
$username = $_POST['username'];
$used_data_gb1=0;
$month_start = date('Y-m-').'01'.' 00:00:00';
if(!$conn)
{
    die('Could not connect:'.mysql_error());
}

$response = array();
$used_data_in_mb=0;
$response["fixture"] = array();

$result_1 = "Select * from rm_users ru INNER JOIN rm_services rs ON ru.srvid = rs.srvid where username = '".$username."' ";
$check_1=mysqli_query($conn,$result_1);

while($row_1 = mysqli_fetch_assoc($check_1))
{
    $firstname = $row_1["firstname"];
    $lastname = $row_1["lastname"];
    $email = $row_1["email"];
    $mobile = $row_1["mobile"];
    $srvname = $row_1["srvname"];
    $valid_from = $row_1["createdon"];
    $valid_upto = $row_1["expiration"];
    $status = $row_1["enableuser"];
    $total_data_in_mb = $row_1["trafficunitcomb"];
    $total_data_in_gb = $total_data_in_mb/1024;
}

$result = "Select rs.srvname, rs.trafficunitcomb, racc.acctinputoctets, racc.acctoutputoctets from rm_users ru INNER JOIN rm_services rs ON ru.srvid = rs.srvid INNER JOIN radacct racc ON racc.username = ru.username where ru.username = '".$username."' AND racc.acctstarttime > '".$valid_from."'  ";
$check=mysqli_query($conn,$result);

if((mysqli_affected_rows($conn)) > 0 )
{
    $tmp = array();
    while($row = mysqli_fetch_assoc($check))
    {
        $down_limit= $row["acctoutputoctets"];
        $up_limit= $row["acctinputoctets"];
        $comb_limit=$row["trafficunitcomb"];
        $up_limit_mb = (($up_limit/1024)/1024);
        $down_limit_mb = (($down_limit/1024)/1024);
        $used_data_mb = $up_limit_mb + $down_limit_mb;
        $remaining_data_in_mb1 = $total_data_in_mb - $used_data_mb;
        $remaining_data_in_mb = (number_format(($remaining_data_in_mb1), 2, '.', ''));

        $total_data_in_gb_graph = ($comb_limit/1024)."GB";

        if($remaining_data_in_mb > 1024){
            $remaining_data_graph1 = ($remaining_data_in_mb1/1024);
            $remaining_data_graph = (number_format(($remaining_data_graph1), 2, '.', ''))."GB";
        }
        else{
            $remaining_data_graph1 = $remaining_data_in_mb;
            $remaining_data_graph = (number_format(($remaining_data_graph1), 2, '.', ''))."MB";
        }
    }

    $tmp["firstname"] = $firstname;
    $tmp["lastname"] = $lastname;
    $tmp["srvname"] = $srvname;
    $tmp["valid_from"] = $valid_from;
    $tmp["valid_upto"] = $valid_upto;
    $tmp["account_status"] = $status;
    $tmp["mobile"] =  $mobile;
    $tmp["email"] =  $email;
    $tmp["remaining_data_digit"] =  $remaining_data_in_mb;
    $tmp["total_data_digit"] =  $comb_limit;
    $tmp["used_data_digit_mb"] = $tmp["total_data_digit"] - $tmp["remaining_data_digit"] ;
    $tmp["total_data_graph"] =  $total_data_in_gb_graph;
    $tmp["remaining_data_graph"] =  $remaining_data_graph;
    $tmp["success"] = 1;
    array_push($response["fixture"], $tmp);
    echo json_encode($response);
//    echo '<pre>'; print_r($response);
}
else
{
    $tmp["firstname"] = $firstname;
    $tmp["lastname"] = $lastname;
    $tmp["srvname"] = $srvname;
    $tmp["valid_from"] = $valid_from;
    $tmp["valid_upto"] = $valid_upto;
    $tmp["account_status"] = $status;
    $tmp["mobile"] =  $mobile;
    $tmp["email"] =  $email;
    $tmp["remaining_data_digit"] =  $total_data_in_mb;
    $tmp["total_data_digit"] =  $total_data_in_mb;
    $tmp["total_data_graph"] =  $total_data_in_gb."GB";
    $tmp["remaining_data_graph"] =  $total_data_in_mb."GB";
    $tmp["success"] = 1;

    array_push($response["fixture"], $tmp);

    echo json_encode($response);
}
mysqli_close($conn);
?>