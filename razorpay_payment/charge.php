<?php
//These should be commented out in production
error_reporting(E_ALL);
ini_set('display_errors', 1);

//Load API, Ideally it should installed by composer and autoloaded if your project uses composer
require('Razorpay.php');

use Razorpay\Api\Api;

$key_id = $_POST['key_id'];
$key_secret = $_POST['key_secret'];
$amount = $_POST['amount'];

//Use your key_id and key secret
$api = new Api($key_id, $key_secret);

//This is submited by the checkout form
if (isset($_POST['razorpay_payment_id']) === false)
{
    die("Payment id not provided");
}

$id = $_POST['razorpay_payment_id'];

//capture Rs 5100
$payment = $api->payment->fetch($id)->capture(array('amount' => $amount));

//echo response
//echo json_encode($payment->toArray());

/* Start My Custom code */

$data = $payment->toArray();

if(isset($data['id'])){

    $payment_id = $data['id'];
    $capture_status = $data['status'];
    $payment_mode = $data['method'];

    if( $capture_status != ''){
        echo '1';
    }else{
        echo '0';
    }
}else{
    echo '0';
}

/* End My Custom code */

//Payment is captured, do whatever else you need to do
// Mark order as done using the submitted hidden field
//$shopping_order_id = $_POST['shopping_order_id'];
// markOrderAsDone($shopping_order_id);
?>