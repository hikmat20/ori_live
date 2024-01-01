<?php
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

$db1 		= new database_ORI();
$koneksi 	= $db1->connect();
$query      = "SELECT *, COUNT(`status`) AS qty FROM ipp_sample WHERE deleted='N' AND status <> 'CANCELED' AND created_by <> 'json' GROUP BY `status` ORDER BY no_ipp ASC";
$result     = $koneksi->query($query);

$ArrData    = array();
while($row  = $result->fetch_array(MYSQLI_ASSOC))
$ArrData[]  = $row;

$ArrData_2  = array();
foreach($ArrData AS $val => $valx){
    $ArrData_2[$val]['status'] = $valx['status'];
    $ArrData_2[$val]['qty'] = $valx['qty'];
}

header('Content-Type: application/json'); 


$json = json_encode(array('data' => $ArrData_2));
// write json to file
if (file_put_contents("api_status.json", $json)){
    print_r(json_encode($ArrData_2));
} else {
    echo "Oops! Terjadi error saat membuat file JSON...";
}

