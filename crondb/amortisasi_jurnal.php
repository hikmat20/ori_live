<?php 
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

$db1 			= new database_ORI();
$koneksi 		= $db1->connect();

//cek if end of month
$tanggal_exe=date("Y-m-t");
if(date("Y-m-d")!=$tanggal_exe) die("Bukan akhir bulan");

$bulan=date("m");
$tahun=date("Y");

$sqlHeader	= "select * from amortisasi_generate WHERE bulan='".$bulan."' and tahun='".$tahun."' and kd_asset in (select kd_asset from amortisasi where status=1)";
$Q_Awal	= $koneksi->query($sqlHeader);

//echo $sqlHeader."<hr>";

$ArrJurnal = array();
while($row  = $Q_Awal->fetch_array(MYSQLI_ASSOC))
$ArrJurnal[] = $row;

if(!empty($ArrJurnal)){
	$det_Jurnaltes1=array();
	$jenis_jurnal = 'AMORTISASI';
	$nomor_jurnal = $jenis_jurnal . $tahun.$bulan . rand(100, 999);
	$payment_date=date("Y-m-d");
	foreach($ArrJurnal AS $val => $valx){
		$dtrow	= $koneksi->query("select a.*,b.coa as cat_coa from amortisasi a left join amortisasi_category b on a.category=b.id  WHERE kd_asset='".$valx["kd_asset"]."'");

//		echo "select a.*,b.coa as cat_coa from amortisasi a left join amortisasi_category b on a.category=b.id  WHERE kd_asset='".$valx["kd_asset"]."'<hr>";

		if(!empty($dtrow)) {
			$result	= $dtrow->fetch_array(MYSQLI_ASSOC);
		}

		$sqlinsert="insert into jurnaltras (nomor, tanggal, tipe, no_perkiraan, keterangan, no_request, debet, kredit, no_reff, jenis_jurnal, nocust)
		VALUE 
		('".$nomor_jurnal."','".$payment_date."','JV','".$result['coa']."','Amortisasi ".$result['nm_asset'].",".$tahun."-".$bulan."','".$result['kd_asset']."','".$result['value']."','0','".$result['kd_asset']."','".$jenis_jurnal."','')";
		$koneksi->query($sqlinsert);

//		echo $sqlinsert.'<hr>';

		$sqlinsert="insert into jurnaltras (nomor, tanggal, tipe, no_perkiraan, keterangan, no_request, debet, kredit, no_reff, jenis_jurnal, nocust)
		VALUE 
		('".$nomor_jurnal."','".$payment_date."','JV','".$result['cat_coa']."','Amortisasi ".$result['nm_asset'].",".$tahun."-".$bulan."','".$result['kd_asset']."','0','".$result['value']."','".$result['kd_asset']."','".$jenis_jurnal."','')";
		$koneksi->query($sqlinsert);

//		echo $sqlinsert.'<hr>';

		$koneksi->query("update amortisasi_generate set flag='Y' WHERE kd_asset='".$valx["kd_asset"]."' and nomor='".$valx["nomor"]."'");

//		echo "update amortisasi_generate set flag='Y' WHERE kd_asset='".$valx["kd_asset"]."' and nomor='".$valx["nomor"]."'"."<hr>";

	}
}

?>