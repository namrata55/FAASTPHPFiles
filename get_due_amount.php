<?php
require_once('connection.php');

$username = $_POST['username'];
//$username="8549826883";
if(!$conn)
{
    die('Could not connect:'.mysql_error());
}

    $result_1 = "Select firstname, email, mobile from rm_users where username = '" . $username . "' ";
    $check_1 = mysqli_query($conn, $result_1);

while ($row_1 = mysqli_fetch_assoc($check_1)) {
    $firstname = $row_1["firstname"];
    $email = $row_1["email"];
    $mobile = $row_1["mobile"];
}

$response = array();
$response["fixture"] = array();

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
                $due_date_value=$row1["paymentopt"];
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
    $tmp["due_amount"] = $due_amount_final;
    $tmp["due_date"] = $due_date_final;
    $tmp["firstname"] = $firstname;
    $tmp["email"] = $email;
    $tmp["mobile"] = $mobile;
    $tmp["success"]="1";

    array_push($response["fixture"], $tmp);
    echo json_encode($response);

mysqli_close($conn);
?>

