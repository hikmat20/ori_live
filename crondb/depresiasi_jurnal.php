<?php 
  
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

$db1 			= new database_ORI();
$koneksi 		= $db1->connect();

$sqlHeader      = "SELECT * FROM asset_jurnal";
$Q_Awal			= $koneksi->query($sqlHeader);

$ArrJurnal = array();
while($row  = $Q_Awal->fetch_array(MYSQLI_ASSOC))
$ArrJurnal[] = $row;

$ArrDebit   = array();
$ArrKredit  = array();
$ArrJavh    = array();
$Loop       = 0;

$sqlDel2 = "DELETE FROM asset_jurnal_temp";
$koneksi->query($sqlDel1);

foreach($ArrJurnal AS $val => $valx){
    $Loop++;
    if($valx['category'] == 1){
        $coaD 	= "6831-02-01";
        $ketD	= "BIAYA PENYUSUTAN KENDARAAN";
        $coaK 	= "1309-05-01";
        $ketK	= "AKUMULASI PENYUSUTAN KENDARAAN";
    }
    if($valx['category'] == 2){
        $coaD 	= "6831-06-01";
        $ketD	= "BIAYA PENYUSUTAN HARTA LAINNYA";
        $coaK 	= "1309-08-01";
        $ketK	= "AKUMULASI PENYUSUTAN HARTA LAINNYA";
    }
    if($valx['category'] == 3){
        $coaD 	= "6831-01-01";
        $ketD	= "BIAYA PENYUSUTAN BANGUNAN";
        $coaK 	= "1309-07-01";
        $ketK	= "AKUMULASI PENYUSUTAN BANGUNAN";
    }
    
    $ArrDebit[$Loop]['category'] 		= $valx['nm_category'];
    $ArrDebit[$Loop]['tipe'] 			= "JV";
    $ArrDebit[$Loop]['nomor'] 			= $Loop;
    $ArrDebit[$Loop]['tanggal'] 		= date('Y-m-d');
    $ArrDebit[$Loop]['no_perkiraan'] 	= $coaD;
    $ArrDebit[$Loop]['keterangan'] 		= $ketD;
    $ArrDebit[$Loop]['kdcab'] 			= $valx['kdcab'];
    $ArrDebit[$Loop]['debet'] 			= $valx['sisa_nilai'];
    $ArrDebit[$Loop]['kredit'] 			= 0;
    
    $ArrKredit[$Loop]['category'] 		= $valx['nm_category'];
    $ArrKredit[$Loop]['tipe'] 			= "JV";
    $ArrKredit[$Loop]['nomor'] 			= $Loop;
    $ArrKredit[$Loop]['tanggal'] 		= date('Y-m-d');
    $ArrKredit[$Loop]['no_perkiraan'] 	= $coaK;
    $ArrKredit[$Loop]['keterangan'] 	= $ketK;
    $ArrKredit[$Loop]['kdcab'] 			= $valx['kdcab'];
    $ArrKredit[$Loop]['debet'] 			= 0;
    $ArrKredit[$Loop]['kredit'] 		= $valx['sisa_nilai'];

    $sqlInsertDet = "INSERT INTO asset_jurnal_temp
                    (category, tipe, nomor, tanggal, no_perkiraan, keterangan, kdcab, debet, kredit)
                    VALUE
                    ('".$valx['nm_category']."','JV','".$Loop."','".date('Y-m-d')."','".$coaD."','".$ketD."','".$valx['kdcab']."','".$valx['sisa_nilai']."','0')";
    $sqlInsertDet2 = "INSERT INTO asset_jurnal_temp
                    (category, tipe, nomor, tanggal, no_perkiraan, keterangan, kdcab, debet, kredit)
                    VALUE
                    ('".$valx['nm_category']."','JV','".$Loop."','".date('Y-m-d')."','".$coaK."','".$ketK."','".$valx['kdcab']."','0','".$valx['sisa_nilai']."')";
    $koneksi->query($sqlInsertDet);
    $koneksi->query($sqlInsertDet2);
}
echo '<pre>';
print_r($ArrDebit);
print_r($ArrKredit);
exit;

//INSERT JURNAL
$SQL_JURNAL      = "SELECT a.*, b.nm_branch FROM asset_jurnal_temp a LEFT JOIN asset_branch b ON a.kdcab=b.id_branch ORDER BY a.kdcab ASC, a.nomor ASC, a.id ASC";
$RESULT_JURNAL			= $koneksi->query($SQL_JURNAL);

$ARR_INSERT_JURNAL = array();
while($ROWS_JURNAL  = $RESULT_JURNAL->fetch_array(MYSQLI_ASSOC))
$ARR_INSERT_JURNAL[] = $ROWS_JURNAL;


?>