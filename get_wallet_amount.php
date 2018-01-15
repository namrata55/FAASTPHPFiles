<?php
require_once('connection_rm_faast.php');
$username = $_POST['username'];
//$username = "sxw";
$response["output"] = array();

$wallet_query = "SELECT balance FROM wallet WHERE username = '" . $username . "' ORDER BY id DESC LIMIT 1";
$query_output2 = mysqli_query($conn_rm_faast, $wallet_query);
$row2 = mysqli_fetch_assoc($query_output2);
if (isset($row2)) {
        $tmp["balance"] = $row2["balance"];
        $tmp["result"] = "1";
        array_push($response["output"], $tmp);
} else {
    $tmp["balance"] = "0.00";
    $tmp["result"] = "1";
    array_push($response["output"], $tmp);
}
echo json_encode($response);

?>