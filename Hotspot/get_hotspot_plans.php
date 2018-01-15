<?php
require_once('connection.php');

$result = "Select srvname, srvid, unitprice, trafficunitcomb, downrate,descr from rm_services where srvtype='1' order by srvid";

$check=mysqli_query($conn,$result);
while($tmp=mysqli_fetch_assoc($check)){
    $row["srvname"] = $tmp["srvname"];
    $row["srvid"] = $tmp["srvid"];
    $row["descr"] = $tmp["descr"];
    $row["unitprice"] = $tmp["unitprice"];
    $data = $tmp["trafficunitcomb"]/1024;
    $row["trafficunitcomb"] = $data;
    $speed = (($tmp["downrate"])/1024)/1024;
    $speed_in_integer=number_format($speed, 0, '.', '');
    $row["speed"] = $speed_in_integer;
    $json_output[] = $row;
}
print(json_encode($json_output));
?>