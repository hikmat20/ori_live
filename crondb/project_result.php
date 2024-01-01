<?php 
  
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

// echo "<pre>";print_r(file_get_contents('function_connec.php'));exit;
//echo"masuk bro".$_SERVER['DOCUMENT_ROOT'];
$db1 				= new database_ORI();
$koneksi 			= $db1->connect();
$sqlTrunc = "TRUNCATE TABLE group_project";
$koneksi->query($sqlTrunc);
   
$sqlInsertHead = "
                    SELECT
						a.no_ipp AS no_ipp,
						a.estimasi AS estimasi,
						b.ref_quo AS rev,
						a.order_type AS order_type,
						b.nm_customer AS nm_customer,
						b.status AS sts_ipp
					FROM
						 bq_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp
					WHERE
						a.estimasi = 'Y' 
						AND ( b.status = 'PROCESS PRODUCTION' OR b.status = 'PARTIAL PROCESS')";
$Q_Awal			= $koneksi->query($sqlInsertHead);

$restHeader = array();
while($row  = $Q_Awal->fetch_array(MYSQLI_ASSOC))
$restHeader[] = $row;

// print_r($restHeader); exit;
foreach($restHeader AS $val=>$valx){
	$sqlCh      = "SELECT jalur FROM production_header WHERE id_produksi='PRO-".$valx['no_ipp']."' ";
	$Q_Che		= $koneksi->query($sqlCh);
	$restCh		= $Q_Che->fetch_array(MYSQLI_ASSOC);
	$HelpDet 	= "group_cost_project_process_fast_table";
	if($restCh['jalur'] == 'FD'){
		$HelpDet = "group_so_cost_project_process_fast_table";
	}

	$sqlBy 		= " SELECT
						*
					FROM
						".$HelpDet."
					WHERE no_ipp='".$valx['no_ipp']."' LIMIT 1";
	
	$Q_By		= $koneksi->query($sqlBy);
	$restBy		= $Q_By->fetch_array(MYSQLI_ASSOC);
	
	$sqlInsertDet = "INSERT INTO group_project
						(
							no_ipp,
							estimasi,
							rev,
							order_type,
							nm_customer,
							sts_ipp,
							real_material,
							real_harga,
							est_mat,
							est_harga,
							persenx,
							create_by,
							create_date
						)
						VALUE
						(
							'".$valx['no_ipp']."',
							'".$valx['estimasi']."',
							'".$valx['rev']."',
							'".$valx['order_type']."',
							'".$valx['nm_customer']."',
							'".$valx['sts_ipp']."',
							'".$restBy['real_material']."',
							'".$restBy['real_harga']."',
							'".$restBy['est_mat']."',
							'".$restBy['est_harga']."',
							'".$restBy['persenx']."',
							'system',
							'".date('Y-m-d H:i:s')."')
					";
	echo $sqlInsertDet."<br>";
	$koneksi->query($sqlInsertDet);
}

//FINISH
$sqlInsertHead2 = "
                    SELECT
						a.no_ipp AS no_ipp,
						a.estimasi AS estimasi,
						b.ref_quo AS rev,
						a.order_type AS order_type,
						b.nm_customer AS nm_customer,
						b.status AS sts_ipp
					FROM
						 bq_header a LEFT JOIN production b ON a.no_ipp = b.no_ipp
					WHERE
						a.estimasi = 'Y' 
						AND ( b.status = 'FINISH')";
$Q_Awal2			= $koneksi->query($sqlInsertHead2);

$restHeader2 = array();
while($row2  = $Q_Awal2->fetch_array(MYSQLI_ASSOC))
$restHeader2[] = $row2;

// print_r($restHeader); exit;
foreach($restHeader2 AS $val=>$valx){
	$sqlCh      = "SELECT jalur FROM production_header WHERE id_produksi='PRO-".$valx['no_ipp']."' ";
	$Q_Che		= $koneksi->query($sqlCh);
	$restCh		= $Q_Che->fetch_array(MYSQLI_ASSOC);
	$HelpDet 	= "group_cost_project_finish_fast_table";
	if($restCh['jalur'] == 'FD'){
		$HelpDet = "group_so_cost_project_finish_fast_table";
	}

	$sqlBy 		= " SELECT
						*
					FROM
						".$HelpDet."
					WHERE no_ipp='".$valx['no_ipp']."' LIMIT 1";
	echo $sqlBy."<br>";
	$Q_By		= $koneksi->query($sqlBy);
	$restBy		= $Q_By->fetch_array(MYSQLI_ASSOC);
	$persenx = (!empty($restBy['persenx']))?$restBy['persenx']:0;
	
	$sqlInsertDet = "INSERT INTO group_project
						(
							no_ipp,
							estimasi,
							rev,
							order_type,
							nm_customer,
							sts_ipp,
							real_material,
							real_harga,
							est_mat,
							est_harga,
							persenx,
							create_by,
							create_date
						)
						VALUE
						(
							'".$valx['no_ipp']."',
							'".$valx['estimasi']."',
							'".$valx['rev']."',
							'".$valx['order_type']."',
							'".$valx['nm_customer']."',
							'".$valx['sts_ipp']."',
							'".$restBy['real_material']."',
							'".$restBy['real_harga']."',
							'".$restBy['est_mat']."',
							'".$restBy['est_harga']."',
							'".$persenx."',
							'system',
							'".date('Y-m-d H:i:s')."')
					";
	echo $sqlInsertDet."<br>";
	$koneksi->query($sqlInsertDet);
}

?>