<?php
class Presupuestodetalle_model extends CI_Model{
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
    public function listar($presupuesto)
    {
        $where = array("PRESUP_Codigo"=>$presupuesto,"PRESDEC_FlagEstado"=>"1");
        $query = $this->db->order_by('PRESDEP_Codigo')->where($where)->get('cji_presupuestodetalle');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function insertar($filter=null)
    {
        $this->db->insert('cji_presupuestodetalle',(array)$filter);
    }
    public function insertar_varios($filter=null,$cod_presupuesto)
    {
        $filter->PRESUP_Codigo = $cod_presupuesto;
        $this->db->insert('cji_presupuestodetalle',(array)$filter);
    }
     public function modificar($presupuesto_detalle,$filter=null)
    {
        $where = array("PRESDEP_Codigo"=>$presupuesto_detalle);
        $this->db->where($where);
        $this->db->update('cji_presupuestodetalle',(array)$filter);
    }
    public function eliminar($presupuesto_detalle)
    {
        $data      = array("PRESDEC_FlagEstado"=>'0');
        $where = array("PRESDEP_Codigo"=>$presupuesto_detalle);
        $this->db->where($where);
        $this->db->update('cji_presupuestodetalle',$data);
    }
    
}
?>