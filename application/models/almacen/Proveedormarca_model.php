<?php
class Proveedormarca_Model extends CI_Model
{
    protected $_name = "cji_proveedormarca";
    public function  __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    public function seleccionar()
    {
        $arreglo = array('0'=>':: Seleccione ::');
        if(count($this->listar())>0){
            foreach($this->listar() as $indice=>$valor)
            {
                $indice1   = $valor->MARCP_Codigo;
                $valor1    = $valor->MARCC_Descripcion;
                $arreglo[$indice1] = $valor1;
            }
        }
        return $arreglo;
    }
     public function listar($number_items='',$offset='')
     {
        $where = array("MARCC_FlagEstado"=>1,"MARCP_Codigo !="=>0);
        $query = $this->db->order_by('MARCC_Descripcion')->where($where)->get($this->_name,$number_items,$offset);
        if($query->num_rows() > 0){
            return $query->result();
        }
     }	 
     public function obtener($id)
     {
        $where = array("MARCP_Codigo"=>$id);
        $query = $this->db->order_by('MARCC_Descripcion')->where($where)->get($this->_name,1);
        if($query->num_rows() > 0){
          return $query->result();
        }
     }
    public function insertar(stdClass $filter = null)
    {
        $this->db->insert($this->_name,(array)$filter);
        $id = $this->db->insert_id();
        return $id;
    }
    public function modificar($id,$filter)
    {
        $this->db->where("MARCP_Codigo",$id);
        $this->db->update($this->_name,(array)$filter);
    }
    public function eliminar($id)
    {
        $this->db->delete($this->_name, array('MARCP_Codigo' => $id));
    }
    public function buscar($filter,$number_items='',$offset='')
    {
        $where = array("MARCC_FlagEstado"=>1,"MARCP_Codigo !="=>0);
        $this->db->where($where);
        if(isset($filter->MARCC_Descripcion) && $filter->MARCC_Descripcion!='')
            $this->db->like('MARCC_Descripcion',$filter->MARCC_Descripcion,'right');
        $query = $this->db->get($this->_name,$number_items,$offset);
        if($query->num_rows() > 0){
                return $query->result();
        }
    }
    public function buscar_por_nombre($filter)
    {
        $where = array("MARCC_FlagEstado"=>1,"MARCP_Codigo !="=>0);
        $this->db->where($where);
        if(isset($filter->MARCC_Descripcion) && $filter->MARCC_Descripcion!='')
            $this->db->where('MARCC_Descripcion',$filter->MARCC_Descripcion);
        $query = $this->db->get($this->_name,$number_items,$offset);
        if($query->num_rows() > 0){
                return $query->result();
        }
    }
}
?>