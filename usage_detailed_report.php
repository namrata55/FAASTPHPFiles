<?php

require_once('connection.php');

if (!$conn) {
    die('Could not connect: ' . mysql_error());
} else {

    $json_output = array();
    $username = $_POST['username'];
//    $username = '9620520420';
    $total_data_gb = null;
    $timezone = new DateTimeZone("Asia/Kolkata");
    $date = new DateTime();
    $date->setTimezone($timezone);
    $current_date = $date->format('Y-m-d H:i:s');
    $firstdate = $date->format('Y-m-01 00:00:00');
    $sql = "SELECT * FROM radacct where username = '" . $username . "' AND acctstarttime <= '" . $current_date . "' AND acctstarttime >= '" . $firstdate . "'  ORDER BY acctstarttime DESC ";

    $r = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($r)) {

        if ($row["acctstoptime"] == NULL) {
            $tmp["end_time"] = "Online";
            $start_time = new DateTime($row["acctstarttime"]);
            $end_time = new DateTime($row["acctstoptime"]);
            $session_time = $end_time->diff($start_time);
            if ($session_time->format('%a') > 1) {
                $tmp["session_time"] = $session_time->format('%a days %H:%i hrs.');
            } else if ($session_time->format('%a') == 1) {
                $tmp["session_time"] = $session_time->format('%a day %H:%i hrs.');
            } else {
                $tmp["session_time"] = $session_time->format('%H:%i hrs.');
            }

            $upload_kb = (($row["acctinputoctets"]) / 1024);
            $download_kb = (($row["acctoutputoctets"]) / 1024);
            $total_kb = ($upload_kb + $download_kb);

            $upload_data = convert_data_1($upload_kb);
            $download_data = convert_data_1($download_kb);
            $total_data = convert_data_1($total_kb);

            $tmp["upload_data"] = $upload_data;
            $tmp["download_data"] = $download_data;
            $tmp["consumed_data"] = $total_data;

            $tmp['start_date'] = date('d/m/Y', strtotime($row["acctstarttime"]));
            $total_data_gb += $total_kb;
            $total_data_gb = $total_data_gb + $total_data;
            $grand_data = convert_data_2($total_data_gb);
            $tmp['grand_total_usage'] = $grand_data;
        } else {
            $tmp["start_time"] = $row["acctstarttime"];
            $tmp["end_time"] = $row["acctstoptime"];
            $start_time = new DateTime($row["acctstarttime"]);
            $end_time = new DateTime($row["acctstoptime"]);
            $session_time = $end_time->diff($start_time);
            if ($session_time->format('%a') > 1) {
                $tmp["session_time"] = $session_time->format('%a days %H:%i hrs.');
            } else if ($session_time->format('%a') == 1) {
                $tmp["session_time"] = $session_time->format('%a day %H:%i hrs.');
            } else {
                $tmp["session_time"] = $session_time->format('%H:%i hrs.');
            }

            $upload_kb = (($row["acctinputoctets"]) / 1024);
            $download_kb = (($row["acctoutputoctets"]) / 1024);
            $total_kb = ($upload_kb + $download_kb);

            $upload_data = convert_data_1($upload_kb);
            $download_data = convert_data_1($download_kb);
            $total_data = convert_data_1($total_kb);

            $tmp["upload_data"] = $upload_data;
            $tmp["download_data"] = $download_data;
            $tmp["consumed_data"] = $total_data;

            $tmp['start_date'] = date('d/m/Y', strtotime($row["acctstarttime"]));
            $total_data_gb += $total_kb;
            $total_data_gb = $total_data_gb + $total_data;
            $grand_data = convert_data_2($total_data_gb);
            $tmp['grand_total_usage'] = $grand_data;
        }

        $json_output[] = $tmp;
    }
    print(json_encode($json_output));

//    echo '<pre>'; print_r($json_output);
}
mysqli_close($conn);

function convert_data_1($data)
{
    if ($data > 1023) {
        $result = number_format(($data / 1024), 2, '.', '') . 'MB';
        if ($result > 1023) {
            return $result = number_format(($result / 1024), 2, '.', '') . 'GB';
        } else {
            return $result;
        }
    } else {
        return number_format($data, 2, '.', '') . 'KB';
    }
}

function convert_data_2($total_data_gb)
{
    if ($total_data_gb < 1023) {
        return number_format($total_data_gb, 2, '.', '') . 'KB';
    } else if ($total_data_gb < 1047552) {
        return number_format(($total_data_gb / 1024), 2, '.', '') . 'MB';
    } else if ($total_data_gb < 1072693248) {
        return number_format((($total_data_gb / 1024) / 1024), 2, '.', '') . 'GB';
    } else {
        return number_format(((($total_data_gb / 1024) / 1024) / 1024), 2, '.', '') . 'TB';
    }
}

?>