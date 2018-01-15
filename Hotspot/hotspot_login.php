<?php

require_once('connection.php');
$username = $_POST['username'];
//$username="ka.mmdc";

$sql = "select * from rm_users where username='".$username."' and acctype='1'";

$check=mysqli_query($conn,$sql);

$row=mysqli_fetch_assoc($check);
if(isset($row)){
            echo "success";

}else{
    echo 'failure';
}
?>