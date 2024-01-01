<?php

	class database_ORI extends mysqli {
		private $DB_HOST 		= 'localhost';
		private $DB_DATABASE 	= 'sentralsistem';
        private $DB_USER 		= 'sentralsistem';
        private $DB_PASSWORD 	= '$$sentral$$99oo';



		public function __construct() {
			$this->_conn = mysqli_connect($this->DB_HOST, $this->DB_USER, $this->DB_PASSWORD);

			if(!$this->_conn) {
				echo 'Connection failed!<br>';
			}
		}

		public function connect() {
			if(!mysqli_select_db($this->_conn, $this->DB_DATABASE)) {
				die("Cannot connect database..<br>");
			}

			return $this->_conn;

		}

		function get_ph($table, $value){
			$koneksi 	= $this->connect();
			$SQL 		= "SELECT std_rate FROM $table WHERE id='".$value."' ";
			$qMysql		= mysqli_query($koneksi, $SQL);
			$rest		= mysqli_fetch_array($qMysql);

			$data 		= (!empty($rest['std_rate']))?$rest['std_rate']:0;
			return $data;
		}

		function get_con($product){
			$koneksi 	= $this->connect();
	
			$value = '4';
			if($product == 'pipe'){
				$value = '3';
			}
			if($product == 'field joint'){
				$value = '8';
			}
			if($product == 'shop joint' OR $product == 'branch joint'){
				$value = '5';
			}
	
			$SQL 		= "SELECT std_rate FROM cost_process WHERE id = '".$value."'";
			$qMysql		= mysqli_query($koneksi, $SQL);
			$rest		= mysqli_fetch_array($qMysql);

			$data 		= (!empty($rest['std_rate']))?$rest['std_rate']:0;
			return $data;
		}

		function pe_machine($total_time, $id_mesin){
			$koneksi 	= $this->connect();
			$tm_mesin	= 0;
			if(!empty($id_mesin)){
				$SQL	= "SELECT machine_cost_per_hour FROM machine WHERE no_mesin = '".$id_mesin."' LIMIT 1 ";
				$qMysql		= mysqli_query($koneksi, $SQL);
				$rest		= mysqli_fetch_array($qMysql);

				$tm_mesin 		= (!empty($rest['machine_cost_per_hour']))?$rest['machine_cost_per_hour']:0;
			}
			$hasil		= $tm_mesin;
			if($hasil == NULL){
				$hasil	= 0;
			}
			return $hasil;
		}
	
		function pe_mould_mandrill($product_parent, $diameter_1, $diameter_2){
			$koneksi 	= $this->connect();
			$dim2		= ($diameter_2 == '0' OR $diameter_2 == '')?'0':$diameter_2;
			$SQL	= "	SELECT
								biaya_per_pcs
							FROM mould_mandrill
							WHERE product_parent = '".$product_parent."'
								AND diameter = '".$diameter_1."'
								AND diameter2 = '".$dim2."'
							LIMIT 1  ";
			// echo $qHeader;
			$qMysql		= mysqli_query($koneksi, $SQL);
			$rest		= mysqli_fetch_array($qMysql);

			$hasil 		= (!empty($rest['biaya_per_pcs']))?$rest['biaya_per_pcs']:0;
			
			return $hasil;
		}

		function get_profit($product, $dim1, $dim2){
			$koneksi 	= $this->connect();
			$SQL 		= "SELECT profit FROM cost_profit WHERE product_parent='".$product."' AND diameter='".$dim1."' AND diameter2='".$dim2."' LIMIT 1";
			$qMysql		= mysqli_query($koneksi, $SQL);
			$rest		= mysqli_fetch_array($qMysql);

			$hasil 		= (!empty($rest['profit']))?$rest['profit']:0;
			
			return $hasil;
		}

		public function get_weight_comp($id, $series, $product, $dim1, $dim2){
			
			$koneksi 			= $this->connect();

			$date		= date('Y-m-d');
			//get machine
			$field_ = 'last_cost';
			if($product == 'shop joint' OR $product == 'branch joint' OR $product == 'field joint'){
				$field_ = 'material_weight';
			}
	
			$wherePN = floatval(substr($series, 3,2));
			$whereLN = floatval(substr($series, 6,3));
	
			$wherePlus = " AND diameter='".$dim1."' ";
			if($product == 'concentric reducer' OR $product == 'eccentric reducer' OR $product == 'reducer tee mould' OR $product == 'reducer tee slongsong'){
				$wherePlus = " AND diameter='".$dim1."' AND diameter2 = '".$dim2."' ";
			}
			if($product == 'branch joint'){
				$wherePlus = " AND diameter2 = '".$dim2."' ";
			}
			$qSeries = "SELECT man_power, id_mesin, total_time, man_hours FROM cycletime_default WHERE product_parent='".$product."' ".$wherePlus." AND pn='".$wherePN."' AND liner='".$whereLN."' LIMIT 1 ";
			$qSeries2	= mysqli_query($koneksi, $qSeries);
			$restSer	= mysqli_fetch_array($qSeries2);

			$total_time 	= (!empty($restSer['total_time']))?$restSer['total_time']:0;
			$id_mesin 		= (!empty($restSer['id_mesin']))?$restSer['id_mesin']:'';
			$man_hours 		= (!empty($restSer['man_hours']))?$restSer['man_hours']:0;
			
			$eEx_resin 	= "	SELECT
								SUM(a.$field_) AS berat,
								SUM(a.$field_ * b.price_ref_estimation) AS price
							FROM component_detail a
								LEFT JOIN raw_materials b ON a.id_material=b.id_material
							WHERE 1=1
								AND a.id_product='".$id."'
								AND a.id_category <> 'TYP-0001'
								AND a.id_material <> 'MTL-1903000'
								AND a.id_category <> 'TYP-0030'";
			$eEx_resin2X	= mysqli_query($koneksi, $eEx_resin);
			$rEx_resin		= mysqli_fetch_array($eEx_resin2X);
			
			$direct 	= $this->get_ph('cost_process', 1);
			$indirect 	= $this->get_ph('cost_process', 2);
			$machine 	= $this->pe_machine($total_time, $id_mesin) * $total_time;
			$mould 		= $this->pe_mould_mandrill($product, $dim1, $dim2);
			$consumable = $this->get_con($product);
			$foh_consumable 			= $this->get_ph('cost_foh', 1) / 100;
			$foh_depresiasi 			= $this->get_ph('cost_foh', 2) / 100;
			$biaya_gaji_non_produksi 	= $this->get_ph('cost_foh', 3) / 100;
			$biaya_non_produksi 		= $this->get_ph('cost_foh', 4) / 100;
			$biaya_rutin_bulanan 		= $this->get_ph('cost_foh', 5) / 100;
			$p_profit 	= $this->get_profit($product, $dim1, $dim2) / 100;
	
			$wExResin 	= (!empty($rEx_resin['berat']))?$rEx_resin['berat']:0;
			$wExPrice 	= (!empty($rEx_resin['price']))?$rEx_resin['price']:0;
			
			

			$eEx_resin2 	= "	SELECT
									SUM(a.last_cost) AS berat,
									SUM(a.last_cost * b.price_ref_estimation) AS price
								FROM component_detail_plus a
									LEFT JOIN raw_materials b ON a.id_material=b.id_material
								WHERE 1=1
									AND a.id_product='".$id."'
									AND a.id_category <> 'TYP-0001'
									AND a.id_material <> 'MTL-1903000'
									AND a.id_category <> 'TYP-0030'";
	
			$eEx_resin2V	= mysqli_query($koneksi, $eEx_resin2);
			$rEx_resin2		= mysqli_fetch_array($eEx_resin2V);
			$wExResin2 	= (!empty($rEx_resin2['berat']))?$rEx_resin2['berat']:0;
			$wExPrice2 	= (!empty($rEx_resin2['price']))?$rEx_resin2['price']:0;
			
			$eEx_resin3 	= "	SELECT
									SUM(a.last_cost) AS berat,
									SUM(a.last_cost * b.price_ref_estimation) AS price
								FROM component_detail_add a
									LEFT JOIN raw_materials b ON a.id_material=b.id_material
								WHERE 1=1
									AND a.id_material <> 'MTL-1903000'
									AND a.id_product='".$id."'
									AND a.id_category <> 'TYP-0001'";
	
			$eEx_resin3x	= mysqli_query($koneksi, $eEx_resin3);
			$rEx_resin3		= mysqli_fetch_array($eEx_resin3x);
			$wExResin3 	= (!empty($rEx_resin3['berat']))?$rEx_resin3['berat']:0;
			$wExPrice3 	= (!empty($rEx_resin3['price']))?$rEx_resin3['price']:0;
	
			$e_resin 	= "	SELECT
								MAX(a.$field_) AS berat,
								(MAX(a.$field_) * b.price_ref_estimation) AS price
							FROM component_detail a
								LEFT JOIN raw_materials b ON a.id_material=b.id_material
							WHERE 1=1
								AND a.id_material <> 'MTL-1903000'
								AND a.id_product='".$id."'
								AND a.id_category = 'TYP-0001'
							GROUP BY
								a.detail_name";
			
			$qPro		= $koneksi->query($e_resin);
			while($row  = $qPro->fetch_array(MYSQLI_ASSOC))
			$r_resin[] = $row;

			$SUM1 = 0;
			$SUMP1 = 0;
			foreach($r_resin AS $valx => $val){
				$SUM1 += $val['berat'];
				$SUMP1 += $val['price'];
			}
			$wResin 	= (!empty($r_resin))?$SUM1:0;
			$wPrice 	= (!empty($r_resin))?$SUMP1:0;

			if($product == 'shop joint' OR $product == 'branch joint' OR $product == 'field joint'){
				$e_resinJN 	= "	SELECT
								SUM(a.$field_) AS berat,
								(SUM(a.$field_ * b.price_ref_estimation)) AS price
							FROM component_detail a
								LEFT JOIN raw_materials b ON a.id_material=b.id_material
							WHERE 1=1
								AND a.id_material <> 'MTL-1903000'
								AND a.id_product='".$id."'
								AND a.id_category = 'TYP-0001'";
				$qMysql		= mysqli_query($koneksi, $e_resinJN);
				$r_resinJN		= mysqli_fetch_array($qMysql);
	
				$wResin 	= (!empty($r_resinJN['berat']))?$r_resinJN['berat']:0;
				$wPrice 	= (!empty($r_resinJN['price']))?$r_resinJN['price']:0;
			}
			
			$e_resin2 	= "	SELECT
								MAX(a.last_cost) AS berat,
								(MAX(a.last_cost) * b.price_ref_estimation) AS price
							FROM component_detail_plus a
								LEFT JOIN raw_materials b ON a.id_material=b.id_material
							WHERE 1=1
								AND a.id_material <> 'MTL-1903000'
								AND a.id_product='".$id."'
								AND a.id_category = 'TYP-0001'
							GROUP BY
								a.detail_name";
	
			$qPro2		= $koneksi->query($e_resin2);
			while($row2  = $qPro2->fetch_array(MYSQLI_ASSOC))
			$r_resin2[] = $row2;

			$SUM2 = 0;
			$SUMP2 = 0;
			foreach($r_resin2 AS $valx => $val){
				$SUM2 += $val['berat'];
				$SUMP2 += $val['price'];
			}
			$wResin2 	= (!empty($r_resin2))?$SUM2:0;
			$wPrice2 	= (!empty($r_resin2))?$SUMP2:0;
			// echo 'Masuk'; exit;
			$weight 	= $wExResin + $wExResin2 + $wExResin3 + $wResin + $wResin2;
			$price 		= $wExPrice + $wExPrice2 + $wExPrice3 + $wPrice + $wPrice2;
			$process 	= ($man_hours * $direct) + ($man_hours * $indirect) + $machine + $mould + ($price * $consumable);
			$consumab 	= $price * $consumable;
			$foh 		= (($process + $price) * $foh_consumable) + (($process + $price) * $foh_depresiasi) + (($process + $price) * $biaya_gaji_non_produksi) + (($process + $price) * $biaya_non_produksi) + (($process + $price) * $biaya_rutin_bulanan);
			$profit 	= ($price + $process + $foh) * $p_profit;
	
			$data	= array(
				'weight' => $weight,
				'price' => $price,
				'process' => $process,
				'foh' => $foh,
				'profit' => $profit
			);
			return $data;
		}

		public function sample() {
			return 'Hay';
		}

	}



?>
