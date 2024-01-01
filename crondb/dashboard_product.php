<?php 
  
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

$db1 				= new database_ORI();
$koneksi 			= $db1->connect();

$dateC = date('Y-m-d');
$date = date('Y-m-d', strtotime('-1 days', strtotime($dateC)));

$sqlHeader      = "SELECT a.* FROM laporan_per_hari a";
$Q_Awal			= $koneksi->query($sqlHeader);

$restHeader = array();
while($row  = $Q_Awal->fetch_array(MYSQLI_ASSOC))
$restHeader[] = $row;


if(!empty($restHeader)){
 
    foreach($restHeader AS $val=>$valx){
        $sqlCh      = "SELECT jalur FROM production_header WHERE id_produksi='".$valx['id_produksi']."' ";
        $Q_Che		= $koneksi->query($sqlCh);
        $restCh		= $Q_Che->fetch_array(MYSQLI_ASSOC);
        $HelpDet3 	= "bq_product";
        if($restCh['jalur'] == 'FD'){
            $HelpDet3 	= "bq_product_fd";
        }

        $sqlBy 		= " SELECT
							b.man_hours AS man_hours
                        FROM
                            ". $HelpDet3." b
                        WHERE b.id ='".$valx['id_milik']."' LIMIT 1";
        
        $Q_By		= $koneksi->query($sqlBy);
        $restBy		= $Q_By->fetch_array(MYSQLI_ASSOC);
     
		$jumTot     = ($valx['qty_akhir'] - $valx['qty_awal']) + 1;
 
        $sqlInsertDet = "UPDATE laporan_per_hari SET man_hours = '".$restBy['man_hours'] * $jumTot."' WHERE id = '".$valx['id']."'";
        echo $sqlInsertDet."<br>";
        $koneksi->query($sqlInsertDet);
    }
    

}

?>