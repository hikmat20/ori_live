<?php 
  
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

//echo "<pre>";print_r(file_get_contents('function_connec.php'));exit;
//echo"masuk bro".$_SERVER['DOCUMENT_ROOT'];
$db1 				= new database_ORI();
$koneksi 			= $db1->connect();
//echo $db1;exit;
$dateC = $_GET["tanggal"];
if($dateC=="") {echo "Date Error"; die();}
$date = date('Y-m-d', strtotime('-2 days', strtotime($dateC)));
// echo $date; exit;
// $date = date('2023-04-13');
// $dateIN = "('2023-06-01','2023-06-02','2023-06-03','2023-06-04','2023-06-05','2023-06-06','2023-06-07','2023-06-08','2023-06-09','2023-06-10','2023-06-11','2023-06-12','2023-06-13','2023-06-14','2023-06-15','2023-06-16','2023-06-17','2023-06-18','2023-06-19','2023-06-20','2023-06-21','2023-06-22','2023-06-23','2023-06-24','2023-06-25','2023-06-26','2023-06-27','2023-06-28','2023-06-29','2023-06-30')";
// echo"<pre>";
$sqlHeader      = "SELECT a.*, b.id_milik FROM history_pro_header_cron a LEFT JOIN production_detail b ON a.id_production_detail = b.id  WHERE DATE(a.status_date)='".$date."' ";
// $sqlHeader      = "SELECT a.*, b.id_milik FROM history_pro_header_cron a LEFT JOIN production_detail b ON a.id_production_detail = b.id  WHERE DATE(a.status_date) IN ".$dateIN."  ORDER BY a.status_date ASC ";
// echo $sqlHeader;exit;

$Q_Awal			= $koneksi->query($sqlHeader);
// $queryHeader	= $Q_Awal->fetch_array(MYSQLI_ASSOC);
// print_r($queryHeader);
$restHeader = array();
while($row  = $Q_Awal->fetch_array(MYSQLI_ASSOC))
$restHeader[] = $row;
// print_r($restHeader);
// exit;

//!!!==================================DIAKTIFKAN DITUKAR
$dateNext = $date;
// $dateNext = date('Y-m-d',strtotime($restHeader[0]['status_date']));

$kurs=1;
$sqlkurs="select * from ms_kurs where tanggal <='".$dateNext."' and mata_uang='USD' order by tanggal desc limit 1";
$dtkurs	= $koneksi->query($sqlkurs);
if(!empty($dtkurs)) {
	$getkurs	= $dtkurs->fetch_array(MYSQLI_ASSOC);
	$kurs=$getkurs['kurs'];
}

