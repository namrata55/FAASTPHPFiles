<?php
require_once('connection.php');

//$current_plan_name = "HOME DAILY";
//$current_plan_descr = "Belgaum";
//$faast_prime_member = "1";

$current_plan_name = $_POST['current_plan_name'];
$current_plan_descr = $_POST['current_plan_descr'];
$faast_prime_member = $_POST['faast_prime_member'];
$arr = explode(' ', trim($current_plan_name));
$json_output3 = array();

if ($faast_prime_member != "1") {
    $result = "Select srvname, srvid, unitprice, trafficunitcomb, downrate, descr, nextsrvid, dailynextsrvid, combquota from rm_services where enableburst = '1' and srvname NOT LIKE '%100%' and descr = '" . $current_plan_descr . "' order by srvid";
    $row = array();
    $json_output = array();
    $check = mysqli_query($conn, $result);
} else {
    $result = "Select srvname, srvid, unitprice, trafficunitcomb, downrate, descr, nextsrvid, dailynextsrvid, combquota from rm_services where descr = '" . $current_plan_descr . "' and enableburst = '1' and srvname LIKE '%100%' and srvname NOT LIKE '%DAILY 100%' order by srvid";
    $row = array();
    $json_output = array();
    $check = mysqli_query($conn, $result);

    $result3 = "Select srvname, srvid, unitprice, trafficunitcomb, downrate, descr, nextsrvid, dailynextsrvid, combquota from rm_services where descr = '" . $current_plan_descr . "' and enableburst = '1' and srvname LIKE '%DAILY 100%' order by srvid";
    $row3 = array();
    $json_output3 = array();
    $check3 = mysqli_query($conn, $result3);
}

