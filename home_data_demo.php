<?php
require_once('connection.php');
$username="8549826883";
//$username = $_POST['username'];
$used_data_gb1=0;
$faast_prime_status = "0";
$flag_for_faast_prime = "0";
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
    $city = $row_1["descr"];
    $arr = explode(' ',trim($srvname));
    if( $arr[0]=='HOME' && $city == "Belgaum"){
        $flag_for_faast_prime = "1";
    }
    $status = $row_1["enableuser"];
    $downlimit_rmusers = $row_1["downlimit"];
    $uplimit_rmusers = $row_1["uplimit"];
    $comblimit_rmusers = $row_1["comblimit"];
    $status = $row_1["enableuser"];
    $comb_limit = $row_1["trafficunitcomb"];
    $total_data = $comb_limit/1024;
    $total_data_gb = number_format(($total_data), 2, '.', '');
}

$result = "select SUM(acctinputoctets) as acctinputoctets, SUM(acctoutputoctets) as acctoutputoctets, SUM(acctinputoctets + acctoutputoctets) as total_usage from radacct where username = '".$username."'";
$check = mysqli_query($conn,$result);

while($row_1 = mysqli_fetch_assoc($check))
{
    $total_usage_from_rad = $row_1['total_usage'];
}

$total_allocated_data_for_month = $comblimit_rmusers - ($downlimit_rmusers + $uplimit_rmusers);
$remaining_data = $comblimit_rmusers - $total_usage_from_rad;
$used_data_of_month = $total_allocated_data_for_month - $remaining_data;

$used_data1 = (($used_data_of_month/1024)/1024)/1024;
$used_data_in_gb = number_format(($used_data1), 2, '.', '');

$remaining_data_of_month1 = (($remaining_data/1024)/1024)/1024;
$remaining_data_in_gb = number_format(($remaining_data_of_month1), 2, '.', '');

$total_data1 = (($total_allocated_data_for_month/1024)/1024)/1024;
$total_data_in_gb = number_format(($total_data1), 2, '.', '');

if($remaining_data_in_gb < 0.0){
    $remaining_data_of_month = 0.0;
}
else{
    $remaining_data_of_month = $remaining_data_in_gb;
}

$conn->close();
require_once('connection_rm_faast.php');

$sql22 = "select * from faast_prime_member where username = '".$username."' and status = '1'";
$check22 = mysqli_query($conn_rm_faast, $sql22);
$rowcount=mysqli_num_rows($check22);

if($rowcount > 0 ){
    $faast_prime_status = "1";
}

$result_image = "Select * from image_updated_date";
$check_image = mysqli_query($conn_rm_faast,$result_image);
while($row_image = mysqli_fetch_assoc($check_image)){
    $images_uploaded_date = $row_image["creation"];
}


$tmp["success"]="1";
$tmp["firstname"] = $firstname;
$tmp["lastname"] = $lastname;
$tmp["mobile"] =  $mobile;
$tmp["flag_for_faast_prime"] =  $flag_for_faast_prime;
$tmp["faast_prime_member"] =  $faast_prime_status;
$tmp["email"] =  $email;
$tmp["srvname"] = $srvname;
$tmp["account_status"] = $status;
$tmp["used_data"] = $used_data_in_gb;
$tmp["remaining_data"] = $remaining_data_of_month;
$tmp["total_data"] = $total_data_in_gb;
$tmp["images_uploaded_date"] = $images_uploaded_date;

array_push($response["fixture"], $tmp);
print json_encode($response);

?>