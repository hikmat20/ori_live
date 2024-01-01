<?php
class Portal_model extends CI_Model {

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

}
?>