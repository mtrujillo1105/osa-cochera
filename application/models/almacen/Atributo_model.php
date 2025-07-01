<?php
class Atributo_model extends CI_Model{
    var $somevar;
	function __construct(){
		parent::__construct();
		$this->load->database();
        $this->somevar ['compania'] = $this->session->userdata('compania');
        $this->somevar['hoy']              = date("%Y-%m-%d %h:%i:%s",time());
	}
	function listar_atributos(){
		
                $query = $this->db->order_by('ATRIB_Descripcion')->where('ATRIB_FlagEstado','1')->get('cji_atributo');
                
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;
		}
                 else {
                        return array();
                }
	}
	function obtener_atributo($atributo){
		$query = $this->db->where("ATRIB_Codigo",$atributo)->get("cji_atributo");
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;		
		}	
	}
	function insertar_atributo($tipo,$descripcion){
		$data = array(
					  "ATRIB_TipoAtributo" => $tipo,
					  "ATRIB_Descripcion"  => strtoupper($descripcion)
					 );
		$this->db->insert("cji_atributo",$data);
	}
	function modificar_atributo($atributo,$descripcion){
		$data = array("ATRIB_Descripcion" => strtoupper($descripcion));
		$this->db->where('ATRIB_Codigo',$atributo);
		$this->db->update("cji_atributo",$data);
	}
	function eliminar_atributo($atributo){
		$data  = array("ATRIB_FlagEstado" => '0');
		$where = array("ATRIB_Codigo"     => $atributo); 
		$this->db->where($where);
		$this->db->update('cji_atributo',$data);	
	}
        public function buscar_atributo($filter,$number_items='',$offset='')
	{
            if(isset($filter->nombre_atributo) && $filter->nombre_atributo!='')
                $this->db->like('ATRIB_Descripcion',$filter->nombre_atributo,'both');
            $query = $this->db->where('ATRIB_FlagBienServicio',$filter->flagBS)->get('cji_atributo',$number_items,$offset);
            if($query->num_rows() > 0){
                foreach($query->result() as $fila){
                        $data[] = $fila;
                }
                return $data;
            }
	}
}
?>