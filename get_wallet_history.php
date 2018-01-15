<?php
require_once('connection_rm_faast.php');
$username = $_POST['username'];
//$username = "9620520420";
$query = "SELECT credit, debit, balance, created_at FROM wallet WHERE username = '" . $username . "' ORDER BY id DESC LIMIT 5";
$query_output = mysqli_query($conn_rm_faast, $query);
while ($row = mysqli_fetch_assoc($query_output)) {
    $tmp["credit"] = $row["credit"];
    $tmp["debit"] = $row["debit"];
    $tmp["balance"] = $row["balance"];
    $tmp["date"] = date('d-m-Y', strtotime($row["created_at"]));
    $json_output[] = $tmp;
}

echo(json_encode($json_output));
?>