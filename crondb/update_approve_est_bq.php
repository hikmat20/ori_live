<?php 
	
$awal = microtime(true);

date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

$db1 			= new database_ORI();
$koneksi 		= $db1->connect();
//b.`status` = 'WAITING APPROVE EST PROJECT'
$sqlHeader      = "	SELECT
						a.id_bq,
						a.id,
						a.id_category,
						a.no_komponen,
						a.diameter_1,
						a.diameter_2,
						a.length,
						a.thickness,
						a.type,
						a.sudut,
						a.qty,
						a.id_product
					FROM
						bq_detail_header a
						LEFT JOIN production b ON REPLACE ( a.id_bq, 'BQ-', '' ) = b.no_ipp 
					WHERE b.`status` = 'WAITING APPROVE EST PROJECT'
						";

$Q_Awal			= $koneksi->query($sqlHeader);
$restHeader = array();
while($row  = $Q_Awal->fetch_array(MYSQLI_ASSOC))
$restHeader[] = $row;


$sqlTrunc = "TRUNCATE TABLE laporan_excel_est_bq";
$koneksi->query($sqlTrunc);

// echo "<pre>";
// print_r($restHeader); exit;
if(!empty($restHeader)){
	foreach($restHeader AS $val => $valx){
		//LINER
		$liner_resin = 0;
		if($valx['id_category'] <> 'field joint' OR $valx['id_category'] <> 'shop joint' OR $valx['id_category'] <> 'branch joint'){
			$sql_liner_resin    = "SELECT MAX(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='LINER THIKNESS / CB' AND id_category='TYP-0001' AND id_milik='".$valx['id']."'";
			$q_liner_resin		= $koneksi->query($sql_liner_resin);
			$rest_liner_resin	= $q_liner_resin->fetch_array(MYSQLI_ASSOC);
			$liner_resin		= (!empty($rest_liner_resin['last_cost']))?$rest_liner_resin['last_cost']:0;
		}
		
		$liner_veil = 0;
		if($valx['id_category'] <> 'field joint' OR $valx['id_category'] <> 'shop joint' OR $valx['id_category'] <> 'branch joint'){
			$sql_liner_veil    	= "SELECT SUM(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='LINER THIKNESS / CB' AND id_category='TYP-0003' AND id_milik='".$valx['id']."'";
			$q_liner_veil		= $koneksi->query($sql_liner_veil);
			$rest_liner_veil	= $q_liner_veil->fetch_array(MYSQLI_ASSOC);
			$liner_veil			= (!empty($rest_liner_veil['last_cost']))?$rest_liner_veil['last_cost']:0;
		}
		
		
		$liner_csm = 0;
		if($valx['id_category'] <> 'field joint' OR $valx['id_category'] <> 'shop joint' OR $valx['id_category'] <> 'branch joint'){
			$sql_liner_csm    	= "SELECT SUM(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='LINER THIKNESS / CB' AND id_category='TYP-0004' AND id_milik='".$valx['id']."'";
			$q_liner_csm		= $koneksi->query($sql_liner_csm);
			$rest_liner_csm		= $q_liner_csm->fetch_array(MYSQLI_ASSOC);
			$liner_csm			= (!empty($rest_liner_csm['last_cost']))?$rest_liner_csm['last_cost']:0;
		}
		
		
		//STRUCTURE
		$structure_resin = 0;
		if($valx['id_category'] <> 'field joint' OR $valx['id_category'] <> 'shop joint' OR $valx['id_category'] <> 'branch joint'){
			$sql_structure_resin    = "SELECT MAX(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='STRUKTUR THICKNESS' AND id_category='TYP-0001' AND id_milik='".$valx['id']."'";
			$q_structure_resin		= $koneksi->query($sql_structure_resin);
			$rest_structure_resin	= $q_structure_resin->fetch_array(MYSQLI_ASSOC);
			$structure_resin		= (!empty($rest_structure_resin['last_cost']))?$rest_structure_resin['last_cost']:0;
		}
		
		
		$structure_csm = 0;
		if($valx['id_category'] <> 'field joint' OR $valx['id_category'] <> 'shop joint' OR $valx['id_category'] <> 'branch joint'){
			$sql_structure_csm    	= "SELECT SUM(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='STRUKTUR THICKNESS' AND id_category='TYP-0004' AND id_milik='".$valx['id']."'";
			$q_structure_csm		= $koneksi->query($sql_structure_csm);
			$rest_structure_csm		= $q_structure_csm->fetch_array(MYSQLI_ASSOC);
			$structure_csm			= (!empty($rest_structure_csm['last_cost']))?$rest_structure_csm['last_cost']:0;
		}
		
		
		$structure_wr = 0;
		if($valx['id_category'] <> 'field joint' OR $valx['id_category'] <> 'shop joint' OR $valx['id_category'] <> 'branch joint'){
			$sql_structure_wr   = "SELECT SUM(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='STRUKTUR THICKNESS' AND id_category='TYP-0006' AND id_milik='".$valx['id']."'";
			$q_structure_wr		= $koneksi->query($sql_structure_wr);
			$rest_structure_wr	= $q_structure_wr->fetch_array(MYSQLI_ASSOC);
			$structure_wr			= (!empty($rest_structure_wr['last_cost']))?$rest_structure_wr['last_cost']:0;
		}
		
		
		$structure_rooving = 0;
		if($valx['id_category'] <> 'field joint' OR $valx['id_category'] <> 'shop joint' OR $valx['id_category'] <> 'branch joint'){
			$sql_structure_rooving  = "SELECT SUM(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='STRUKTUR THICKNESS' AND id_category='TYP-0005' AND id_milik='".$valx['id']."'";
			$q_structure_rooving	= $koneksi->query($sql_structure_rooving);
			$rest_structure_rooving	= $q_structure_rooving->fetch_array(MYSQLI_ASSOC);
			$structure_rooving			= (!empty($rest_structure_rooving['last_cost']))?$rest_structure_rooving['last_cost']:0;
		}
		
		
		//STRUCTURE NECK 1
		$structure_resinn1 = 0;
		if($valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'colar' OR $valx['id_category'] == 'colar slongsong'){
			$sql_structure_resinn1  = "SELECT MAX(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='STRUKTUR NECK 1' AND id_category='TYP-0001' AND id_milik='".$valx['id']."'";
			$q_structure_resinn1	= $koneksi->query($sql_structure_resinn1);
			$rest_structure_resinn1	= $q_structure_resinn1->fetch_array(MYSQLI_ASSOC);
			$structure_resinn1			= (!empty($rest_structure_resinn1['last_cost']))?$rest_structure_resinn1['last_cost']:0;
		}
		
		
		$structure_csmn1 = 0;
		if($valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'colar' OR $valx['id_category'] == 'colar slongsong'){
			$sql_structure_csmn1    = "SELECT SUM(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='STRUKTUR NECK 1' AND id_category='TYP-0004' AND id_milik='".$valx['id']."'";
			$q_structure_csmn1		= $koneksi->query($sql_structure_csmn1);
			$rest_structure_csmn1	= $q_structure_csmn1->fetch_array(MYSQLI_ASSOC);
			$structure_csmn1			= (!empty($rest_structure_csmn1['last_cost']))?$rest_structure_csmn1['last_cost']:0;
		}
		
		
		$structure_wrn1 = 0;
		if($valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'colar' OR $valx['id_category'] == 'colar slongsong'){
			$sql_structure_wrn1   	= "SELECT SUM(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='STRUKTUR NECK 1' AND id_category='TYP-0006' AND id_milik='".$valx['id']."'";
			$q_structure_wrn1		= $koneksi->query($sql_structure_wrn1);
			$rest_structure_wrn1	= $q_structure_wrn1->fetch_array(MYSQLI_ASSOC);
			$structure_wrn1				= (!empty($rest_structure_wrn1['last_cost']))?$rest_structure_wrn1['last_cost']:0;
		}
		
		
		$structure_roovingn1 = 0;
		if($valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'colar' OR $valx['id_category'] == 'colar slongsong'){
			$sql_structure_roovingn1  	= "SELECT SUM(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='STRUKTUR NECK 1' AND id_category='TYP-0005' AND id_milik='".$valx['id']."'";
			$q_structure_roovingn1		= $koneksi->query($sql_structure_roovingn1);
			$rest_structure_roovingn1	= $q_structure_roovingn1->fetch_array(MYSQLI_ASSOC);
			$structure_roovingn1			= (!empty($rest_structure_roovingn1['last_cost']))?$rest_structure_roovingn1['last_cost']:0;
		}
		
		
		//STRUCTURE NECK 2
		$structure_resinn2 = 0;
		if($valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'colar' OR $valx['id_category'] == 'colar slongsong'){
			$sql_structure_resinn2  = "SELECT MAX(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='STRUKTUR NECK 2' AND id_category='TYP-0001' AND id_milik='".$valx['id']."'";
			$q_structure_resinn2	= $koneksi->query($sql_structure_resinn2);
			$rest_structure_resinn2	= $q_structure_resinn2->fetch_array(MYSQLI_ASSOC);
			$structure_resinn2			= (!empty($rest_structure_resinn2['last_cost']))?$rest_structure_resinn2['last_cost']:0;
		}
		
		
		$structure_csmn2 = 0;
		if($valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'colar' OR $valx['id_category'] == 'colar slongsong'){
			$sql_structure_csmn2    = "SELECT SUM(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='STRUKTUR NECK 2' AND id_category='TYP-0004' AND id_milik='".$valx['id']."'";
			$q_structure_csmn2		= $koneksi->query($sql_structure_csmn2);
			$rest_structure_csmn2	= $q_structure_csmn2->fetch_array(MYSQLI_ASSOC);
			$structure_csmn2			= (!empty($rest_structure_csmn2['last_cost']))?$rest_structure_csmn2['last_cost']:0;
		}
		
		
		$structure_wrn2 = 0;
		if($valx['id_category'] == 'flange mould' OR $valx['id_category'] == 'flange slongsong' OR $valx['id_category'] == 'colar' OR $valx['id_category'] == 'colar slongsong'){
			$sql_structure_wrn2   	= "SELECT SUM(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='STRUKTUR NECK 2' AND id_category='TYP-0006' AND id_milik='".$valx['id']."'";
			$q_structure_wrn2		= $koneksi->query($sql_structure_wrn2);
			$rest_structure_wrn2	= $q_structure_wrn2->fetch_array(MYSQLI_ASSOC);
			$structure_wrn2				= (!empty($rest_structure_wrn2['last_cost']))?$rest_structure_wrn2['last_cost']:0;
		}
		
		
		//EXTERNAL
		$external_resin = 0;
		if($valx['id_category'] <> 'field joint' OR $valx['id_category'] <> 'shop joint' OR $valx['id_category'] <> 'branch joint'){
			$sql_external_resin    = "SELECT MAX(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='EXTERNAL LAYER THICKNESS' AND id_category='TYP-0001' AND id_milik='".$valx['id']."'";
			$q_external_resin		= $koneksi->query($sql_external_resin);
			$rest_external_resin	= $q_external_resin->fetch_array(MYSQLI_ASSOC);
			$external_resin		= (!empty($rest_external_resin['last_cost']))?$rest_external_resin['last_cost']:0;
		}
		
		
		$external_veil = 0;
		if($valx['id_category'] <> 'field joint' OR $valx['id_category'] <> 'shop joint' OR $valx['id_category'] <> 'branch joint'){
			$sql_external_veil    	= "SELECT SUM(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='EXTERNAL LAYER THICKNESS' AND id_category='TYP-0003' AND id_milik='".$valx['id']."'";
			$q_external_veil		= $koneksi->query($sql_external_veil);
			$rest_external_veil	= $q_external_veil->fetch_array(MYSQLI_ASSOC);
			$external_veil			= (!empty($rest_external_veil['last_cost']))?$rest_external_veil['last_cost']:0;
		}
		
		
		$external_csm = 0;
		if($valx['id_category'] <> 'field joint' OR $valx['id_category'] <> 'shop joint' OR $valx['id_category'] <> 'branch joint'){
			$sql_external_csm    	= "SELECT SUM(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='EXTERNAL LAYER THICKNESS' AND id_category='TYP-0004' AND id_milik='".$valx['id']."'";
			$q_external_csm		= $koneksi->query($sql_external_csm);
			$rest_external_csm		= $q_external_csm->fetch_array(MYSQLI_ASSOC);
			$external_csm			= (!empty($rest_external_csm['last_cost']))?$rest_external_csm['last_cost']:0;
		}
		
		
		//TOPCOAT
		$topcoat_resin = 0;
		if($valx['id_category'] <> 'field joint' OR $valx['id_category'] <> 'shop joint' OR $valx['id_category'] <> 'branch joint'){
			$sql_topcoat_resin  = "SELECT MAX(last_cost) AS last_cost FROM bq_component_detail_plus WHERE detail_name='TOPCOAT' AND id_category='TYP-0001' AND id_milik='".$valx['id']."'";
			$q_topcoat_resin	= $koneksi->query($sql_topcoat_resin);
			$rest_topcoat_resin	= $q_topcoat_resin->fetch_array(MYSQLI_ASSOC);
			$topcoat_resin			= (!empty($rest_topcoat_resin['last_cost']))?$rest_topcoat_resin['last_cost']:0;
		}
		
		
		//JOINT
		$joint_veil = 0;
		if($valx['id_category'] == 'field joint' OR $valx['id_category'] == 'shop joint' OR $valx['id_category'] == 'branch joint'){
			$sql_joint_veil    	= "SELECT MAX(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='GLASS' AND id_category='TYP-0003' AND id_milik='".$valx['id']."'";
			$q_joint_veil		= $koneksi->query($sql_joint_veil);
			$rest_joint_veil	= $q_joint_veil->fetch_array(MYSQLI_ASSOC);
			$joint_veil				= (!empty($rest_joint_veil['last_cost']))?$rest_joint_veil['last_cost']:0;
		}
		
		
		$joint_wr = 0;
		if($valx['id_category'] == 'field joint' OR $valx['id_category'] == 'shop joint' OR $valx['id_category'] == 'branch joint'){
			$sql_joint_wr   = "SELECT MAX(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='GLASS' AND id_category='TYP-0006' AND id_milik='".$valx['id']."'";
			$q_joint_wr		= $koneksi->query($sql_joint_wr);
			$rest_joint_wr	= $q_joint_wr->fetch_array(MYSQLI_ASSOC);
			$joint_wr			= (!empty($rest_joint_wr['last_cost']))?$rest_joint_wr['last_cost']:0;
		}
		
		
		$joint_csm = 0;
		if($valx['id_category'] == 'field joint' OR $valx['id_category'] == 'shop joint' OR $valx['id_category'] == 'branch joint'){
			$sql_joint_csm    	= "SELECT MAX(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='GLASS' AND id_category='TYP-0004' AND id_milik='".$valx['id']."'";
			$q_joint_csm		= $koneksi->query($sql_joint_csm);
			$rest_joint_csm		= $q_joint_csm->fetch_array(MYSQLI_ASSOC);
			$joint_csm				= (!empty($rest_joint_csm['last_cost']))?$rest_joint_csm['last_cost']:0;
		}
		
		
		$joint_resin = 0;
		if($valx['id_category'] == 'field joint' OR $valx['id_category'] == 'shop joint' OR $valx['id_category'] == 'branch joint'){
			$sql_joint_resin    = "SELECT SUM(last_cost) AS last_cost FROM bq_component_detail WHERE detail_name='RESIN AND ADD' AND id_category='TYP-0001' AND id_milik='".$valx['id']."'";
			$q_joint_resin		= $koneksi->query($sql_joint_resin);
			$rest_joint_resin	= $q_joint_resin->fetch_array(MYSQLI_ASSOC);
			$joint_resin			= (!empty($rest_joint_resin['last_cost']))?$rest_joint_resin['last_cost']:0;
		}
		
		
		$sqlInsertDet = "INSERT INTO 
							laporan_excel_est_bq 
							( 
								id_bq, 
								id_milik, 
								no_komponen, 
								id_category, 
								diameter_1, 
								diameter_2, 
								length, 
								thickness, 
								type, 
								sudut, 
								qty, 
								id_product,
								liner_resin,
								liner_veil,
								liner_csm,
								structure_resin,
								structure_csm,
								structure_wr,
								structure_rooving,
								structuren1_resin,
								structuren1_csm,
								structuren1_wr,
								structuren1_rooving,
								structuren2_resin,
								structuren2_csm,
								structuren2_wr,
								external_resin,
								external_veil,
								external_csm,
								topcoat_resin,
								joint_glass_veil,
								joint_glass_wr,
								joint_glass_csm,
								joint_resin,
								hist_by,
								hist_date
							)
							VALUE
							(
								'".$valx['id_bq']."',
								'".$valx['id']."',
								'".$valx['no_komponen']."',
								'".$valx['id_category']."',
								'".$valx['diameter_1']."',
								'".$valx['diameter_2']."',
								'".$valx['length']."',
								'".$valx['thickness']."',
								'".$valx['type']."',
								'".$valx['sudut']."',
								'".$valx['qty']."',
								'".$valx['id_product']."',
								'".$liner_resin."',
								'".$liner_veil."',
								'".$liner_csm."',
								'".$structure_resin."',
								'".$structure_csm."',
								'".$structure_wr."',
								'".$structure_rooving."',
								'".$structure_resinn1."',
								'".$structure_csmn1."',
								'".$structure_wrn1."',
								'".$structure_roovingn1."',
								'".$structure_resinn2."',
								'".$structure_csmn2."',
								'".$structure_wrn2."',
								'".$external_resin."',
								'".$external_veil."',
								'".$external_csm."',
								'".$topcoat_resin."',
								'".$joint_veil."',
								'".$joint_wr."',
								'".$joint_csm."',
								'".$joint_resin."',
								'system',
								'".date('Y-m-d H:i:s')."'
							)
							";
			// echo $sqlInsertDet."<br><br>";
			$koneksi->query($sqlInsertDet);
	}
	
	$akhir = microtime(true);
	$totalwaktu = $akhir  - $awal;
	
	$sqlInsertStatus = "INSERT INTO laporan_status 
							(`date`,`status`,insert_by, insert_date, category, keterangan)
							VALUE
							('".date('Y-m-d')."','SUCCESS','system','".date('Y-m-d H:i:s')."','approval est bq','eksekusi dalam waktu ".number_format($totalwaktu, 3, '.', '')." detik')   
						";
	$koneksi->query($sqlInsertStatus);
	echo "Success Insert Data";
}

if(empty($restHeader)){
	$akhir = microtime(true);
	$totalwaktu = $akhir  - $awal;

	$sqlInsertStatus = "INSERT INTO laporan_status 
							(`date`,`status`,insert_by, insert_date, category, keterangan)
							VALUE
							('".date('Y-m-d')."','EMPTY','system','".date('Y-m-d H:i:s')."','approval est bq','eksekusi dalam waktu ".number_format($totalwaktu, 3, '.', '')." detik')   
						";
	$koneksi->query($sqlInsertStatus);
	echo "No Data Insert Data";
}

// echo "<br><br>";
// $akhir = microtime(true);
// $totalwaktu = $akhir  - $awal;
echo "<br><br>Halaman ini di eksekusi dalam waktu " . number_format($totalwaktu, 3, '.', '') . " detik!";
?>