<?php
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

$db1 		= new database_ORI();
$koneksi 	= $db1->connect();
$query      = "SELECT * FROM ipp_sample WHERE deleted='N' AND created_by <> 'json' ORDER BY no_ipp ASC";
$result     = $koneksi->query($query);

$ArrData    = array();
while($row  = $result->fetch_array(MYSQLI_ASSOC))
$ArrData[]  = $row;

$ArrData_2  = array();
foreach($ArrData AS $val => $valx){
    $ArrData_2[$val]['no_ipp'] = $valx['no_ipp'];
    $ArrData_2[$val]['project'] = strtoupper($valx['project']);
    $ArrData_2[$val]['nm_customer'] = strtoupper($valx['nm_customer']);
    $ArrData_2[$val]['status'] = $valx['status'];
    $ArrData_2[$val]['created_by'] = $valx['created_by'];
}

header('Content-Type: application/json'); 
print_r(json_encode($ArrData_2));

// $json = json_encode(array('data' => $ArrData_2));
// // write json to file
// if (file_put_contents("api_ipp.json", $json)){
//     echo "File JSON sukses dibuat...";
// } else {
//     echo "Oops! Terjadi error saat membuat file JSON...";
// }

