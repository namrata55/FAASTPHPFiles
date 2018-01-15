<?php

require_once('connection.php');

if (!$conn) {
    die('Could not connect: ' . mysql_error());
} else {

  $username = $_POST['username'];
// $username = '8549826883';

    $sql = "SELECT acctstarttime FROM radacct where username = '".$username."' ORDER BY acctstarttime ASC ";

    $res = $conn->query($sql);

    if ($res->num_rows > 0) {

        $result = array();
        while ($row = $res->fetch_assoc()) {

            $date = explode(' ',$row['acctstarttime']);
            $date_1 = explode('-',$date[0]);
            $result[] = $date_1[0].'-'.$date_1[1];
        }

        $result = array_unique($result);
        $j = 0;
        foreach($result as $row_1){

            $start_date = $row_1.'-01'.' 00:00:00';
            $end_date = $row_1.'-31'.' 23:59:59';

            $sql_1 = "SELECT acctinputoctets, acctoutputoctets FROM radacct where username = '".$username."' AND acctstarttime > '".$start_date."'  AND acctstarttime < '".$end_date."'  ";

            $res_1 = $conn->query($sql_1);

            $used_data = 0;
            while ($row_2 = $res_1->fetch_assoc()) {

                $upload_data = ($row_2['acctinputoctets']/1024/1024/1024);
                $download_data = ($row_2['acctoutputoctets']/1024/1024/1024);
                $used_data += $upload_data + $download_data;
            }

            $all_result[$j]['month'] = $row_1;
            $all_result[$j]['data'] = number_format($used_data, 2, '.', '');

            $j++;
        }
            $size = count($all_result);
            if($size > 4){
                $all_result = array_slice($all_result, $size-4, $size);
            }
            else{
                $all_result = array_slice($all_result, 0, $size);
            }
            echo json_encode($all_result);
//            echo '<pre>'; print_r($all_result);

    } else {
        echo "0 results";
    }
}
mysqli_close($conn);

?>