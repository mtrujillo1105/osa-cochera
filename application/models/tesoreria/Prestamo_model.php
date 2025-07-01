<?php
class Prestamo_Model extends CI_Model
{
    protected $_name = "cji_prestamo";
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
                $indice1   = $valor->LINP_Codigo;
                $valor1    = $valor->LINC_Descripcion;
                $arreglo[$indice1] = $valor1;
            }
        }
        return $arreglo;
    }
     public function listar($number_items='',$offset='')
     {
        $where = array("p.PRES_FlagEstado"=>1,"p.PRES_Codigo !="=>0);
        $query = $this->db->order_by('p.PRES_Codigo','DESC')
                          ->join('cji_moneda m', 'm.MONED_Codigo = p.MONED_Codigo', 'inner')
                          ->where($where)
                          ->get('cji_prestamo p' ,$number_items,$offset);
        if($query->num_rows() > 0){
                return $query->result();
        }
     }  

      public function listar2($number_items='',$offset=''){
        
        $sql ="SELECT * FROM cji_prestamo p 
INNER JOIN cji_moneda m ON m.MONED_Codigo = p.MONED_Codigo
WHERE p.PRES_FlagEstado = 1 AND p.PRES_Codigo != 0 ORDER BY p.PRES_Codigo DESC ";

        $query = $this->db->query($sql);
        if($query->num_rows() >0){
            foreach ($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
        
    }

     public function obtener($id)
     {
        $where = array("PRES_Codigo"=>$id);
        $query = $this->db->order_by('PRES_Codigo','DESC')->where($where)->get($this->_name,1);
        if($query->num_rows() > 0){
          return $query->result();
        }
     }
    public function insertar(stdClass $filter = null)
    {
        $this->db->insert($this->_name,(array)$filter);
    }
    public function modificar($id,$filter)
    {
        $this->db->where("PRES_Codigo",$id);
        $this->db->update($this->_name,(array)$filter);
    }
    public function eliminar($id)
    {
            $data  = array("PRES_FlagEstado"=>'0');
            $where = array("PRES_Codigo"=>$id);
            $this->db->where($where);
            $this->db->update('cji_prestamo',$data);
    }
    public function buscar($filter,$number_items='',$offset='')
    {
        $where = array("LINC_FlagEstado"=>1,"LINP_Codigo !="=>0);
        $this->db->where($where);
        if(isset($filter->LINC_Descripcion) && $filter->LINC_Descripcion!='')
            $this->db->like('LINC_Descripcion',$filter->LINC_Descripcion,'right');
        $query = $this->db->get($this->_name,$number_items,$offset);
        if($query->num_rows() > 0){
                return $query->result();
        }
    }
}
?>