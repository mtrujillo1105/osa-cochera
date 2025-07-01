<?php
class Tipomovimiento_model extends CI_Model
{
    var $somevar;
    public function __construct(){
        parent::__construct();
        $this->load->database();
        $this->load->helper('date');
        $this->somevar['compania'] = $this->session->userdata('compania');
        $this->somevar['hoy']      = mdate("%Y-%m-%d %h:%i:%s",time());
    }
    public function seleccionar(stdClass $filter = null)
    {
        if(isset($filter->TIPOMOVC_Tipo) && $filter->TIPOMOVC_Tipo>0)   $tipo_mov = $filter->TIPOMOVC_Tipo;
        $arreglo = array(''=>':: Seleccione ::');
        foreach($this->listar() as $indice=>$valor)
        {
            $indice1   = $valor->TIPOMOVP_Codigo;
            $valor1    = $valor->TIPOMOVC_Descripcion;
            $tipo      = $valor->TIPOMOVC_Tipo;
            if(isset($tipo_mov)){
                if($tipo==$tipo_mov) $arreglo[$indice1] = $valor1;
            }
            else{
                $arreglo[$indice1] = $valor1;
            }
        }
        return $arreglo;
    }
    public function listar($number_items='',$offset='')
    {
        $where = array('TIPOMOVC_FlagEstado'=>'1');        
        $query = $this->db->order_by('TIPOMOVP_Codigo')->where($where)->get('cji_tipomovimiento',$number_items,$offset);
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }
    public function obtener($tipo)
    {
        $query = $this->db->where('TIPOMOVP_Codigo',$tipo)->get('cji_tipomovimiento');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
              $data[] = $fila;
            }
            return $data;
        }
    }
    public function insertar(stdClass $filter = null)
    {
        $this->db->insert("cji_tipomovimiento",(array)$filter);
    }
    public function modificar($id,$filter){
        $this->db->where("TIPOMOVP_Codigo",$id);
        $this->db->update("cji_tipomovimiento",(array)$filter);
    }
    public function eliminar($tipo){
        $this->db->delete('cji_tipomovimiento',array('TIPOMOVP_Codigo' => $id));
    }
    public function buscar($filter,$number_items='',$offset='')
    {
        $this->db->where("TIPOMOVC_FlagEstado",1);
        if(isset($filter->TIPOMOVC_Descripcion) && $filter->TIPOMOVC_Descripcion!='')
            $this->db->like('TIPOMOVC_Descripcion',$filter->TIPOMOVC_Descripcion,'right');
        $query = $this->db->get('cji_tipomovimiento',$number_items,$offset);
        if($query->num_rows() > 0){
            return $query->result();
        }
    }
}
?>