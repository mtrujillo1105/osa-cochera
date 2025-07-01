<?php
class Moneda_model extends CI_Model{

    private $compania;

    public function __construct(){
        parent::__construct();
        $this->compania = $this->session->userdata('compania');
    }

    public function getMonedas(){
        $sql = "SELECT * FROM cji_moneda m WHERE m.MONED_FlagEstado LIKE '1'";
        $query = $this->db->query($sql);
        
        if ( $query->num_rows() > 0 )
            return $query->result();
        else
            return NULL;
    }

    public function seleccionar(){
        $arreglo = array(''=>':: Seleccione ::');
        foreach($this->listar() as $indice=>$valor)
        {
            $indice1   = $valor->MONED_Codigo;
            $valor1    = $valor->MONED_Descripcion;
            $arreglo[$indice1] = $valor1;
        }
        return $arreglo;
    }

    public function listar(){
        $compania = $this->compania;
        $where = array("MONED_FlagEstado"=>"1");  //saque esta condicional "COMPP_Codigo"=>$compania
        $query = $this->db->order_by('MONED_Orden')->where('MONED_FlagEstado','1')->get('cji_moneda');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function listartipomoneda(){
    	$sql="select moned_codigo,moned_descripcion from cji_moneda";
    	
    	$query = $this->db->query($sql);
    	if($query->num_rows() > 0){
    		foreach ($query->result() as $fila){
    			$data[] = $fila;
    		}
    		return $data;
    	}
    }

    public function getById($id){
        $data = $this->db->where('MONED_Codigo',$id)->get('cji_moneda')->result();

        return $data[0];
    }

    public function getById2($id){
        $query = $this->db->where('MONED_Codigo',$id)->get('cji_moneda');

        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function obtener($moneda){
        $query = $this->db->where('MONED_Codigo',$moneda)->get('cji_moneda');
        if($query->num_rows() > 0){
            foreach($query->result() as $fila){
                $data[] = $fila;
            }
            return $data;
        }
    }

    public function insertar(stdClass $filter = null){
        $this->db->insert("cji_moneda",(array)$filter);
    }

    public function modificar($id,$filter){
        $this->db->where("MONED_Codigo",$id);
        $this->db->update("cji_moneda",(array)$filter);
    }

    public function eliminar($id){
        $this->db->delete('cji_moneda',array('MONED_Codigo' => $id));
    }

    public function buscar($filter,$number_items='',$offset=''){

    }
   
}
?>