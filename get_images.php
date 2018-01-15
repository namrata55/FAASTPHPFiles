<?php
require_once('connection_rm_faast.php');

$result = "Select * from android_image_slider where status = '1' ";
$check = mysqli_query($conn_rm_faast,$result);

while($tmp = mysqli_fetch_assoc($check)){
    $row["id"] = $tmp["id"];
    $row["image_url"] = $tmp["image_url"];
    $json_output[] = $row;
}
print(json_encode($json_output));
?>