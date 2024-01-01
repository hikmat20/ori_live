<?php 
// ini_set('max_input_vars', 4000 );
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

$db1 				= new database_ORI();
$koneksi 			= $db1->connect();
//COSTBOOK
$SQL_COST_BOOK = "	SELECT a.id_material, a.price_book
					FROM price_book a 
					LEFT JOIN price_book b ON (a.id_material = b.id_material AND a.id < b.id)
					WHERE b.id IS NULL";
$REST_COST_BOOK = $koneksi->query($SQL_COST_BOOK);

$CostBookArr = array();
while($row  = $REST_COST_BOOK->fetch_array(MYSQLI_ASSOC))
$CostBookArr[] = $row;

$GetCostBook = [];
foreach ($CostBookArr as $key => $value) {
	$GetCostBook[$value['id_material']] = (!empty($value['price_book']))?$value['price_book']:0;
}

//STOCK MATERIAL
$sqlHeader      = "SELECT
						a.*,
						'system' AS hist_by,
						'".date('Y-m-d H:i:s')."' AS hist_date
					FROM
						warehouse_stock a ORDER BY id ASC";
$Q_Awal			= $koneksi->query($sqlHeader);
$restHeader = array();
while($row  = $Q_Awal->fetch_array(MYSQLI_ASSOC))
$restHeader[] = $row;

// echo '<pre>';
// print_r($restHeader);
// exit;

foreach($restHeader AS $val=>$valx){
	$UPDATED_BY = (!empty($valx['update_by']))?$valx['update_by']:'empty';
	$UPDATED_DATE = (!empty($valx['update_date']))?$valx['update_date']:date('Y-m-d H:i:s');
	$COSTBOOK = (!empty($GetCostBook[$valx['id_material']]))?$GetCostBook[$valx['id_material']]:0;
	$COSTBOOK_VAL = $COSTBOOK * $valx['qty_stock'];

	$sqlInsertDet = "INSERT INTO 
						warehouse_stock_per_day 
						( 
							costbook, 
							total_value, 
							id_material, 
							idmaterial, 
							nm_material, 
							id_category, 
							nm_category, 
							id_gudang, 
							kd_gudang, 
							qty_stock, 
							qty_booking, 
							qty_rusak, 
							update_by, 
							update_date, 
							hist_by,
							hist_date
						)
						VALUE
						(
							'".$COSTBOOK."',
							'".$COSTBOOK_VAL."',
							'".$valx['id_material']."',
							'".$valx['idmaterial']."',
							'".str_replace('"',' ',$valx['nm_material'])."',
							'".$valx['id_category']."',
							'".$valx['nm_category']."',
							'".$valx['id_gudang']."',
							'".$valx['kd_gudang']."',
							'".$valx['qty_stock']."',
							'".$valx['qty_booking']."',
							'".$valx['qty_rusak']."',
							'".$UPDATED_BY."',
							'".$UPDATED_DATE."',
							'".$valx['hist_by']."',
							'".$valx['hist_date']."'
						)
                        ";
        $koneksi->query($sqlInsertDet);
		// echo $val.' - '.$valx['nm_material'].'<br>';
		// print_r($valx['id_material']);
	}
	// exit;

//STOCK RUTIN
$sqlHeader2      = "SELECT
						a.*,
						'system' AS hist_by,
						'".date('Y-m-d H:i:s')."' AS hist_date
					FROM
						warehouse_rutin_stock a";
$Q_Awal2			= $koneksi->query($sqlHeader2);
$restHeader2 = array();
while($row2  = $Q_Awal2->fetch_array(MYSQLI_ASSOC))
$restHeader2[] = $row2;


foreach($restHeader2 AS $val2=>$valx2){
	$UPDATED_BY = (!empty($valx2['update_by']))?$valx2['update_by']:'empty';
	$UPDATED_DATE = (!empty($valx2['update_date']))?$valx2['update_date']:date('Y-m-d H:i:s');

	$COSTBOOK = (!empty($GetCostBook[$valx2['code_group']]))?$GetCostBook[$valx2['code_group']]:0;
	$COSTBOOK_VAL = $COSTBOOK * $valx2['stock'];

	$sqlInsertDet2 = "INSERT INTO 
						warehouse_rutin_stock_per_day 
						( 
							costbook, 
							total_value, 
							code_group, 
							material_name, 
							gudang, 
							stock, 
							rusak, 
							update_by, 
							update_date, 
							hist_by,
							hist_date
						)
						VALUE
						(
							'".$COSTBOOK."',
							'".$COSTBOOK_VAL."',
							'".$valx2['code_group']."',
							'".$valx2['material_name']."',
							'".$valx2['gudang']."',
							'".$valx2['stock']."',
							'".$valx2['rusak']."',
							'".$UPDATED_BY."',
							'".$UPDATED_DATE."',
							'".$valx2['hist_by']."',
							'".$valx2['hist_date']."'
						)
                        ";
        $koneksi->query($sqlInsertDet2);
}
?>