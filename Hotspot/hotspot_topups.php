<?php
header('Access-Control-Allow-Origin: *');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once('connection.php');
require('SMTPconfig.php');
require('SMTPclass.php');

$username = $_POST['username'];
$topups_data = $_POST['topupsdata'];
$topups_price = $_POST['topupsprice'];

$current_date = date('Y-m-d');

//$username ="8549826883";
//$topups_data = "10 GB";
//$topups_price = "175";

$topups_price_float = $topups_price.".00";

$sql = "SELECT mobile, firstname, lastname FROM rm_users where username = '".$username."' ";
$check = mysqli_query($conn,$sql);
$row_cnt = $check->num_rows;
if($row_cnt > 0){
    while($row=mysqli_fetch_assoc($check)){

    }
}
else{
    echo 'failure';
}
?>