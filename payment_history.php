<?php

    require_once('connection.php');
    $startmonth = date('Y-m-01', strtotime("-2 months"));
    $end_month = date('Y-m-31');
    //$username = 'ajit.patil';
    $username = $_POST['username'];
    $sql = "select * from rm_invoices where username='" . $username . "' and paid != '0000-00-00' and paid >='$startmonth' and paid <= '$end_month' and invnum != '' order by paid DESC limit 3";
    $check = mysqli_query($conn, $sql);

    $response = array();
    while ($row = mysqli_fetch_assoc($check)) {
        $tmp["payment_date"] = $row["paid"];
        $amount = $row["amount"];
        $price = $amount * ($row["price"] + $row["tax"]);
        $tmp["payment_amount"] = $price;
        $tmp["payment_method"] = $row["remark"];
        array_push($response, $tmp);
    }
    print(json_encode($response));
?>