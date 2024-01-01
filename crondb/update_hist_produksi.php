<?php 
  
date_default_timezone_set("Asia/Bangkok"); 
include_once 'function_connect.php';

//echo "<pre>";print_r(file_get_contents('function_connec.php'));exit;
//echo"masuk bro".$_SERVER['DOCUMENT_ROOT'];
$db1 				= new database_ORI();
$koneksi 			= $db1->connect();
$sqlTrunc = "TRUNCATE TABLE table_history_pro_header";
$koneksi->query($sqlTrunc);
   
$sqlInsertHead = "
                    INSERT INTO table_history_pro_header ( id_produksi, 
                    id_category, 
                    id_product, 
                    product_ke, 
                    qty_akhir, 
                    qty, 
                    status_by, 
                    status_date, 
                    id_production_detail, 
                    id,
                    id_milik, 
                    create_by, 
                    create_date ) 
                    SELECT
                        a.id_produksi,
                        a.id_category,
                        a.id_product,
                        a.product_ke,
                        a.qty_akhir,
                        a.qty,
                        a.status_by,
                        a.status_date,
                        a.id_production_detail,
                        a.id,
                        a.id_milik,
                        'System',
                        '".date('Y-m-d H:i:s')."'
                        FROM
                            history_pro_header a";
$koneksi->query($sqlInsertHead);


?>