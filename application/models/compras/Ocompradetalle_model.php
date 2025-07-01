<?php
class Ocompradetalle_model extends CI_Model{
    var $somevar;
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['user']  = $this->session->userdata('user');
        $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }
    public function insertar($filter=null)
    {
        $this->db->insert('cji_ocompradetalle',(array)$filter);
    }
    public function modificar($id,$filter=null)
    {   $where = array("OCOMDEP_Codigo"=>$id);
        $this->db->where($where);
        $this->db->update('cji_ocompradetalle',(array)$filter);
    }
    public function eliminar($id)
    {   $data      = array("OCOMDEC_FlagEstado"=>'0');
        $where = array("OCOMDEP_Codigo"=>$id);
        $this->db->where($where);
        $this->db->update('cji_ocompradetalle',$data);
    }

    public function listar_productos_by_id_orden($id_orden)
    {
        return $this->db->from("cji_ocompradetalle")
                        ->where("OCOMP_Codigo", $id_orden)   
                        ->where("OCOMDEC_FlagEstado", 1)
                        ->get()->result();
    }
    
}
?>