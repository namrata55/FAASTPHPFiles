<?php

require_once('connection.php');
$username = $_POST['username'];
$password = md5($_POST['password']);
$gcmid = $_POST['gcmid'];
//$username="8549826883";
//$password = md5("nam123");
//$gcmid="66666666666666";

$sql = "select groupid from rm_users where username='" . $username . "' and password='" . $password . "'";
$check = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($check);
if (isset($row)) {

    if ($row["groupid"] == '4' || $row["groupid"] == '6' || $row["groupid"] == '16' || $row["groupid"] == '9') {
        $sql2 = "UPDATE rm_users SET contractid = '" . $gcmid . "' WHERE username='" . $username . "' ";
        $check2 = mysqli_query($conn, $sql2);
        echo "success";
    } else if ($row["groupid"] == '19' || $row["groupid"] == '15') {
        $response = array();
        $response["fixture"] = array();

        $result2 = "SELECT SUM(amount * (price + tax)) as due_amount, min(paymentopt) as due_date FROM `rm_invoices` where invnum != ' ' and paid = '0000-00-00' and price > '0.00' and username = '" . $username . "' ";
        $check2 = mysqli_query($conn, $result2);

        if ((mysqli_affected_rows($conn)) > 0) {

            while ($row1 = mysqli_fetch_assoc($check2)) {
                $due_amount_final = $row1["due_amount"];
            }
        } else {
            $due_amount_final = "0";
        }

        if ($row["groupid"] == '15' && $due_amount_final > 0) {
            echo "cancelled";
        } else if ($row["groupid"] == '19' && $due_amount_final > 0) {
            echo "deactivated";
        } else {
            echo "failure";
        }
    }

} else {
    echo "credencialsfailure";
}
?>