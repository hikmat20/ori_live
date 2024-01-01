<?php 
// ini_set('max_input_vars', 4000 );
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

$db1 				= new database_ORI();
$koneksi 			= $db1->connect();

$ArrCategory = ['pipe','cutting','fitting'];
foreach ($ArrCategory as $value) {
	$category = $value;
	echo $category.'<br>';
}
?>