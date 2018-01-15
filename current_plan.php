<?php
require_once('connection.php');

$username = $_POST['username'];
//$username = "8549826883";
$json_output = array();
$json_output["fixture"] = array();
$final_postspeed_value = "";

$result = "Select rs.srvid, rs.srvname, rs.unitprice, rs.trafficunitcomb, rs.downrate, rs.nextsrvid, rs.dailynextsrvid, rs.descr, rs.combquota from rm_users ru, rm_services rs where ru.srvid=rs.srvid and ru.username = '".$username."' ";

$check=mysqli_query($conn,$result);
while($row=mysqli_fetch_assoc($check)){

    $downrate_up_1 = $row["downrate"]/1048576;
    $downrate_up_2 = ($downrate_up_1 * 9) / 100;
    $downrate_up_3 = $downrate_up_1 - $downrate_up_2;
    $downrate_up_4 = round($downrate_up_3);

    $postspeed_value =  $row["nextsrvid"];
    $postspeed_value_daily =  $row["dailynextsrvid"];


    $result1 = "Select downrate from rm_services where srvid = '".$postspeed_value."' ";
    $check1=mysqli_query($conn,$result1);
    while($tmp1=mysqli_fetch_assoc($check1)){
        $final_postspeed_value = $tmp1["downrate"];
    }

    $postspeed_10 = ($final_postspeed_value) * 10 / 100;

    if(((($final_postspeed_value - $postspeed_10) / 1024)/1024) < 0.5)
    {
        $postspeed = 0.5;
    }
    else{
        $postspeed = round((($final_postspeed_value - $postspeed_10 / 1024)/1024)/1024);
    }


    if($postspeed_value_daily > 0){

        $result2 = "Select downrate from rm_services where srvid = '".$postspeed_value_daily."' ";
        $check2 = mysqli_query($conn,$result2);
        while($tmp2 = mysqli_fetch_assoc($check2)){
            $final_postspeed_value_for_daily_plans = $tmp2["downrate"];
            //echo $final_postspeed_value_for_daily_plans;
        }

        $postspeed_daily = ($final_postspeed_value_for_daily_plans) * 10 / 100;

        if(((($final_postspeed_value_for_daily_plans - $postspeed_daily) / 1024)/1024) < 0.5)
        {
            $postspeed_for_daily_plans = '512 Kbps';
        }
        else{
            $postspeed_for_daily_plans = round((($final_postspeed_value_for_daily_plans - $postspeed_daily / 1024)/1024)/1024).' Mbps';
        }
    }

    $a = $row["srvname"];
    if (preg_match('/UL/',$a)){
        $tmp["service_name"] = $row["srvname"];
        $tmp["service_price"] = $row["unitprice"];
        $tmp["total_data"] = "unlimited";
        $speed = (($row["downrate"])/1024)/1024;
        $speed_in_integer=number_format($speed, 0, '.', '');
        $tmp["speed"] = "upto ".$downrate_up_4." Mbps";
        $tmp["post_speed"] = " ";
        $tmp["srvid"] = $row["srvid"];
        $tmp["descr"] = $row["descr"];
        $tmp["success"]=1;
        array_push( $json_output["fixture"], $tmp);

    }
    elseif(preg_match('/RED/',$a)){
        $tmp["service_name"] = $row["srvname"];
        $tmp["service_price"] = $row["unitprice"];
        $data_in_mb = $row["trafficunitcomb"];
        $data_in_gb = $data_in_mb /1024;
        $speed = (($row["downrate"])/1024)/1024;
        $speed_in_integer = number_format($speed, 0, '.', '');
        $tmp["speed"] = "upto ".$downrate_up_4." Mbps";
        $tmp["total_data"] ="".$downrate_up_4." Mbps upto ".$data_in_gb." GB";
        $tmp["srvid"] = $row["srvid"];
        $tmp["descr"] = $row["descr"];
        $tmp["post_speed"] = "Unlimited@".$postspeed."Mbps";
        $tmp["success"]=1;
        array_push( $json_output["fixture"], $tmp);
    }

    elseif(preg_match('/DAILY/',$a)){

        $tmp["service_name"] = $row["srvname"];
        $tmp["service_price"] = $row["unitprice"];
        $data_in_mb = $row["combquota"];
        $data_in_gb = $data_in_mb /1024/1024/1024;
        $speed = (($row["downrate"])/1024)/1024;
        $speed_in_integer = number_format($speed, 0, '.', '');
        $tmp["speed"] = "upto ".$downrate_up_4." Mbps";
        $tmp["total_data"] ="".$downrate_up_4." Mbps upto ".$data_in_gb." GB Daily";
        $tmp["srvid"] = $row["srvid"];
        $tmp["descr"] = $row["descr"];
        $tmp["post_speed"] = "Unlimited@".$postspeed_for_daily_plans." post ".$data_in_gb." GB Daily Limit";
        $tmp["success"]=1;
        array_push( $json_output["fixture"], $tmp);

//        $tmp["service_name"] = $tmp["srvname"];
//        $row["srvid"] = $tmp["srvid"];
//        $row["unitprice"] = $tmp["unitprice"];
//        $data = $tmp["combquota"]/1073741824;
//        $speed = (($tmp["downrate"])/1024)/1024;
//        $speed_in_integer=number_format($speed, 0, '.', '');
//        $row["trafficunitcomb"] = round($downrate_up_3)." Mbps upto ".$data." GB Daily";
//        $row["speed"] = round($downrate_up_3);
//        $postspeed1 = $postspeed_for_daily_plans;
//        $row['postspeed'] = "Unlimited@".$postspeed1." post ".$data." Daily Limit";
//        $row["descr"] = $tmp["descr"];
//        $tmp["success"]=1;
//        array_push( $json_output["fixture"], $tmp);
    }

    else{
        $tmp["service_name"] = $row["srvname"];
        $tmp["service_price"] = $row["unitprice"];
        $data_in_mb = $row["trafficunitcomb"];
        $data_in_gb = $data_in_mb /1024;
        $speed = (($row["downrate"])/1024)/1024;
        $speed_in_integer = number_format($speed, 0, '.', '');
        $tmp["speed"] = "upto ".$downrate_up_4." Mbps";
        $tmp["total_data"] ="".$downrate_up_4." Mbps upto ".$data_in_gb." GB";
        $tmp["srvid"] = $row["srvid"];
        $tmp["descr"] = $row["descr"];
        $tmp["post_speed"] = "Unlimited@".$postspeed."Mbps";
        $tmp["success"]=1;
        array_push( $json_output["fixture"], $tmp);
    }
}
print(json_encode($json_output));
//echo '<pre>'; print_r($json_output);

mysqli_close($conn);

?>