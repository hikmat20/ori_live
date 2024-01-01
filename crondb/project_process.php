<?php 
  
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

//echo "<pre>";print_r(file_get_contents('function_connec.php'));exit;
//echo"masuk bro".$_SERVER['DOCUMENT_ROOT'];
$db1 				= new database_ORI();
$koneksi 			= $db1->connect();
$sqlTrunc = "TRUNCATE TABLE group_cost_project_process_fast_table";
$koneksi->query($sqlTrunc);

$sqlHeader      = "SELECT
						a.no_ipp AS no_ipp,
						a.estimasi AS estimasi,
						a.rev AS rev,
						a.order_type AS order_type,
						a.nm_customer AS nm_customer,
						a.sts_ipp AS sts_ipp,
						IF(a.est_mat IS NULL, 0, a.est_mat) AS est_mat,
						IF(a.est_mat IS NULL, 0, a.est_harga) AS est_harga,
						IF(a.est_mat IS NULL, 0, a.real_material) AS real_material,
						IF(a.est_mat IS NULL, 0, a.real_harga) AS real_harga,
						IF(a.est_mat IS NULL, 0, a.persenx) AS persenx,
						'system' AS create_by,
						'".date('Y-m-d H:i:s')."' AS create_date
					FROM
						group_cost_project_process_fast a";
$Q_Awal			= $koneksi->query($sqlHeader);
$restHeader = array();
while($row  = $Q_Awal->fetch_array(MYSQLI_ASSOC))
$restHeader[] = $row;


foreach($restHeader AS $val=>$valx){
	$sqlInsertDet = "INSERT INTO 
						group_cost_project_process_fast_table 
						( 
							no_ipp, 
							estimasi, 
							rev, 
							order_type, 
							nm_customer, 
							sts_ipp, 
							est_mat, 
							est_harga, 
							real_material, 
							real_harga, 
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
							'".$valx['est_mat']."',
							'".$valx['est_harga']."',
							'".$valx['real_material']."',
							'".$valx['real_harga']."',
							'".$valx['persenx']."',
							'".$valx['create_by']."',
							'".$valx['create_date']."'
						)
                        ";
        // echo $sqlInsertDet."<br>";
        $koneksi->query($sqlInsertDet);
}
   
// $sqlInsertHead = "
                    // INSERT INTO 
						// group_cost_project_process_fast_table 
						// ( 
							// no_ipp, 
							// estimasi, 
							// rev, 
							// order_type, 
							// nm_customer, 
							// sts_ipp, 
							// est_mat, 
							// est_harga, 
							// real_material, 
							// real_harga, 
							// persenx, 
							// create_by, 
							// create_date
						// ) 
						// SELECT
							// a.no_ipp,
							// a.estimasi,
							// a.rev,
							// a.order_type,
							// a.nm_customer,
							// a.sts_ipp,
							// IF(a.est_mat IS NULL, 0, a.est_mat) AS est_mat,
							// IF(a.est_mat IS NULL, 0, a.est_harga) AS est_harga,
							// IF(a.est_mat IS NULL, 0, a.real_material) AS real_material,
							// IF(a.est_mat IS NULL, 0, a.real_harga) AS real_harga,
							// IF(a.est_mat IS NULL, 0, a.persenx) AS persenx,
							// 'system',
							// '".date('Y-m-d H:i:s')."'
						// FROM
							// group_cost_project_process_fast a";
// $koneksi->query($sqlInsertHead);


?>