if ($arr[0] == 'SMB' || $arr[0] == 'HOME') {
    while ($tmp = mysqli_fetch_assoc($check)) {

        $downrate_up_1 = $tmp["downrate"] / 1048576;
        $downrate_up_2 = ($downrate_up_1 * 9) / 100;
        $downrate_up_3 = $downrate_up_1 - $downrate_up_2;

        $postspeed_value = $tmp["nextsrvid"];
        $postspeed_value_daily = $tmp["dailynextsrvid"];

        $result1 = "Select downrate from rm_services where srvid = '" . $postspeed_value . "' ";
        $check1 = mysqli_query($conn, $result1);
        while ($tmp1 = mysqli_fetch_assoc($check1)) {
            $final_postspeed_value = $tmp1["downrate"];
        }

        $postspeed_10 = ($final_postspeed_value) * 10 / 100;

        if (((($final_postspeed_value - $postspeed_10) / 1024) / 1024) < 0.5) {
            $postspeed = '512 Kbps';
        } else {
            $postspeed = round((($final_postspeed_value - $postspeed_10 / 1024) / 1024) / 1024) . ' Mbps';
        }


        if ($postspeed_value_daily > 0) {

            $result2 = "Select downrate from rm_services where srvid = '" . $postspeed_value_daily . "' ";
            $check2 = mysqli_query($conn, $result2);
            while ($tmp2 = mysqli_fetch_assoc($check2)) {
                $final_postspeed_value_for_daily_plans = $tmp2["downrate"];
                //echo $final_postspeed_value_for_daily_plans;
            }

            $postspeed_daily = ($final_postspeed_value_for_daily_plans) * 10 / 100;

            if (((($final_postspeed_value_for_daily_plans - $postspeed_daily) / 1024) / 1024) < 0.5) {
                $postspeed_for_daily_plans = '512 Kbps';
            } else {
                $postspeed_for_daily_plans = round((($final_postspeed_value_for_daily_plans - $postspeed_daily / 1024) / 1024) / 1024) . ' Mbps';
            }
        }


        $a = $tmp["srvname"];
        $plan_first_word = explode(' ', trim($a));

        if (!strcmp($arr[0], $plan_first_word[0])) {
            if (preg_match('/UL/', $a)) {
                $row["srvname"] = $tmp["srvname"];
                $row["srvid"] = $tmp["srvid"];
                $row["unitprice"] = $tmp["unitprice"];
                $data = $tmp["trafficunitcomb"] / 1024;
                $row["trafficunitcomb"] = "Unlimited";
                $speed = (($tmp["downrate"]) / 1024) / 1024;
                $speed_in_integer = number_format($speed, 0, '.', '');
                $row["speed"] = round($downrate_up_3);
                $row['postspeed'] = "";
                $row["descr"] = $tmp["descr"];
                $row["total_monthly_data"] = "";
                $json_output[] = $row;
            } elseif (preg_match('/RED/', $a)) {
                $row["srvname"] = $tmp["srvname"];
                $row["srvid"] = $tmp["srvid"];
                $row["unitprice"] = $tmp["unitprice"];
                $data = $tmp["trafficunitcomb"] / 1024;
                $speed = (($tmp["downrate"]) / 1024) / 1024;
                $speed_in_integer = number_format($speed, 0, '.', '');
                $row["trafficunitcomb"] = round($downrate_up_3) . " Mbps upto " . $data . " GB Monthly";
                $row["speed"] = round($downrate_up_3);
                $postspeed1 = $postspeed;
                $row['postspeed'] = "Unlimited@" . $postspeed1;
                $row["descr"] = $tmp["descr"];
                $row["total_monthly_data"] = "";
                $json_output[] = $row;
            } elseif (preg_match('/DAILY/', $a)) {
                $row["srvname"] = $tmp["srvname"];
                $row["srvid"] = $tmp["srvid"];
                $row["unitprice"] = $tmp["unitprice"];
                $data = $tmp["combquota"] / 1073741824;
                $speed = (($tmp["downrate"]) / 1024) / 1024;
                $speed_in_integer = number_format($speed, 0, '.', '');
                $row["trafficunitcomb"] = round($downrate_up_3) . " Mbps upto " . $data . " GB Daily";
                $row["speed"] = round($downrate_up_3);
                $postspeed1 = $postspeed_for_daily_plans;
                $row['postspeed'] = "Unlimited@" . $postspeed1 . " post " . $data . " Daily Limit";
                $row["descr"] = $tmp["descr"];
                $row["total_monthly_data"] = "";
                $json_output[] = $row;
            } else {
                $row["srvname"] = $tmp["srvname"];
                $row["srvid"] = $tmp["srvid"];
                $row["unitprice"] = $tmp["unitprice"];
                $data = $tmp["trafficunitcomb"] / 1024;
                $speed = (($tmp["downrate"]) / 1024) / 1024;
                $speed_in_integer = number_format($speed, 0, '.', '');
                $row["trafficunitcomb"] = round($downrate_up_3) . " Mbps upto " . $data . " GB Monthly";
                $row["speed"] = round($downrate_up_3);
                $postspeed1 = $postspeed;
                $row['postspeed'] = "Unlimited@" . $postspeed1;
                $row["descr"] = $tmp["descr"];
                $row["total_monthly_data"] = "";
                $json_output[] = $row;
            }
        } else {
        }
    }

    if(isset($check3)) {

        while ($tmp3 = mysqli_fetch_assoc($check3)) {


            $downrate_up_1 = $tmp3["downrate"] / 1048576;
            $downrate_up_2 = ($downrate_up_1 * 9) / 100;
            $downrate_up_3 = $downrate_up_1 - $downrate_up_2;

            $postspeed_value = $tmp3["nextsrvid"];
            $postspeed_value_daily = $tmp3["dailynextsrvid"];

            $result1 = "Select downrate from rm_services where srvid = '" . $postspeed_value . "' ";
            $check1 = mysqli_query($conn, $result1);
            while ($tmp1 = mysqli_fetch_assoc($check1)) {
                $final_postspeed_value = $tmp1["downrate"];
            }

            $postspeed_10 = ($final_postspeed_value) * 10 / 100;

            if (((($final_postspeed_value - $postspeed_10) / 1024) / 1024) < 0.5) {
                $postspeed = '512 Kbps';
            } else {
                $postspeed = round((($final_postspeed_value - $postspeed_10 / 1024) / 1024) / 1024) . ' Mbps';
            }


            if ($postspeed_value_daily > 0) {

                $result2 = "Select downrate from rm_services where srvid = '" . $postspeed_value_daily . "' ";
                $check2 = mysqli_query($conn, $result2);
                while ($tmp2 = mysqli_fetch_assoc($check2)) {
                    $final_postspeed_value_for_daily_plans = $tmp2["downrate"];
                    //echo $final_postspeed_value_for_daily_plans;
                }

                $postspeed_daily = ($final_postspeed_value_for_daily_plans) * 10 / 100;

                if (((($final_postspeed_value_for_daily_plans - $postspeed_daily) / 1024) / 1024) < 0.5) {
                    $postspeed_for_daily_plans = '512 Kbps';
                } else {
                    $postspeed_for_daily_plans = round((($final_postspeed_value_for_daily_plans - $postspeed_daily / 1024) / 1024) / 1024) . ' Mbps';
                }
            }


            $a = $tmp3["srvname"];
            $plan_first_word = explode(' ', trim($a));

            if (!strcmp($arr[0], $plan_first_word[0])) {
                if (preg_match('/UL/', $a)) {
                    $row3["srvname"] = $tmp3["srvname"];
                    $row3["srvid"] = $tmp3["srvid"];
                    $row3["unitprice"] = $tmp3["unitprice"];
                    $data = $tmp3["trafficunitcomb"] / 1024;
                    $row3["trafficunitcomb"] = "Unlimited";
                    $speed = (($tmp3["downrate"]) / 1024) / 1024;
                    $speed_in_integer = number_format($speed, 0, '.', '');
                    $row3["speed"] = round($downrate_up_3);
                    $row3['postspeed'] = "";
                    $row3["descr"] = $tmp3["descr"];
                    $row3["total_monthly_data"] = "";
                    $json_output3[] = $row3;
                } elseif (preg_match('/RED/', $a)) {
                    $row3["srvname"] = $tmp3["srvname"];
                    $row3["srvid"] = $tmp3["srvid"];
                    $row3["unitprice"] = $tmp3["unitprice"];
                    $data = $tmp3["trafficunitcomb"] / 1024;
                    $speed = (($tmp3["downrate"]) / 1024) / 1024;
                    $speed_in_integer = number_format($speed, 0, '.', '');
                    $row3["trafficunitcomb"] = round($downrate_up_3) . " Mbps upto " . $data . " GB Monthly";
                    $row3["speed"] = round($downrate_up_3);
                    $postspeed1 = $postspeed;
                    $row3['postspeed'] = "Unlimited@" . $postspeed1;
                    $row3["descr"] = $tmp3["descr"];
                    $row3["total_monthly_data"] = "";
                    $json_output3[] = $row3;
                } elseif (preg_match('/DAILY/', $a)) {
                    $row3["srvname"] = $tmp3["srvname"];
                    $row3["srvid"] = $tmp3["srvid"];
                    $row3["unitprice"] = $tmp3["unitprice"];
                    $data = $tmp3["combquota"] / 1073741824;
                    $speed = (($tmp3["downrate"]) / 1024) / 1024;
                    $speed_in_integer = number_format($speed, 0, '.', '');
                    $row3["trafficunitcomb"] = round($downrate_up_3) . " Mbps upto " . $data . " GB Daily";
                    $row3["speed"] = round($downrate_up_3);
                    $postspeed1 = $postspeed_for_daily_plans;
                    $row3['postspeed'] = "Unlimited@" . $postspeed1 . " post " . $data . " Daily Limit";
                    $row3["descr"] = $tmp3["descr"];
                    $row3["total_monthly_data"] = "";
                    $json_output3[] = $row3;
                } else {
                    $row3["srvname"] = $tmp3["srvname"];
                    $row3["srvid"] = $tmp3["srvid"];
                    $row3["unitprice"] = $tmp3["unitprice"];
                    $data = $tmp3["trafficunitcomb"] / 1024;
                    $speed = (($tmp3["downrate"]) / 1024) / 1024;
                    $speed_in_integer = number_format($speed, 0, '.', '');
                    $row3["trafficunitcomb"] = round($downrate_up_3) . " Mbps upto " . $data . " GB Monthly";
                    $row3["speed"] = round($downrate_up_3);
                    $postspeed1 = $postspeed;
                    $row3['postspeed'] = "Unlimited@" . $postspeed1;
                    $row3["descr"] = $tmp3["descr"];
                    $row3["total_monthly_data"] = "";
                    $json_output3[] = $row3;
                }
            } else {
            }
        }
    }

    $json_output = array_merge($json_output,$json_output3);

} else {

    $downrate_up_1 = $tmp["downrate"] / 1048576;
    $downrate_up_2 = ($downrate_up_1 * 9) / 100;
    $downrate_up_3 = $downrate_up_1 - $downrate_up_2;

    $result1 = "Select downrate from rm_services where srvid = '" . $postspeed_value . "' ";
    $check1 = mysqli_query($conn, $result1);
    while ($tmp = mysqli_fetch_assoc($check)) {
        $final_postspeed_value = $postspeed_value / 1024;
    }

    $postspeed_10 = $final_postspeed_value * 10 / 100;

    if ((($final_postspeed_value - $postspeed / 1024) / 1024) < 0.5) {
        $postspeed = '512 Kbps';
    } else {
        $postspeed = round((($final_postspeed_value - $postspeed / 1024) / 1024)) . ' Mbps';
    }

    if ($postspeed_value_daily > 0) {

        $result2 = "Select downrate from rm_services where srvid = '" . $postspeed_value_daily . "' ";
        $check2 = mysqli_query($conn, $result2);
        while ($tmp2 = mysqli_fetch_assoc($check2)) {
            $final_postspeed_value_for_daily_plans = $tmp2["downrate"];
            //echo $final_postspeed_value_for_daily_plans;
        }

        $postspeed_daily = ($final_postspeed_value_for_daily_plans) * 10 / 100;

        if (((($final_postspeed_value_for_daily_plans - $postspeed_daily) / 1024) / 1024) < 0.5) {
            $postspeed_for_daily_plans = '512 Kbps';
        } else {
            $postspeed_for_daily_plans = round((($final_postspeed_value_for_daily_plans - $postspeed_daily / 1024) / 1024) / 1024) . ' Mbps';
        }
    }
    while ($tmp = mysqli_fetch_assoc($check)) {
        $a = $tmp["srvname"];
        if (preg_match('/UL/', $a)) {
            $row["srvname"] = $tmp["srvname"];
            $row["srvid"] = $tmp["srvid"];
            $row["unitprice"] = $tmp["unitprice"];
            $data = $tmp["trafficunitcomb"] / 1024;
            $row["trafficunitcomb"] = "Unlimited";
            $speed = (($tmp["downrate"]) / 1024) / 1024;
            $speed_in_integer = number_format($speed, 0, '.', '');
            $row["speed"] = round($downrate_up_3);
            $row['postspeed'] = "";
            $row["descr"] = $tmp["descr"];
            $row["total_monthly_data"] = "";
            $json_output[] = $row;
        } elseif (preg_match('/RED/', $a)) {
            $row["srvname"] = $tmp["srvname"];
            $row["srvid"] = $tmp["srvid"];
            $row["unitprice"] = $tmp["unitprice"];
            $data = $tmp["trafficunitcomb"] / 1024;
            $speed = (($tmp["downrate"]) / 1024) / 1024;
            $speed_in_integer = number_format($speed, 0, '.', '');
            $row["trafficunitcomb"] = round($downrate_up_3) . " Mbps upto " . $data . " GB Monthly";
            $row["speed"] = round($downrate_up_3);
            $postspeed1 = $postspeed;
            $row['postspeed'] = "Unlimited@" . $postspeed1;
            $row["descr"] = $tmp["descr"];
            $row["total_monthly_data"] = "";
            $json_output[] = $row;
        } elseif (preg_match('/DAILY/', $a)) {
            $row["srvname"] = $tmp["srvname"];
            $row["srvid"] = $tmp["srvid"];
            $row["unitprice"] = $tmp["unitprice"];
            $data = $tmp["combquota"] / 1073741824;
            $speed = (($tmp["downrate"]) / 1024) / 1024;
            $speed_in_integer = number_format($speed, 0, '.', '');
            $row["trafficunitcomb"] = round($downrate_up_3) . " Mbps upto " . $data . " GB Daily";
            $row["speed"] = round($downrate_up_3);
            $postspeed1 = $postspeed_for_daily_plans;
            $row['postspeed'] = "Unlimited@" . $postspeed1 . " post " . $data . " Daily Limit";
            $row["descr"] = $tmp["descr"];
            $row["total_monthly_data"] = "";
            $json_output[] = $row;
        } else {
            $row["srvname"] = $tmp["srvname"];
            $row["srvid"] = $tmp["srvid"];
            $row["unitprice"] = $tmp["unitprice"];
            $data = $tmp["trafficunitcomb"] / 1024;
            $speed = (($tmp["downrate"]) / 1024) / 1024;
            $speed_in_integer = number_format($speed, 0, '.', '');
            $row["trafficunitcomb"] = round($downrate_up_3) . " Mbps upto " . $data . " GB Monthly";
            $row["speed"] = round($downrate_up_3);
            $postspeed1 = $postspeed;
            $row['postspeed'] = "Unlimited@" . $postspeed1;
            $row["descr"] = $tmp["descr"];
            $row["total_monthly_data"] = "";
            $json_output[] = $row;
        }
    }
}
print(json_encode($json_output));
//echo '<pre>';
//print_r($json_output);
?>