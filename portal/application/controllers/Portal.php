<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portal extends CI_Controller {

	public function __construct(){
        parent::__construct();
        $this->load->model('portal_model');
    }

	public function index()
	{
		$data = array(
			'link' => base_url('assets/adminlte3'),
			// 'company' => $this->portal_model->get_identitas(),
			'portal' => $this->portal_model->get_portal()
		);
		$this->load->view('portal', $data);
	}
}
