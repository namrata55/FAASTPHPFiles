<?php
require_once('connection_rm_faast.php');
$username = $_POST['username'];
$paymentid = $_POST['paymentid'];
$credit = $_POST['credit'];
$debit = $_POST['debit'];
$balance = $_POST['balance'];
$invnum = $_POST['invnum'];
$current_date = date("Y-m-d");

//$username = "8549826883";
//$paymentid = "ndwjqnd";
//$credit = "0.00";
//$debit = "591.00";
//$balance = "0.00";
//$invnum = "sjqwnsz";
//$current_date = date("Y-m-d");

$query = "INSERT INTO wallet(username,invnum,credit,debit,balance,created_through,created_by) VALUES ('" . $username . "','" . $invnum . "','" . $credit . "','" . $debit . "','" . $balance . "','through app','" . $username . "') ";
$executed_query = mysqli_query($conn_rm_faast, $query);
if ($executed_query) {
    echo "success";
} else {
    echo "failure";
}
?>