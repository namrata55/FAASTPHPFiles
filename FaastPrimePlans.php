<?php
require_once('connection.php');
$current_plan_name = "HOME";
$current_plan_descr = "Belgaum";
$faast_prime_member = "1";

//$current_plan_name = $_POST['current_plan_name'];
//$current_plan_descr = $_POST['current_plan_descr'];
//$faast_prime_member = $_POST['faast_prime_member'];
$arr = explode(' ',trim($current_plan_name));

if($faast_prime_member != "1"){
    $result = "Select srvname, srvid, unitprice, trafficunitcomb, downrate, descr, nextsrvid from rm_services where enableburst = '1' and srvname NOT LIKE '%100%' and descr = '".$current_plan_descr."' order by srvid";
    $row = array();
    $json_output = array();
    $check=mysqli_query($conn,$result);
}
else{
    $result = "Select srvname, srvid, unitprice, trafficunitcomb, downrate, descr, nextsrvid from rm_services where descr = '".$current_plan_descr."' and enableburst = '1' order by srvid";
    $row = array();
    $json_output = array();
    $check=mysqli_query($conn,$result);
}

    if($arr[0]=='SMB' || $arr[0]=='HOME'){
        while($tmp=mysqli_fetch_assoc($check)){

            $downrate_up_1 = $tmp["downrate"]/1048576;
            $downrate_up_2 = ($downrate_up_1 * 9) / 100;
            $downrate_up_3 = $downrate_up_1 - $downrate_up_2;

            $postspeed_value =  $tmp["nextsrvid"];

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

            $a = $tmp["srvname"];
            $plan_first_word = explode(' ',trim($a));

            if (!strcmp($arr[0],$plan_first_word[0])){
                if (preg_match('/UL/',$a)){
                    $row["srvname"] = $tmp["srvname"];
                    $row["srvid"] = $tmp["srvid"];
                    $row["unitprice"] = $tmp["unitprice"];
                    $data = $tmp["trafficunitcomb"]/1024;
                    $row["trafficunitcomb"] = "Unlimited";
                    $speed = (($tmp["downrate"])/1024)/1024;
                    $speed_in_integer=number_format($speed, 0, '.', '');
                    $row["speed"] = round($downrate_up_3);
                    $row['postspeed'] = "";
                    $row["descr"] = $tmp["descr"];
                    $json_output[] = $row;
                }
                elseif(preg_match('/RED/',$a)){
                    $row["srvname"] = $tmp["srvname"];
                    $row["srvid"] = $tmp["srvid"];
                    $row["unitprice"] = $tmp["unitprice"];
                    $data = $tmp["trafficunitcomb"]/1024;
                    $speed = (($tmp["downrate"])/1024)/1024;
                    $speed_in_integer=number_format($speed, 0, '.', '');
                    $row["trafficunitcomb"] = round($downrate_up_3)." Mbps upto ".$data." GB";
                    $row["speed"] = round($downrate_up_3);
                    $postspeed1 = $postspeed;
                    $row['postspeed'] = "Unlimited@".$postspeed1."Mbps";
                    $row["descr"] = $tmp["descr"];
                    $json_output[] = $row;
                }
                else{
                    $row["srvname"] = $tmp["srvname"];
                    $row["srvid"] = $tmp["srvid"];
                    $row["unitprice"] = $tmp["unitprice"];
                    $data = $tmp["trafficunitcomb"]/1024;
                    $speed = (($tmp["downrate"])/1024)/1024;
                    $speed_in_integer=number_format($speed, 0, '.', '');
                    $row["trafficunitcomb"] = round($downrate_up_3)." Mbps upto ".$data." GB";
                    $row["speed"] = round($downrate_up_3);
                    $postspeed1 = $postspeed;
                    $row['postspeed'] = "Unlimited@".$postspeed1."Mbps";
                    $row["descr"] = $tmp["descr"];
                    $json_output[] = $row;
                }
            }
            else{
            }
        }
    }
    else{

        $downrate_up_1 = $tmp["downrate"]/1048576;
        $downrate_up_2 = ($downrate_up_1 * 9) / 100;
        $downrate_up_3 = $downrate_up_1 - $downrate_up_2;

        $result1 = "Select downrate from rm_services where srvid = '".$postspeed_value."' ";
        $check1=mysqli_query($conn,$result1);
        while($tmp=mysqli_fetch_assoc($check)){
            $final_postspeed_value = $postspeed_value / 1024;
        }

        $postspeed_10 = $final_postspeed_value * 10 / 100;

        if((($final_postspeed_value - $postspeed / 1024)/1024) < 0.5)
        {
            $postspeed = 0.5;
        }
        else{
            $postspeed = round((($final_postspeed_value - $postspeed / 1024)/1024));
        }

        while($tmp=mysqli_fetch_assoc($check)){
            $a = $tmp["srvname"];
            if (preg_match('/UL/',$a)){
                $row["srvname"] = $tmp["srvname"];
                $row["srvid"] = $tmp["srvid"];
                $row["unitprice"] = $tmp["unitprice"];
                $data = $tmp["trafficunitcomb"]/1024;
                $row["trafficunitcomb"] = "Unlimited";
                $speed = (($tmp["downrate"])/1024)/1024;
                $speed_in_integer=number_format($speed, 0, '.', '');
                $row["speed"] = round($downrate_up_3);
                $row['postspeed'] = "";
                $row["descr"] = $tmp["descr"];
                $json_output[] = $row;
            }
            else{
                $row["srvname"] = $tmp["srvname"];
                $row["srvid"] = $tmp["srvid"];
                $row["unitprice"] = $tmp["unitprice"];
                $data = $tmp["trafficunitcomb"]/1024;
                $speed = (($tmp["downrate"])/1024)/1024;
                $speed_in_integer=number_format($speed, 0, '.', '');
                $row["trafficunitcomb"] = round($downrate_up_3)." Mbps upto ".$data." GB";
                $row["speed"] = round($downrate_up_3);
                $postspeed1 = $postspeed;
                $row['postspeed'] = "Unlimited@".$postspeed1."Mbps";
                $row["descr"] = $tmp["descr"];
                $json_output[] = $row;
            }
        }
    }


//print(json_encode($json_output));
echo '<pre>'; print_r($json_output);
?>