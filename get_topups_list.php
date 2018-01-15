
<?php
require_once('connection_rm_faast.php');
require_once('connection.php');

$result = "Select * from rm_settings";
$check=mysqli_query($conn,$result);

while($tmp=mysqli_fetch_assoc($check)){
    $vat_percent = $tmp["vatpercent"];
}
$result = "Select * from topup";
$check=mysqli_query($conn_rm_faast,$result);

while($tmp=mysqli_fetch_assoc($check)){
    $row["name"] = $tmp["name"];
    $row["data_in_byte"] = $tmp["data"];
    $row["price"] = $tmp["price"];
    $row["tax"] = $row["price"] * ($vat_percent / 100);
    $row["vat_percent"] =  $vat_percent ;
    $row["final_amount"] = $tmp["price"] + ($row["price"] * ($vat_percent / 100))  ;
    $json_output[] = $row;
}

print(json_encode($json_output));
//echo '<pre>'; print_r($json_output);

?>