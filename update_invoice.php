<?php
require_once('connection.php');
$username = $_POST['username'];
$paymentid = $_POST['paymentid'];
//$payment_remark="Online";
$payment_remark=$_POST['remark'];
$payment_comment="through app";
$current_date= date("Y-m-d");
//
//$username = '8549826883';
//$paymentid = 'pay_6D88r2aq1b35GH';

$q="SELECT invnum, time FROM  rm_invoices WHERE invnum != ' ' AND username = '".$username."' AND paid = '0000-00-00' AND price > '0.00' ";
$r=mysqli_query($conn,$q);

while($row=mysqli_fetch_assoc($r)){
       $invnum= $row['invnum'];
        $inv_type = $row['time'];
    if($inv_type == 2){
        $sql = "UPDATE rm_invoices SET transid = '" . $paymentid . "' , paid = '" . $current_date . "' , time = '0', remark = '" . $payment_remark . "' , comment='" . $payment_comment . "' WHERE username ='" . $username . "' AND invnum ='" . $invnum . "' ";
        $row2 = mysqli_query($conn, $sql);
    }
    else {
        $sql = "UPDATE rm_invoices SET transid = '" . $paymentid . "' , paid = '" . $current_date . "' , remark = '" . $payment_remark . "' , comment='" . $payment_comment . "' WHERE username ='" . $username . "' AND invnum ='" . $invnum . "' ";
        $row2 = mysqli_query($conn, $sql);
    }
}

if ((mysqli_affected_rows($conn)) > 0) {

    $sql5 = "UPDATE rm_users SET enableuser = '1' WHERE username = '".$username."' ";
    $check2 = mysqli_query($conn,$sql5);

//    $sql6 = "UPDATE rm_users SET groupid = '4' WHERE username = '".$username."' ";
//    $check3 = mysqli_query($conn,$sql6);

    echo 'success';

}else{
    echo 'failure';
}
?>
