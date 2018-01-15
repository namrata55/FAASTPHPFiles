<?php
require_once('connection.php');
//$username="00:0c:29:15:4a:aa";
$username="16630695";
//$username = $_POST['username'];
$used_data_gb1=0;
$month_start = date('Y-m-').'01'.' 00:00:00';
if(!$conn)
{
    die('Could not connect:'.mysql_error());
}

$response = array();
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
    $comb_limit = $row_1["trafficunitcomb"];
    $total_data = $comb_limit;
    $total_data_gb = number_format(($total_data), 2, '.', '');
}

$result = "Select rs.srvname, rs.trafficunitcomb, racc.acctinputoctets, racc.acctoutputoctets from rm_users ru INNER JOIN rm_services rs ON ru.srvid = rs.srvid INNER JOIN radacct racc ON racc.username = ru.username where ru.username = '".$username."' AND racc.acctstarttime > '".$valid_from."'  ";
$check=mysqli_query($conn,$result);

if((mysqli_affected_rows($conn)) > 0 )
{
    $tmp = array();         // temporary array to create single match information
    $used_data = 0; $used_data_gb1=0;

    while($row = mysqli_fetch_assoc($check))
    {

        $srvname = $row["srvname"];
        $down_limit= $row["acctoutputoctets"];
        $up_limit= $row["acctinputoctets"];
        $comb_limit=$row["trafficunitcomb"];
        $total_data=$comb_limit;
        //$total_data_gb=number_format(($total_data), 2, '.', '');


        $up_limit_1 = (($up_limit/1024)/1024);
        $down_limit_1 = (($down_limit/1024)/1024);

        $used_data += $down_limit_1 + $up_limit_1;
        $used_data_gb1=number_format(($used_data), 2, '.', '');

    }

    $remaining_data = $total_data_gb - $used_data_gb1;
    if($remaining_data<0.0)
    {
        $remaining_data1 =0.0;
    }
    else{
        $remaining_data1 = $total_data_gb - $used_data_gb1;
    }

    if($remaining_data1 > 1024){
        $remaining_final = number_format(($remaining_data1/1024), 2, '.', '');
        $total_data_final = number_format(($total_data_gb/1024), 2, '.', '');
    }else{
        $remaining_final = $remaining_data1;
        $total_data_final = $total_data_gb;
    }

    $tmp["firstname"] = $firstname;
    $tmp["lastname"] = $lastname;
    $tmp["srvname"] = $srvname;
    $tmp["valid_from"] = $valid_from;
    $tmp["valid_upto"] = $valid_upto;
    $tmp["account_status"] = $status;
    $tmp["used_data"] = $used_data_gb1;
    $tmp["remaining_data"] = $remaining_final;
    $tmp["total_data"] =  $total_data_final;
    $tmp["mobile"] =  $mobile;
    $tmp["email"] =  $email;

    $tmp["success"] = 1;

    array_push($response["fixture"], $tmp);

    echo json_encode($response);
//    print(json_encode($response));

}
else
{
    $tmp["success"]=1;
    $tmp["firstname"] = $firstname;
    $tmp["lastname"] = $lastname;
    $tmp["mobile"] =  $mobile;
    $tmp["email"] =  $email;
    $tmp["srvname"] = $srvname;
    $tmp["valid_from"] = $valid_from;
    $tmp["valid_upto"] = $valid_upto;
    $tmp["account_status"] = $status;
    $tmp["used_data"] = 0;
    $tmp["remaining_data"] = $total_data_gb;
    $tmp["total_data"] = $total_data_gb;
    array_push($response["fixture"], $tmp);
    echo json_encode($response);
}
mysqli_close($conn);
?>