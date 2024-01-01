<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Setting extends CI_Controller {

	public function __construct(){
        parent::__construct();
        $this->load->model('setting_model');
    }

	public function index()
	{
		$data = array(
			'link' => base_url('assets/adminlte3'),
			'link_g' => base_url('assets'),
			// 'company' => $this->setting_model->get_identitas(),
			'portal' => $this->setting_model->get_portal()
		);
		$this->load->view('setting/index', $data);
	}

	public function delete($id=null)
	{
		$ArrUpdate = array(
            'active' => '0'
        );
		$this->setting_model->update_portal($ArrUpdate, $id);
	}

	public function edit($id=null)
	{
		$this->setting_model->get_edit_portal($id);
	}

	public function save()
	{
		$data 	= $this->input->post();
		$id 	= $data['id'];
		$ArrUpdate = array(
            'name' => strtolower($data['name']),
			'link' => $data['link'],
			'desc' => strtolower($data['desc']),
			'sort' => $data['sort'],
			'button_title' => 'to link'
        );
		
		$this->db->trans_start();
			if(!empty($id)){
				$this->db->where('id', $id);
				$this->db->update('menu_portal', $ArrUpdate);
			}
			if(empty($id)){
				$this->db->insert('menu_portal', $ArrUpdate);
			}
		$this->db->trans_complete();
		
		if($this->db->trans_status() === FALSE){
			$this->db->trans_rollback();
			$Arr_Data	= array(
				'pesan'		=>'Process failed. Please try again later ...',
				'status'	=> 0
			);			
		}
		else{
			$this->db->trans_commit();
			$Arr_Data	= array(
				'pesan'		=>'Process success. Thanks ...',
				'status'	=> 1
			);
		}
		echo json_encode($Arr_Data);
	}
}
