<?php
class Penawaran_model extends CI_Model {

	public function __construct() {
		parent::__construct();
	}
	
	public function add_penawaran(){
		$id_bq		= $this->uri->segment(3);
		$getEx		= explode('-', $id_bq);
		$ipp		= $getEx[1];

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
		$row		= $this->db->query($qSupplier)->result();
		
		$qMatr 		= SQL_Quo($id_bq);
		$rowDet		= $this->db->query($qMatr)->result_array();
		
		$engC 		= "SELECT * FROM cost_engine ORDER BY id ASC ";
		$rowengC	= $this->db->query($engC)->result_array();
		
		$engCPC 	= "SELECT * FROM list_help WHERE group_by = 'pack cost' ORDER BY id ASC ";
		$rowengCPC	= $this->db->query($engCPC)->result_array();
		
		$gTruck 	= "SELECT
							a.shipping_name,
							a.type,
							(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".substr($id_bq, 3,9)."') as country_code,
							(SELECT d.country_name FROM country d WHERE d.country_code=(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".substr($id_bq, 3,9)."')) as country_name,
							(SELECT c.price FROM cost_export_trans c WHERE c.shipping_name = CONCAT(a.shipping_name, ' ', a.type) AND c.country_code = (SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".substr($id_bq, 3,9)."') AND c.deleted='N')  as price
						FROM
							list_shipping a
						WHERE
							a.flag = 'Y' 
						ORDER BY
							a.urut ASC";
		$rowgTruck	= $this->db->query($gTruck)->result_array();
		
		$gTruckP 	= "SELECT * FROM list_packing WHERE flag = 'Y' ORDER BY urut ASC ";
		$rowgTruckP	= $this->db->query($gTruckP)->result_array();
		
		$engCPCV 	= "SELECT * FROM list_help WHERE group_by = 'via' ORDER BY urut	 ASC ";
		$rowengCPCV	= $this->db->query($engCPCV)->result_array();
		
		$qOpt 		= "SELECT * FROM list_help WHERE group_by = 'opt' ORDER BY id DESC ";
		$getOpt		= $this->db->query($qOpt)->result_array();
		
		$qOptPl 	= "SELECT * FROM list_help WHERE group_by = 'opt' OR group_by = 'opt plus' ORDER BY id DESC ";
		$getOptPl	= $this->db->query($qOptPl)->result_array();
		
		$qArea		= "SELECT area FROM cost_trucking WHERE category='darat' GROUP BY area ORDER BY area ASC ";
		$getArea	= $this->db->query($qArea)->result_array();
		
		$qAreaL		= "SELECT area FROM cost_trucking WHERE category='laut' GROUP BY area ORDER BY area ASC ";
		$getAreaL	= $this->db->query($qAreaL)->result_array();
		
		$sql_non_frp	= "SELECT * FROM bq_acc_and_mat WHERE category='acc' AND id_bq='".$id_bq."' ";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material	= "SELECT * FROM bq_acc_and_mat WHERE category='mat' AND id_bq='".$id_bq."' ";
		$material		= $this->db->query($sql_material)->result_array();
		
		$data = array(
			'title'			=> 'Offer Structure New',
			'action'		=> 'index',
			'getHeader'		=> $row,
			'getDetail'		=> $rowDet,
			'getEngCost'	=> $rowengC,
			'getPackCost'	=> $rowengCPC,
			'getPackP'		=> $rowgTruckP,
			'getTruck'		=> $rowgTruck,
			'getVia'		=> $rowengCPCV,
			'getOpt'		=> $getOpt,
			'getOptP'		=> $getOptPl,
			'getArea'		=> $getArea,
			'getAreaL'		=> $getAreaL,
			'non_frp'		=> $non_frp,
			'material'		=> $material
		);
		$this->load->view('Penawaran/add_penawaran',$data);
	}
	
	public function save_penawaran(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$MatCost	= $data['MatCost'];
		$EngCost	= $data['EngCost'];
		$PackCost	= $data['PackCost'];
		$ExportCost	= $data['ExportCost'];
		$LokalCost	= $data['LokalCost'];
		
		$ArrHeader = array(
			'id_bq' 		=> $data['id_bq'],
			'project' 		=> $data['project'],
			'customer' 		=> $data['customer'],
			'price_project' => $data['total_all'],
			'created_by' 	=> $data_session['ORI_User']['username'],
			'created_date' 	=> date('Y-m-d H:i:s')
		);
		
		$ArrMatCost = array();
		foreach($MatCost AS $val => $valx){
			$ArrMatCost[$val]['id_bq']			= $data['id_bq'];
			$ArrMatCost[$val]['category']		= $valx['category'];
			$ArrMatCost[$val]['caregory_sub']	= $valx['id_milik'];
			$ArrMatCost[$val]['extra']			= str_replace(',','',$valx['extra']);
			$ArrMatCost[$val]['persen']			= str_replace(',','',$valx['persen']);
			$ArrMatCost[$val]['fumigasi']		= $valx['harga'];
			$ArrMatCost[$val]['price']			= $valx['harga_total1'];
			$ArrMatCost[$val]['price_total']	= $valx['harga_total'];
		}
		
		$ArrEngCost = array();
		foreach($EngCost AS $val => $valx){
			$ArrEngCost[$val]['id_bq']			= $data['id_bq'];
			$ArrEngCost[$val]['category']		= $valx['category'];
			$ArrEngCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrEngCost[$val]['option_type']	= $valx['option_type'];
			$ArrEngCost[$val]['qty']			= $valx['qty'];
			$ArrEngCost[$val]['unit']			= (!empty($valx['unit']))?$valx['unit']:'-';
			$ArrEngCost[$val]['price']			= $valx['price'];
			$ArrEngCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrPackCost = array();
		foreach($PackCost AS $val => $valx){
			$ArrPackCost[$val]['id_bq']			= $data['id_bq'];
			$ArrPackCost[$val]['category']		= $valx['category'];
			$ArrPackCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrPackCost[$val]['option_type']	= $valx['option_type'];
			$ArrPackCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrExportCost = array();
		foreach($ExportCost AS $val => $valx){
			$ArrExportCost[$val]['id_bq']			= $data['id_bq'];
			$ArrExportCost[$val]['category']		= $valx['category'];
			$ArrExportCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrExportCost[$val]['option_type']		= $valx['option_type'];
			$ArrExportCost[$val]['qty']				= $valx['qty'];
			// $ArrExportCost[$val]['fumigasi']		= $valx['fumigasi'];
			$ArrExportCost[$val]['price']			= $valx['price'];
			$ArrExportCost[$val]['price_total']		= $valx['price_total'];
		}
		
		$ArrLokalCost = array();
		foreach($LokalCost AS $val => $valx){
			$ArrLokalCost[$val]['id_bq']			= $data['id_bq'];
			$ArrLokalCost[$val]['category']			= $valx['category'];
			$ArrLokalCost[$val]['caregory_sub']		= $valx['caregory_sub'];
			$ArrLokalCost[$val]['area']				= $valx['area'];
			$ArrLokalCost[$val]['tujuan']			= $valx['tujuan'];
			$ArrLokalCost[$val]['kendaraan']		= $valx['kendaraan'];
			$ArrLokalCost[$val]['qty']				= $valx['qty'];
			$ArrLokalCost[$val]['price']			= $valx['price'];
			$ArrLokalCost[$val]['price_total']		= $valx['price_total'];
		}
		
		$ArrEditCost = array(
			'sts_price_quo'			=> 'Y',
			'sts_price_quo_by' 		=> $data_session['ORI_User']['username'],
			'sts_price_quo_date' 	=> date('Y-m-d H:i:s')
		);
		
		//NONFRP
		$ArrNonFrp = array();
		if(!empty($data['nonfrp'])){
			$nonfrp	= $data['nonfrp'];
			foreach($nonfrp AS $val => $valx){
				$ArrNonFrp[$val]['id_bq']			= $data['id_bq'];
				$ArrNonFrp[$val]['category']		= $valx['category'];
				$ArrNonFrp[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrNonFrp[$val]['option_type']		= $valx['option_type'];
				$ArrNonFrp[$val]['qty']				= str_replace(',','',$valx['qty']);
				$ArrNonFrp[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrNonFrp[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrNonFrp[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrNonFrp[$val]['price']			= $valx['price'];
				$ArrNonFrp[$val]['price_total']		= $valx['price_total'];
			}
		}
		
		//NONFRP
		$ArrMaterial = array();
		if(!empty($data['material'])){
			$material	= $data['material'];
			foreach($material AS $val => $valx){
				$ArrMaterial[$val]['id_bq']			= $data['id_bq'];
				$ArrMaterial[$val]['category']		= $valx['category'];
				$ArrMaterial[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrMaterial[$val]['option_type']	= $valx['option_type'];
				$ArrMaterial[$val]['weight']		= str_replace(',','',$valx['qty']);
				$ArrMaterial[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrMaterial[$val]['persen']		= str_replace(',','',$valx['persen']);
				$ArrMaterial[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrMaterial[$val]['price']			= $valx['price'];
				$ArrMaterial[$val]['price_total']	= $valx['price_total'];
			}
		}
		
		// print_r($ArrMatCost);
		// print_r($ArrEngCost);
		// print_r($ArrPackCost);
		
		// print_r($ArrNonFrp); 
		// print_r($ArrMaterial);
		
		// exit;
		
		$this->db->trans_start();
			$this->db->insert('cost_project_header', $ArrHeader);
			$this->db->insert_batch('cost_project_detail', $ArrMatCost);
			$this->db->insert_batch('cost_project_detail', $ArrEngCost);
			$this->db->insert_batch('cost_project_detail', $ArrPackCost);
			$this->db->insert_batch('cost_project_detail', $ArrExportCost);
			$this->db->insert_batch('cost_project_detail', $ArrLokalCost);
			
			if(!empty($ArrNonFrp)){
				$this->db->insert_batch('cost_project_detail', $ArrNonFrp);
			}
			if(!empty($ArrMaterial)){
				$this->db->insert_batch('cost_project_detail', $ArrMaterial);
			}
			
			$this->db->where('no_ipp', $data['no_ipp']);
			$this->db->update('production', $ArrEditCost);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation success. Thanks ...',
				'status'	=> 1
			);				
			history('Cost Quotation new with : '.$data['id_bq']);
		}
		echo json_encode($Arr_Data);
	}
	
	public function edit_penawaran_new(){
		$controller			= ucfirst(strtolower($this->uri->segment(1))).'/edit_penawaran_new';
		
		$id_bq	= $this->uri->segment(3);
		$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];

		$qSupplier 	= "SELECT * FROM production WHERE no_ipp = '".$ipp."' ";
		$row		= $this->db->query($qSupplier)->result();
		
		
		$qMatr 		= SQL_Quo_Edit($id_bq);
		$rowDet		= $this->db->query($qMatr)->result_array();
		
		$engC 		= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'eng cost' AND b.category = 'engine' AND b.id_bq='".$id_bq."' ORDER BY a.id ASC ";
		$rowengC	= $this->db->query($engC)->result_array();
		// echo $engC;
		$engCPC 	= "SELECT a.*, b.* FROM list_help a INNER JOIN cost_project_detail b ON a.name=b.caregory_sub WHERE a.group_by = 'pack cost' AND b.category = 'packing' AND b.id_bq='".$id_bq."' ORDER BY a.id ASC ";
		$rowengCPC	= $this->db->query($engCPC)->result_array();
		
		$gTruck 	= "SELECT
							e.*,
							a.shipping_name,
							a.type,
							(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".substr($id_bq, 3,9)."') as country_code,
							(SELECT d.country_name FROM country d WHERE d.country_code=(SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".substr($id_bq, 3,9)."')) as country_name,
							(SELECT c.price FROM cost_export_trans c WHERE c.shipping_name = CONCAT(a.shipping_name, ' ', a.type) AND c.country_code = (SELECT b.country_code FROM production_delivery b WHERE b.no_ipp='".substr($id_bq, 3,9)."') AND c.deleted='N')  as price
						FROM
							list_shipping a
								INNER JOIN cost_project_detail e ON CONCAT_WS(' ',a.shipping_name, a.type)=e.caregory_sub
						WHERE
							a.flag = 'Y' AND e.category = 'export' AND e.id_bq='".$id_bq."'
						ORDER BY
							a.urut ASC";
		$rowgTruck	= $this->db->query($gTruck)->result_array();
		
		$gTruckP 	= "SELECT * FROM list_packing WHERE flag = 'Y' ORDER BY urut ASC ";
		$rowgTruckP	= $this->db->query($gTruckP)->result_array();
		
		$engCPCV 	= "SELECT
							b.*,
							c.* 
						FROM
							cost_project_detail b
							LEFT JOIN truck c ON b.kendaraan = c.id 
						WHERE
							 b.category = 'lokal' 
							AND b.id_bq = '".$id_bq."' 
						ORDER BY
							b.id ASC ";
		$rowengCPCV	= $this->db->query($engCPCV)->result_array();
		
		$qOpt 		= "SELECT * FROM list_help WHERE group_by = 'opt' ORDER BY id DESC ";
		$getOpt		= $this->db->query($qOpt)->result_array();
		
		$qOptPl 	= "SELECT * FROM list_help WHERE group_by = 'opt' OR group_by = 'opt plus' ORDER BY id DESC ";
		$getOptPl	= $this->db->query($qOptPl)->result_array();
		
		$qArea		= "SELECT area FROM cost_trucking WHERE category='darat' GROUP BY area ORDER BY area ASC ";
		$getArea	= $this->db->query($qArea)->result_array();
		
		$qAreaL		= "SELECT area FROM cost_trucking WHERE category='laut' GROUP BY area ORDER BY area ASC ";
		$getAreaL	= $this->db->query($qAreaL)->result_array();
		
		$sql_non_frp	= "	SELECT 
								b.* 
							FROM 
								bq_acc_and_mat b
							WHERE 
								b.category='acc'
								AND b.id_bq='".$id_bq."' ";
		$non_frp		= $this->db->query($sql_non_frp)->result_array();
		
		$sql_material	= "	SELECT 
								b.* 
							FROM
								bq_acc_and_mat b
							WHERE 
								b.category='mat'
								AND b.id_bq='".$id_bq."' ";
		$material		= $this->db->query($sql_material)->result_array();
		// echo $sql_non_frp;
		
		$data = array(
			'title'		=> 'Offer Structure',
			'action'	=> 'updateReal',
			'id_bq'			=> $id_bq,
			'getHeader'		=> $row,
			'getDetail'		=> $rowDet,
			'getEngCost'	=> $rowengC,
			'getPackCost'	=> $rowengCPC,
			'getPackP'	=> $rowgTruckP,
			'getTruck'		=> $rowgTruck,
			'getVia'		=> $rowengCPCV,
			'getOpt'		=> $getOpt,
			'getOptP'		=> $getOptPl,
			'getArea'		=> $getArea,
			'getAreaL'		=> $getAreaL,
			'non_frp'		=> $non_frp,
			'material'		=> $material
		);
		$this->load->view('Penawaran/edit_penawaran_new',$data);
	}
	
	public function save_edit_penawaran_new(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		$MatCost	= $data['MatCost'];
		$EngCost	= $data['EngCost'];
		$PackCost	= $data['PackCost'];
		$ExportCost	= $data['ExportCost'];
		$LokalCost	= $data['LokalCost'];
		
		$ArrHeader = array(
			'id_bq' 		=> $data['id_bq'],
			'project' 		=> $data['project'],
			'customer' 		=> $data['customer'],
			'price_project' => $data['total_all'],
			'updated_by' 	=> $data_session['ORI_User']['username'],
			'updated_date' 	=> date('Y-m-d H:i:s')
		);
		
		$ArrMatCost = array();
		foreach($MatCost AS $val => $valx){
			$ArrMatCost[$val]['id_bq']			= $data['id_bq'];
			$ArrMatCost[$val]['category']		= $valx['category'];
			$ArrMatCost[$val]['caregory_sub']	= $valx['id_milik'];
			$ArrMatCost[$val]['persen']			= $valx['persen'];
			$ArrMatCost[$val]['extra']			= $valx['extra'];
			$ArrMatCost[$val]['fumigasi']		= $valx['harga'];
			$ArrMatCost[$val]['price']			= $valx['harga_total1'];
			$ArrMatCost[$val]['price_total']	= $valx['harga_total'];
		}
		
		$ArrEngCost = array();
		foreach($EngCost AS $val => $valx){
			$ArrEngCost[$val]['id_bq']			= $data['id_bq'];
			$ArrEngCost[$val]['category']		= $valx['category'];
			$ArrEngCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrEngCost[$val]['option_type']	= $valx['option_type'];
			$ArrEngCost[$val]['qty']			= $valx['qty'];
			$ArrEngCost[$val]['unit']			= (!empty($valx['unit']))?$valx['unit']:'-';
			$ArrEngCost[$val]['price']			= $valx['price'];
			$ArrEngCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrPackCost = array();
		foreach($PackCost AS $val => $valx){
			$ArrPackCost[$val]['id_bq']			= $data['id_bq'];
			$ArrPackCost[$val]['category']		= $valx['category'];
			$ArrPackCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrPackCost[$val]['option_type']	= $valx['option_type'];
			$ArrPackCost[$val]['price_total']	= $valx['price_total'];
		}
		
		$ArrExportCost = array();
		foreach($ExportCost AS $val => $valx){
			$ArrExportCost[$val]['id_bq']			= $data['id_bq'];
			$ArrExportCost[$val]['category']		= $valx['category'];
			$ArrExportCost[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrExportCost[$val]['option_type']		= $valx['option_type'];
			$ArrExportCost[$val]['qty']				= $valx['qty'];
			// $ArrExportCost[$val]['fumigasi']		= $valx['fumigasi'];
			$ArrExportCost[$val]['price']			= $valx['price'];
			$ArrExportCost[$val]['price_total']		= $valx['price_total'];
		}
		
		$ArrLokalCost = array();
		foreach($LokalCost AS $val => $valx){
			$ArrLokalCost[$val]['id_bq']			= $data['id_bq'];
			$ArrLokalCost[$val]['category']			= $valx['category'];
			$ArrLokalCost[$val]['caregory_sub']		= $valx['caregory_sub'];
			$ArrLokalCost[$val]['area']				= $valx['area'];
			$ArrLokalCost[$val]['tujuan']			= $valx['tujuan'];
			$ArrLokalCost[$val]['kendaraan']		= $valx['kendaraan'];
			$ArrLokalCost[$val]['qty']				= $valx['qty'];
			$ArrLokalCost[$val]['price']			= $valx['price'];
			$ArrLokalCost[$val]['price_total']		= $valx['price_total'];
		}
		
		// $restHistHeader = $this->db->query("SELECT * FROM cost_project_header WHERE id_bq='".$data['id_bq']."' ")->result_array();
		$restHistHeader = $this->db->get_where('cost_project_header', array('id_bq' => $data['id_bq']))->result_array();
		$ArrHeaderHist = array();
		foreach($restHistHeader AS $val => $valx){
			$ArrHeaderHist[$val]['id_bq']			= $data['id_bq'];
			$ArrHeaderHist[$val]['project']			= $valx['project'];
			$ArrHeaderHist[$val]['customer']		= $valx['customer'];
			$ArrHeaderHist[$val]['job_number']		= $valx['job_number'];
			$ArrHeaderHist[$val]['delivery']		= $valx['delivery'];
			$ArrHeaderHist[$val]['delivery_point']	= $valx['delivery_point'];
			$ArrHeaderHist[$val]['price_project']	= $valx['price_project'];
			$ArrHeaderHist[$val]['created_by']		= $valx['created_by'];
			$ArrHeaderHist[$val]['created_date']	= $valx['created_date'];
			$ArrHeaderHist[$val]['updated_by']		= $valx['updated_by'];
			$ArrHeaderHist[$val]['updated_date']	= $valx['updated_date'];
			
			$ArrHeaderHist[$val]['hist_by']			= $data_session['ORI_User']['username'];
			$ArrHeaderHist[$val]['hist_date']		= date('Y-m-d H:i:s');
		}
		
		// $restHistDetail = $this->db->query("SELECT * FROM cost_project_detail WHERE id_bq='".$data['id_bq']."' ")->result_array();
		$restHistDetail = $this->db->get_where('cost_project_detail', array('id_bq' => $data['id_bq']))->result_array();
		$ArrDetailHist = array();
		foreach($restHistDetail AS $val => $valx){
			$ArrDetailHist[$val]['id_bq']			= $data['id_bq'];
			$ArrDetailHist[$val]['category']		= $valx['category'];
			$ArrDetailHist[$val]['caregory_sub']	= $valx['caregory_sub'];
			$ArrDetailHist[$val]['option_type']		= $valx['option_type'];
			$ArrDetailHist[$val]['area']			= $valx['area'];
			$ArrDetailHist[$val]['tujuan']			= $valx['tujuan'];
			$ArrDetailHist[$val]['kendaraan']		= $valx['kendaraan'];
			$ArrDetailHist[$val]['unit']			= $valx['unit'];
			$ArrDetailHist[$val]['qty']				= $valx['qty'];
			$ArrDetailHist[$val]['extra']			= $valx['extra'];
			$ArrDetailHist[$val]['persen']			= $valx['persen'];
			$ArrDetailHist[$val]['fumigasi']		= $valx['fumigasi'];
			$ArrDetailHist[$val]['price']			= $valx['price'];
			$ArrDetailHist[$val]['price_total']		= $valx['price_total'];
			
			$ArrDetailHist[$val]['hist_by']		= $data_session['ORI_User']['username'];
			$ArrDetailHist[$val]['hist_date']		= date('Y-m-d H:i:s');
		}
		
		//NONFRP
		$ArrNonFrp = array();
		if(!empty($data['nonfrp'])){
			$nonfrp	= $data['nonfrp'];
			foreach($nonfrp AS $val => $valx){
				$ArrNonFrp[$val]['id_bq']			= $data['id_bq'];
				$ArrNonFrp[$val]['category']		= $valx['category'];
				$ArrNonFrp[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrNonFrp[$val]['option_type']		= $valx['option_type'];
				$ArrNonFrp[$val]['qty']				= str_replace(',','',$valx['qty']);
				$ArrNonFrp[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrNonFrp[$val]['persen']			= str_replace(',','',$valx['persen']);
				$ArrNonFrp[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrNonFrp[$val]['price']			= $valx['price'];
				$ArrNonFrp[$val]['price_total']		= $valx['price_total'];
			}
		}
		
		//NONFRP
		$ArrMaterial = array();
		if(!empty($data['material'])){
			$material	= $data['material'];
			foreach($material AS $val => $valx){
				$ArrMaterial[$val]['id_bq']			= $data['id_bq'];
				$ArrMaterial[$val]['category']		= $valx['category'];
				$ArrMaterial[$val]['caregory_sub']	= $valx['caregory_sub'];
				$ArrMaterial[$val]['option_type']	= $valx['option_type'];
				$ArrMaterial[$val]['weight']		= str_replace(',','',$valx['qty']);
				$ArrMaterial[$val]['extra']			= str_replace(',','',$valx['extra']);
				$ArrMaterial[$val]['persen']		= str_replace(',','',$valx['persen']);
				$ArrMaterial[$val]['fumigasi']		= $valx['fumigasi'];
				$ArrMaterial[$val]['price']			= $valx['price'];
				$ArrMaterial[$val]['price_total']	= $valx['price_total'];
			}
		}
		
		
		//ToHistory
		
		
		
		// print_r($ArrMatCost);
		// print_r($ArrEngCost);
		// print_r($ArrPackCost);
		// print_r($ArrExportCost);
		// print_r($LokalCost); 
		
		
		// exit;
		
		$this->db->trans_start();
			$this->db->insert_batch('hist_cost_project_header', $ArrHeaderHist);
			$this->db->insert_batch('hist_cost_project_detail', $ArrDetailHist);
			
			$this->db->delete('cost_project_header', array('id_bq' => $data['id_bq'])); 
			
			$this->db->delete('cost_project_detail', array('id_bq' => $data['id_bq']));  
			
			$this->db->insert('cost_project_header', $ArrHeader);
			$this->db->insert_batch('cost_project_detail', $ArrMatCost);
			$this->db->insert_batch('cost_project_detail', $ArrEngCost);
			$this->db->insert_batch('cost_project_detail', $ArrPackCost);
			$this->db->insert_batch('cost_project_detail', $ArrExportCost);
			$this->db->insert_batch('cost_project_detail', $ArrLokalCost);
			
			if(!empty($ArrNonFrp)){
				$this->db->insert_batch('cost_project_detail', $ArrNonFrp);
			}
			if(!empty($ArrMaterial)){
				$this->db->insert_batch('cost_project_detail', $ArrMaterial);
			}

		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Cost Quotation success. Thanks ...',
				'status'	=> 1
			);				
			history('Edit Cost Quotation new with : '.$data['id_bq']);
		}
		echo json_encode($Arr_Data);
	}
	
	
	//==========================================================================================================
	//========================================PENAWARAN SALES===================================================
	//==========================================================================================================
	
	public function edit_penawaran_sales(){
		
		$id_bq	= $this->uri->segment(3);
		$getEx	= explode('-', $id_bq);
		$ipp	= $getEx[1];
		
		$get_max_rev = $this->db->select('MAX(revised_no) AS revised_no')->get_where('laporan_revised_detail', array('id_bq'=>$id_bq))->result();
		

		$sql_header 	= "SELECT a.*, b.job_number, b.quo_number FROM production a LEFT JOIN cost_project_header_sales b ON a.no_ipp=SUBSTR(b.id_bq, 4,9) WHERE a.no_ipp = '".$ipp."' LIMIT 1";
		$rest_header	= $this->db->query($sql_header)->result();
							
		$sql_detail 	= "	SELECT
							a.id_bq,
							a.id AS id_milik,
							a.id_category,
							a.qty,
							a.diameter_1,
							a.diameter_2,
							a.series,
							a.id_product,
							b.price_total AS cost,
							c.est_material
						FROM
							bq_detail_header a 
							LEFT JOIN cost_project_detail b ON a.id=b.caregory_sub
							LEFT JOIN laporan_revised_detail c ON a.id=c.id_milik
						WHERE
							a.id_bq = '".$id_bq."' AND c.revised_no = '".$get_max_rev[0]->revised_no."'";		
		$rest_detail	= $this->db->query($sql_detail)->result_array();
		
		$ListBQipp		= $this->db->query("SELECT series FROM bq_detail_header WHERE id_bq = '".$id_bq."' GROUP BY series")->result_array();
		$dtListArray = array();
		foreach($ListBQipp AS $val => $valx){
			$dtListArray[$val] = $valx['series'];
		}
		$dtImplode	= "".implode(", ", $dtListArray)."";

		$data = array(
			'title'			=> 'Offer Structure',
			'action'		=> 'updateReal',
			'getHeader'		=> $rest_header,
			'getDetail'		=> $rest_detail,
			'series'		=> $dtImplode
		);
		$this->load->view('Penawaran/edit_penawaran_sales',$data);
	}
	
	public function save_edit_penawaran_sales(){
		$data_session	= $this->session->userdata;
		$data	= $this->input->post();
		
		// $MatCost	= $data['MatCost'];
		
		$ArrHeader = array(
			'id_bq' 		=> $data['id_bq'],
			'project' 		=> $data['project'],
			'customer' 		=> $data['customer'],
			'job_number' 	=> $data['job_number'],
			'quo_number' 	=> $data['quo_number'],
			'price_project' => $data['total_material'],
			
			'subject' 				=> strtolower($data['subject']),
			'product' 				=> strtolower($data['product']),
			'pengiriman' 			=> strtolower($data['pengiriman']),
			'sales' 				=> strtolower($data['sales']),
			'jangka_waktu_penawaran'=> strtolower($data['jangka_waktu_penawaran']),
			'garansi_porduct' 		=> strtolower($data['garansi_porduct']),
			'tahap_pembayaran' 		=> strtolower($data['tahap_pembayaran']),
			
			'created_by' 	=> $data_session['ORI_User']['username'],
			'created_date' 	=> date('Y-m-d H:i:s')
		);
		
		// $ArrMatCost = array();
		// foreach($MatCost AS $val => $valx){
			// $ArrMatCost[$val]['id_bq']			= $data['id_bq'];
			// $ArrMatCost[$val]['category']		= $valx['category'];
			// $ArrMatCost[$val]['caregory_sub']	= $valx['id_milik'];
			// $ArrMatCost[$val]['nego']			= $valx['nego'];
			// $ArrMatCost[$val]['price']			= $valx['harga'];
			// $ArrMatCost[$val]['price_total']	= $valx['harga_total'];
		// }
		
		// print_r($ArrMatCost);
		// exit;
		
		$this->db->trans_start();
			$this->db->delete('cost_project_header_sales', array('id_bq' => $data['id_bq']));  
			// $this->db->delete('cost_project_detail_sales', array('id_bq' => $data['id_bq']));  
			
			$this->db->insert('cost_project_header_sales', $ArrHeader);
			// $this->db->insert_batch('cost_project_detail_sales', $ArrMatCost);
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process data failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process data success. Thanks ...',
				'status'	=> 1
			);				
			history('Cost Quotation Sales with bq : '.$data['id_bq']);
		}
		echo json_encode($Arr_Data);
	}
	
	public function print_cetak(){
		$sroot 		= $_SERVER['DOCUMENT_ROOT'];
		include $sroot."/application/libraries/MPDF57/mpdf.php";
	  
		$data_session	= $this->session->userdata;
		$id_bq   		= $this->uri->segment(3);
		
		$mpdf= new mPDF('utf-8','A4');
		$mpdf->SetImportUse();
		
		$get_header = $this->db->get_where('cost_project_header_sales', array('id_bq'=>$id_bq))->result();
		
		$data = array(
			'header'	=> $get_header
		);
        
		
		$header 	= $this->load->view('Print/print_quo_cetak_header', TRUE);
        $body 		= $this->load->view('Print/print_quo_cetak_body', $data, TRUE);
		$footer 	= "<img src='".$sroot."/assets/images/ori_logo.jpg' alt='' height='80' width='70'>"; 

	
        $mpdf->SetHeader($header);
		$mpdf->SetFooter($footer);
	    
        $mpdf->AddPageByArray([
			'orientation' => 'P',
			'margin-top' => 20,
			'margin-bottom' => 15,
			'margin-left' => 10,
			'margin-right' => 10,
			'margin-header' => 10,
			'margin-footer' => 10,
		]);
			
		$mpdf->SetTitle($id_bq);
        $mpdf->WriteHTML($body);
        $mpdf->Output($id_bq." ".date('dmYHis').".pdf" ,'I');
	}
	
}
