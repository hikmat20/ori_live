<?php
class Setting_model extends CI_Model {

    public $nm_perusahaan;
    public $nm_poduct;

    // public function get_identitas()
    // {
        // $query = $this->db->limit(1)->get_where('company', array('id'=>'2'));
        // return $query->result();
    // }

    public function get_portal()
    {
        $query = $this->db->order_by('sort','asc')->get_where('menu_portal', array('active'=>'1'));
        return $query->result_array();
    }

    public function update_portal($ArrUpdate, $id)
    {
        $this->db->trans_start();
            $this->db->where('id', $id);
            $this->db->update('menu_portal', $ArrUpdate);
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

    public function get_edit_portal($id)
    {
        $query = $this->db->get_where('menu_portal', array('id'=>$id))->result();
        $Arr_Data	= array(
			'status'	=> 1,
            'id'	=> $query[0]->id,
            'name'	=> $query[0]->name,
            'link'	=> $query[0]->link,
            'desc'	=> $query[0]->desc,
            'sort'	=> $query[0]->sort
        );
        echo json_encode($Arr_Data);
    }

}
?>