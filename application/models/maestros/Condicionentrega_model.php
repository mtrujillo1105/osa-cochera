<?php
class Condicionentrega_model extends CI_Model{
    var $somevar;
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar ['usuario']  = $this->session->userdata('usuario');
        $this->somevar['hoy']       = mdate("%Y-%m-%d %h:%i:%s",time());
    }
    public function seleccionar()
    {
        $arreglo = array(''=>':: Seleccione ::');
        foreach($this->listar() as $indice=>$valor)
        {
            $indice1   = $valor->CONENP_Codigo;
            $valor1    = $valor->CONENC_Descripcion;
            $arreglo[$indice1] = $valor1;
        }
        return $arreglo;
    }
    public function listar($number_items='',$offset='')
    {
        $query = $this->db->order_by('CONENC_Descripcion')->where('CONENC_FlagEstado','1')->get('cji_condicionentrega',$number_items='',$offset='');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function obtener($id)
    {
        $where = array("CONENP_Codigo"=>$id);
        $query = $this->db->order_by('CONENC_Descripcion')->where($where)->get('cji_condicionentrega',1);
        if($query->num_rows() > 0){
          return $query->result();
        }
    }
    public function insertar(stdClass $filter = null)
    {
        $this->db->insert("cji_condicionentrega",(array)$filter);
    }
    public function modificar($id,$filter)
    {
        $this->db->where("CONENP_Codigo",$id);
        $this->db->update("cji_condicionentrega",(array)$filter);
    }
    public function eliminar($id)
    {
        $this->db->delete('cji_condicionentrega',array('CONENP_Codigo' => $id));
    }
    public function buscar($filter,$number_items='',$offset='')
    {
        $this->db->select('*');
        $this->db->from('cji_condicionentrega',$number_items='',$offset='');
        $this->db->where('cji_condicionentrega.CONENC_Descripcion',$filter->CONENC_Descripcion);
        $query = $this->db->get();
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                    $data[] = $fila;
            }
            return $data;
        }
    }
}
?>