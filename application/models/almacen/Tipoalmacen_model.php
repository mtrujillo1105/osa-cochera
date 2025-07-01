<?php
class Tipoalmacen_model extends CI_Model{

    var $somevar;
    
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->somevar['compania'] = $this->session->userdata('compania');
        $this->somevar['hoy']      = mdate("%Y-%m-%d %h:%i:%s",time());
    }
    
    public function seleccionar(){
        $arreglo = array(''=>':: Seleccione ::');
        foreach($this->listar() as $indice=>$valor)
        {
            $indice1   = $valor->TIPALMP_Codigo;
            $valor1    = $valor->TIPALM_Descripcion;
            $arreglo[$indice1] = $valor1;
        }
        return $arreglo;
    }

    public function listar(){
        $query = $this->db->order_by('TIPALM_Descripcion')->where('TIPALM_flagEstado','1')->get('cji_tipoalmacen');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener($tipo)
    {
        $query = $this->db->where('TIPALMP_Codigo',$tipo)->get('cji_tipoalmacen');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
              $data[] = $fila;
            }
            return $data;
        }
    }
    
    public function insertar($descripcion)
    {
        $data = array("TIPALM_Descripcion" => strtoupper($descripcion));
        $this->db->insert("cji_tipoalmacen",$data);
    }
    public function modificar($tipo,$descripcion){
        $data = array("TIPALM_Descripcion" => strtoupper($descripcion));
        $this->db->where('TIPALM_Codigo',$tipo);
        $this->db->update("cji_tipoalmacen",$data);
    }
    public function eliminar($tipo){
            $data  = array("TIPALM_flagEstado" => '0');
            $where = array("TIPALM_Codigo"     => $tipo);
            $this->db->where($where);
            $this->db->update('cji_tipoalmacen',$data);
    }
    public function buscar($filter,$number_items='',$offset='')
    {
        $this->db->where("TIPALM_flagEstado",1);
        if(isset($filter->TIPALM_Descripcion) && $filter->TIPALM_Descripcion!='')
            $this->db->like('TIPALM_Descripcion',$filter->TIPALM_Descripcion,'right');
        $query = $this->db->get('cji_tipoalmacen',$number_items,$offset);
        if($query->num_rows() > 0){
                return $query->result();
        }
    }
}
?>