<?php
class Estadocivil_model extends CI_Model{

	public function __construct(){
		parent::__construct();
        $this->load->helper('date');
	}

	public function listar_estadoCivil(){
		$query = $this->db->order_by('ESTCC_Descripcion')->where_not_in('ESTCP_Codigo','0')->where('ESTCC_FlagEstado','1')->get('cji_estadocivil');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;		
		}		
	}

	public function obtener_estadoCivil($codigo){
		$query = $this->db->where('ESTCP_Codigo',$codigo)->get('cji_estadocivil');
		if($query->num_rows() > 0){
			foreach($query->result() as $fila){
				$data[] = $fila;
			}
			return $data;		
		}		
	}
}
?>