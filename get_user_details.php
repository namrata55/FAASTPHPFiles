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
$tmp["firstname"] = $firstname;
$tmp["email"] = $email;
$tmp["mobile"] = $mobile;
$tmp["success"]="1";

array_push($response["fixture"], $tmp);
echo json_encode($response);

mysqli_close($conn);
?>

