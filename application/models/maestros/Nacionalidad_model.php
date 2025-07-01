<?php
class Nacionalidad_model extends CI_Model{

	public function __construct(){
		parent::__construct();
        $this->load->helper('date');
	}

	public function listar_nacionalidad(){
		$query = $this->db->order_by('NACC_Descripcion')->where('NACC_FlagEstado','1')->get('cji_nacionalidad');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;		
		}	
	}

	public function obtener_nacionalidad($codigo){
		$query = $this->db->where('NACP_Codigo',$codigo)->get('cji_nacionalidad');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;		
		}		
	}
}
?>