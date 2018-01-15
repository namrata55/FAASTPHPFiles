<?php
require_once('connection.php');
$username = $_POST['username'];
//$username="8549826883";
$q = "SELECT invnum, date, paymentopt, price, tax, amount FROM rm_invoices WHERE invnum != '' AND price > '0.00' AND paid = '0000-00-00' AND username = '" . $username . "' ";
$r = mysqli_query($conn, $q);
while ($row = mysqli_fetch_assoc($r)) {
    $tmp["invnum"] = $row["invnum"];
    $tmp["date"] = $row["date"];
    $tmp["paymentopt"] = $row["paymentopt"];
    $price = $row["price"];
    $tax = $row["tax"];
    $amount = $row["amount"];
    $total_amount = $amount * ($price + $tax);
    $tmp["total"] = $total_amount;
    $json_output[] = $tmp;
}

echo(json_encode($json_output));

?>