if(!empty($restHeader)){
    // echo $sqlHeader; 
	//!!!==================================DIAKTIFKAN
    $sqlDel1 = "DELETE FROM laporan_per_bulan WHERE `date`='".$date."' ";
    $sqlDel2 = "DELETE FROM laporan_per_hari WHERE `date`='".$date."' ";
    $koneksi->query($sqlDel1);
    $koneksi->query($sqlDel2);


    $ArrDay = array();
    $Sum_est_mat        = 0;
    $Sum_est_harga      = 0;
    $Sum_real_mat       = 0;
    $Sum_real_harga     = 0;
    $Sum_real_harga_rp  = 0;
    $Sum_direct_labour  = 0;
    $Sum_in_labour      = 0;
    $Sum_machine        = 0;
    $Sum_mould_mandrill = 0;
    $Sum_consumable     = 0;
    $Sum_foh_consumable = 0;
    $Sum_foh_depresiasi = 0;
    $Sum_by_gaji        = 0;
    $Sum_by_non_pro     = 0;
    $Sum_by_rutin       = 0;
	$Sum_man_hours      = 0;
    foreach($restHeader AS $val=>$valx){
        $sqlCh      = "SELECT jalur FROM production_header WHERE id_produksi='".$valx['id_produksi']."' ";
        $Q_Che		= $koneksi->query($sqlCh);
        $restCh		= $Q_Che->fetch_array(MYSQLI_ASSOC);
        $HelpDet 	= "estimasi_cost_and_mat";
        $HelpDet2 	= "banding_mat_pro";
        $HelpDet3 	= "bq_product";
        if($restCh['jalur'] == 'FD'){
            $HelpDet = "so_estimasi_cost_and_mat";
            $HelpDet2 	= "banding_so_mat_pro";
            $HelpDet3 	= "bq_product_fd";
        }

        $sqlBy 		= " SELECT
                            b.diameter AS diameter,
                            b.diameter2 AS diameter2,
                            b.pressure AS pressure,
                            b.liner AS liner,
							b.man_hours AS man_hours,
                            a.direct_labour AS direct_labour,
                            a.indirect_labour AS indirect_labour,
                            a.machine AS machine,
                            a.mould_mandrill AS mould_mandrill,
                            a.consumable AS consumable,
                            (
                                ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                            ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '1' ) / 100 ) AS foh_consumable,
                            (
                                ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                            ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '2' ) / 100 ) AS foh_depresiasi,
                            (
                                ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                            ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '3' ) / 100 ) AS biaya_gaji_non_produksi,
                            (
                                ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                            ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '4' ) / 100 ) AS biaya_non_produksi,
                            (
                                ((a.direct_labour)+(a.indirect_labour)+(a.machine)+(a.mould_mandrill)+(a.consumable))+ a.est_harga 
                            ) * ( ( SELECT cost_foh.std_rate FROM cost_foh WHERE cost_foh.id = '5' ) / 100 ) AS biaya_rutin_bulanan 
                        FROM
                            ".$HelpDet." a
                            INNER JOIN ". $HelpDet3." b ON a.id_milik = b.id
                        WHERE id_milik='".$valx['id_milik']."' LIMIT 1";
        
        $Q_By		= $koneksi->query($sqlBy);
        $restBy		= $Q_By->fetch_array(MYSQLI_ASSOC);
        
        $sqlBan     = "SELECT * FROM ".$HelpDet2." WHERE id_detail='".$valx['id_production_detail']."' LIMIT 1";
        $Q_ByBan	= $koneksi->query($sqlBan);
        $restBan	= $Q_ByBan->fetch_array(MYSQLI_ASSOC);
        // echo $sqlEst."<br>";
        $jumTot     = ($valx['qty_akhir'] - $valx['product_ke']) + 1;
        
        $Sum_est_mat        += $restBan['est_material'] * $jumTot;
        $Sum_est_harga      += $restBan['est_harga'] * $jumTot;
        $Sum_real_mat       += $restBan['real_material'];
        $Sum_real_harga     += $restBan['real_harga'];
        $Sum_real_harga_rp  += $restBan['real_harga_rp'];
        $Sum_direct_labour  += $restBy['direct_labour'] * $jumTot;
        $Sum_in_labour      += $restBy['indirect_labour'] * $jumTot;
        $Sum_machine        += $restBy['machine'] * $jumTot;
        $Sum_mould_mandrill += $restBy['mould_mandrill'] * $jumTot;
        $Sum_consumable     += $restBy['consumable'] * $jumTot;
        $Sum_foh_consumable += $restBy['foh_consumable'] * $jumTot;
        $Sum_foh_depresiasi += $restBy['foh_depresiasi'] * $jumTot;
        $Sum_by_gaji        += $restBy['biaya_gaji_non_produksi'] * $jumTot;
        $Sum_by_non_pro     += $restBy['biaya_non_produksi'] * $jumTot;
        $Sum_by_rutin       += $restBy['biaya_rutin_bulanan'] * $jumTot;
		$Sum_man_hours       += $restBy['man_hours'] * $jumTot;

        $ArrDay[$val]['id_produksi']            = $valx['id_produksi'];
        $ArrDay[$val]['id_category']            = $valx['id_category'];
        $ArrDay[$val]['id_product']             = $valx['id_product'];
        $ArrDay[$val]['diameter']               = $restBy['diameter'];
        $ArrDay[$val]['diameter2']              = $restBy['diameter2'];
        $ArrDay[$val]['pressure']               = $restBy['pressure'];
        $ArrDay[$val]['liner']                  = $restBy['liner'];
        $ArrDay[$val]['status_date']            = $valx['status_date'];
        $ArrDay[$val]['qty_awal']               = $valx['product_ke'];
        $ArrDay[$val]['qty_akhir']              = $valx['qty_akhir'];
        $ArrDay[$val]['qty']                    = $valx['qty'];
        $ArrDay[$val]['date']                   = date('Y-m-d',strtotime($valx['status_date']));
        $ArrDay[$val]['id_production_detail']   = $valx['id_production_detail'];
        $ArrDay[$val]['id_milik']               = $valx['id_milik'];
        $ArrDay[$val]['est_material']           = $restBan['est_material'] * $jumTot;
        $ArrDay[$val]['est_harga']              = $restBan['est_harga'] * $jumTot;
        $ArrDay[$val]['real_material']          = $restBan['real_material'];
        $ArrDay[$val]['real_harga']             = $restBan['real_harga'];
        $ArrDay[$val]['real_harga_rp']          = $restBan['real_harga_rp'];
        $ArrDay[$val]['kurs']                   = $kurs;

        $ArrDay[$val]['direct_labour']          = $restBy['direct_labour'] * $jumTot;
        $ArrDay[$val]['indirect_labour']        = $restBy['indirect_labour'] * $jumTot;
        $ArrDay[$val]['machine']                = $restBy['machine'] * $jumTot;
        $ArrDay[$val]['mould_mandrill']         = $restBy['mould_mandrill'] * $jumTot;
        $ArrDay[$val]['consumable']             = $restBy['consumable'] * $jumTot;
        $ArrDay[$val]['foh_consumable']         = $restBy['foh_consumable'] * $jumTot;
        $ArrDay[$val]['foh_depresiasi']         = $restBy['foh_depresiasi'] * $jumTot;
        $ArrDay[$val]['biaya_gaji_non_produksi']= $restBy['biaya_gaji_non_produksi'] * $jumTot;
        $ArrDay[$val]['biaya_non_produksi']     = $restBy['biaya_non_produksi'] * $jumTot;
        $ArrDay[$val]['biaya_rutin_bulanan']    = $restBy['biaya_rutin_bulanan'] * $jumTot;

        $ArrDay[$val]['insert_by']              = 'system';
        $ArrDay[$val]['insert_date']            = date('Y-m-d H:i:s');
        
        $sqlInsertDet = "INSERT INTO laporan_per_hari
                            (id_produksi,id_category,id_product,diameter,diameter2,pressure,liner,status_date,
                            qty_awal,qty_akhir,qty,`date`,id_production_detail,id_milik,est_material,est_harga,
                            real_material,real_harga,direct_labour,indirect_labour,machine,mould_mandrill,
                            consumable,foh_consumable,foh_depresiasi,biaya_gaji_non_produksi,biaya_non_produksi,
                            biaya_rutin_bulanan,insert_by,insert_date,man_hours,real_harga_rp,kurs)
                            VALUE
                            ('".$valx['id_produksi']."','".$valx['id_category']."','".$valx['id_product']."',
                            '".$restBy['diameter']."','".$restBy['diameter2']."','".$restBy['pressure']."',
                            '".$restBy['liner']."','".$valx['status_date']."','".$valx['product_ke']."',
                            '".$valx['qty_akhir']."','".$valx['qty']."','".date('Y-m-d',strtotime($valx['status_date']))."','".$valx['id_production_detail']."',
                            '".$valx['id_milik']."','".$restBan['est_material'] * $jumTot."','".$restBan['est_harga'] * $jumTot."',
                            '".$restBan['real_material']."','".$restBan['real_harga']."','".$restBy['direct_labour'] * $jumTot."',
                            '".$restBy['indirect_labour'] * $jumTot."','".$restBy['machine'] * $jumTot."',
                            '".$restBy['mould_mandrill'] * $jumTot."','".$restBy['consumable'] * $jumTot."',
                            '".$restBy['foh_consumable'] * $jumTot."','".$restBy['foh_depresiasi'] * $jumTot."',
                            '".$restBy['biaya_gaji_non_produksi'] * $jumTot."','".$restBy['biaya_non_produksi'] * $jumTot."',
                            '".$restBy['biaya_rutin_bulanan'] * $jumTot."','system','".date('Y-m-d H:i:s')."','".$restBy['man_hours'] * $jumTot."','".$restBan['real_harga_rp']."','".$kurs."')
                        ";
        // echo $sqlInsertDet."<br>";
        $koneksi->query($sqlInsertDet);
    }
    // exit;
    $ArrDayMonth = array(
        'date' => $date,
        'est_material' => $Sum_est_mat,
        'est_harga' => $Sum_est_harga,
        'real_material' => $Sum_real_mat,
        'real_harga' => $Sum_real_harga,
        'real_harga_rp' => $Sum_real_harga_rp,
        'kurs' => $kurs,
        'direct_labour' => $Sum_direct_labour,
        'indirect_labour' => $Sum_in_labour,
        'machine' => $Sum_machine,
        'mould_mandrill' => $Sum_mould_mandrill,
        'consumable' => $Sum_consumable,
        'foh_consumable' => $Sum_foh_consumable,
        'foh_depresiasi' => $Sum_foh_depresiasi,
        'biaya_gaji_non_produksi' => $Sum_by_gaji,
        'biaya_non_produksi' => $Sum_by_non_pro,
        'biaya_rutin_bulanan' => $Sum_by_rutin,
        'insert_by' => 'system',
        'insert_date' => date('Y-m-d H:i:s')
    );
    $sqlInsertHead = "INSERT INTO laporan_per_bulan
                            (`date`,est_material,est_harga,real_material,real_harga,
                            direct_labour,indirect_labour,machine,mould_mandrill,consumable,
                            foh_consumable,foh_depresiasi,biaya_gaji_non_produksi,biaya_non_produksi,
                            biaya_rutin_bulanan,insert_by,insert_date,man_hours,real_harga_rp,kurs)
                            VALUE
                            ('". $date."','".$Sum_est_mat."','".$Sum_est_harga."','".$Sum_real_mat."',
                            '".$Sum_real_harga."','".$Sum_direct_labour."','".$Sum_in_labour."',
                            '".$Sum_machine."','".$Sum_mould_mandrill."','".$Sum_consumable."',
                            '".$Sum_foh_consumable."','".$Sum_foh_depresiasi."','".$Sum_by_gaji."',
                            '".$Sum_by_non_pro."','".$Sum_by_rutin."','system','".date('Y-m-d H:i:s')."','".$Sum_man_hours."','".$Sum_real_harga_rp."','".$kurs."')
                        ";
        // echo $sqlInsertHead."<br>";
    $koneksi->query($sqlInsertHead);

    // echo "<pre>";
    // print_r($ArrDay);
    // print_r($ArrDayMonth);
    // exit;
    // $this->db->trans_start();
    //     $this->db->delete('laporan_per_bulan', array('date' => $date));
    //     $this->db->delete('laporan_per_hari', array('date' => $date));

    //     $this->db->insert('laporan_per_bulan', $ArrDayMonth);
    //     $this->db->insert_batch('laporan_per_hari', $ArrDay);
    // $this->db->trans_complete();

    // if($this->db->trans_status() === FALSE){
    //     $this->db->trans_rollback();
        // $ArrHistF	= array(
        //     'date'			=> $date,
        //     'status'		=> 'FAILED',
        //     'insert_by'		=> 'system',
        //     'insert_date'	=> date('Y-m-d H:i:s')
        // );
        // $this->db->insert('laporan_status', $ArrHistF);
        // echo "Failed Insert Data";
    // }
    // else{
    //     $this->db->trans_commit();
        // $ArrHistS	= array(
        //     'date'			=> $date,
        //     'status'		=> 'SUCCESS',
        //     'insert_by'		=> 'system',
        //     'insert_date'	=> date('Y-m-d H:i:s') 
        // );
        // $this->db->insert('laporan_status', $ArrHistS);

        $sqlInsertStatus = "INSERT INTO laporan_status 
                                (`date`,`status`,insert_by, insert_date)
                                VALUE
                                ('".$date."','SUCCESS','system','".date('Y-m-d H:i:s')."')   
                            ";
        $koneksi->query($sqlInsertStatus);
        echo "Success Insert Data ".$dateC;
    // }
}
else{
    // $ArrHistE	= array(
    //     'date'			=> $date,
    //     'status'		=> 'EMPTY',
    //     'insert_by'		=> 'system',
    //     'insert_date'	=> date('Y-m-d H:i:s') 
    // );
    // $this->db->insert('laporan_status', $ArrHistE);
    $sqlInsertStatus = "INSERT INTO laporan_status 
                                (`date`,`status`,insert_by, insert_date)
                                VALUE
                                ('".$date."','EMPTY','system','".date('Y-m-d H:i:s')."')   
                            ";
    $koneksi->query($sqlInsertStatus);
    echo "No Data Insert Data ".$dateC;
}

?>