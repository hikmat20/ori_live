<?php 
  
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

$db1 				= new database_ORI();
$koneksi 			= $db1->connect();

//LIST BARU
//Componnet Header
$sqlHeader 	    = "	SELECT 
						a.cust,
						a.id_product,
						a.parent_product,
						a.stiffness,
						a.id_product,
						a.created_by,
						a.created_date,
						a.rev,
						a.series,
						a.diameter,
						a.diameter2
					FROM component_header a ORDER BY a.created_date DESC";
$Q_Awal			= $koneksi->query($sqlHeader);
$restHeader = array();
while($row  = $Q_Awal->fetch_array(MYSQLI_ASSOC))
$restHeader[] = $row;

//Table Product List
$product_list 	= "SELECT b.id_product FROM table_product_list b ";
$qPro			= $koneksi->query($product_list);
$ProductArr = array();
while($row  = $qPro->fetch_array(MYSQLI_ASSOC))
$restProductArrHeader[] = $row;

$data_product = array();        
foreach ($restProductArrHeader as $key => $value) {
	$data_product[$value['id_product']] = $value;
}

// echo "<pre>";
// print_r($restHeader);
// print_r($data_product);
// exit;
$nomor = 0;
foreach ($restHeader as $key => $valx) { $nomor++;
	if(!isset($data_product[$valx['id_product']])){
	// if (!in_array($valx['id_product'], $data_product)) {
		
		$cust 		= (!empty($valx['cust']))?$valx['cust']:'C100-1903000';

		$dim1 		= (!empty($valx['diameter']))?$valx['diameter']:0;
		$dim2 		= (!empty($valx['diameter2']))?$valx['diameter2']:0;

		$sqlInsertDet = "INSERT INTO 
						table_product_list 
						( 
							id_product, 
							id_customer, 
							product, 
							stifness,
							created_by, 
							created_date, 
							rev, 
							series, 
							diameter,
							diameter2,
							updated_by, 
							updated_date
						)
						VALUE
						(
							'".$valx['id_product']."',
							'".$cust."',
							'".$valx['parent_product']."',
							'".$valx['stiffness']."',
							'".$valx['created_by']."',
							'".$valx['created_date']."',
							'".$valx['rev']."',
							'".$valx['series']."',
							'".$dim1."',
							'".$dim2."',
							'system',
							'".date('Y-m-d H:i:s')."'
						)
						";
		$koneksi->query($sqlInsertDet);
		echo $sqlInsertDet."<br>";
	}
}


//Table Product List
$dateNow    = date('Y-m-d'); //componentnnya dipasin field nya
$dateNowMin = date('Y-m-d', strtotime('-1 days', strtotime($dateNow)));
// $dateNowMin = '2021-08-10'; //terakhir 2021-08-10 8:00
$product_list 	= "SELECT b.id_product, b.series, b.parent_product AS product, b.diameter, b.diameter2 FROM component_header b WHERE DATE(b.created_date) = '".$dateNowMin."'  ORDER BY b.created_date DESC";

//manual
// $product_list 	= "SELECT b.* FROM table_product_list b WHERE b.weight IS NULL AND DATE(b.created_date) BETWEEN '2020-06-01' AND '2020-09-30' ORDER BY b.created_date DESC";
$qPro			= $koneksi->query($product_list);
$restProductArrHeader = array();
while($row  = $qPro->fetch_array(MYSQLI_ASSOC))
$restProductArrHeader[] = $row;

// echo "<pre>";
// print_r($restProductArrHeader);
// exit;
$nomor=0;
foreach ($restProductArrHeader as $key => $valx) { $nomor++;
    $cal_price  = $db1->get_weight_comp($valx['id_product'], $valx['series'], $valx['product'], $valx['diameter'], $valx['diameter2']);
    $weight     = $cal_price['weight'];
    $price      = $cal_price['price'];
    $process    = $cal_price['process'];
    $foh        = $cal_price['foh'];
    $profit     = $cal_price['profit'];

	$sqlUpdate = "UPDATE 
						table_product_list 
						SET
                            `weight` = '".$weight."', 
                            price = '".$price."',
                            process = '".$process."',
                            foh = '".$foh."',
                            profit = '".$profit."',
							updated_by = 'system', 
							updated_date = '".date('Y-m-d H:i:s')."'
						WHERE 
                            id_product = '".$valx['id_product']."'
                        ";
       
        $koneksi->query($sqlUpdate);
        echo $sqlUpdate."<br>";
}
								
?>